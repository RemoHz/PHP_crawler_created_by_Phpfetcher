<?php
// Set Path
$demo_include_path = dirname(__FILE__) . '/../';
set_include_path(get_include_path() . PATH_SEPARATOR . $demo_include_path);

require_once('phpfetcher.php');
class mycrawler extends Phpfetcher_Crawler_Default {
    public function handlePage($page) {

        $fp = fopen('unitsByCode.csv', 'a');

        // Get li
        $res = $page->sel('//li');
        for ($i = 0; $i < count($res); ++$i)
        {
            $name = "";
            $name = $res[$i]->plaintext;

            if ($name == "")
                continue;

            $objLi = $res[$i]->find("a");

            for ($j = 0; $j < count($objLi); ++$j)
                $url = $objLi[$j]->getAttribute('href');

            $compare = substr_compare($name, $url, 0, 7);

            if ($compare == 0)
            {
                echo $name."\n";
                echo "http://www.monash.edu.au/pubs/2016handbooks/units/".$url."\n\n";

                $unit = array($name, 'http://www.monash.edu.au/pubs/2016handbooks/units/'.$url);

                fputcsv($fp, $unit);
            }
        }

        fclose($fp);
    }
}

$code = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'v'];

for ($index = 0; $index < count($code); ++$index)
{
    $unit_url = 'http://www.monash.edu.au/pubs/2016handbooks/units/index-bycode-'.$code[$index].'.html';

    $crawler = new mycrawler();
    $arrJobs = array(
        'units' => array(
            'start_page' => $unit_url,
            'link_rules' => array(

            ),
            'max_depth' => 1,

        ) ,
    );

    $crawler->setFetchJobs($arrJobs);
    $crawler->run();
}


