<?php
// Set path
$demo_include_path = dirname(__FILE__) . '/../';
set_include_path(get_include_path() . PATH_SEPARATOR . $demo_include_path);

require_once('phpfetcher.php');
class mycrawler extends Phpfetcher_Crawler_Default {
    public function handlePage($page) {

        $fp = fopen('unitsByFaculty.csv', 'a');

        // Get title
        $title = $page->sel('//title');
        for ($i = 0; $i < count($title); ++$i)
        {
            $result = $title[$i]->plaintext."\n";
        }

        $str = explode('-', $result);
        $faculty = trim($str[0]);

        // Get li
        $res = $page->sel('//li');
        for ($i = 0; $i < count($res); ++$i)
        {
            $name = "";
            $name = $res[$i]->plaintext;

            if ($name == "")
                continue;

            $objLi = $res[$i]->find("a");
            for ($j=0; $j < count($objLi); ++$j)
            {
                $url = $objLi[$j]->getAttribute('href');
            }

            $compare = substr_compare($name, $url, 0, 7);

            $unit_code = substr($name, 0, 7);
            $unit_name = substr($name, 8);
            if ($compare == 0)
            {
                echo $faculty."\n";
                echo $unit_code."\n";
                echo $unit_name."\n";
                echo $url."\n\n";

                $unit = array($faculty, $unit_code, $unit_name, 'http://www.monash.edu.au/pubs/2016handbooks/units/'.$url);

                fputcsv($fp, $unit);
            }
        }
        fclose($fp);
    }
}

$code = ['ada', 'arts', 'bus', 'edu', 'eng', 'it', 'law', 'med', 'pha', 'sci'];

for ($index = 0; $index < count($code); ++$index)
{
    $unit_url = 'http://www.monash.edu.au/pubs/2016handbooks/units/index-byfaculty-'.$code[$index].'.html';

    $crawler = new mycrawler();
    $arrJobs = array(
        'units' => array(
            'start_page' => $unit_url, //URL
            'link_rules' => array(

            ),

            'max_depth' => 1,

        ) ,
    );

    $crawler->setFetchJobs($arrJobs);
    $crawler->run();
}






