<?php

function tidy_html($input_string) {

    $config = array('output-html' => true, 'indent' => true, 'wrap' => 800);

    // Detect if Tidy is in configured    
    if (function_exists('tidy_get_release')) {
        $tidy = new tidy;
        $tidy->parseString($input_string, $config, 'raw');
        $tidy->cleanRepair();
        $cleaned_html = tidy_get_output($tidy);
    } else {
        # Tidy not configured for this Server
        $cleaned_html = $input_string;
    }
    return $cleaned_html;
}

$webAddresses = array("http://www.amazon.in/s/ref=sr_pg_3?rh=n%3A2454169031%2Ck%3Abags&page=3&bbn=2454169031&keywords=bags&ie=UTF8&qid=1500353651&spIA=B071X8C42B,B01N5I0SCV,B073FNK8SC");
$des_path = '//div[@class="a-row a-spacing-mini"]'	;
$rating_path = '//span[@class="a-icon-alt"]'	;
$price_path = '//span[@class="a-size-base a-color-price s-price a-text-bold"]'	;
function getFromPage($webAddresses, $des_path,$rating_path,$price_path)
{
    $con = mysqli_connect("localhost", "root", "", "web_scrape");
    if (!$con)
    {
        echo "Connection error";
    } else 
    
    {
        echo"your data is stored";
    }

    mysqli_select_db($con, "web_scrape");

    foreach ($webAddresses as $webAddress)
    {
        $source = file_get_contents($webAddress); //download the page 
        //echo $webAddress->xyz. "<hr></br>";


        $clean_source = tidy_html($source);
        $doc = new DOMDocument;
        // suppress errors
        libxml_use_internal_errors(true);
        // load the html source from a string
        $doc->loadHTML($clean_source);
        $xpath = new DOMXPath($doc);
        //   $data = "";

        $Nodelist = $xpath->query($des_path);
        $Nodelist1=$xpath->query($rating_path);
        $Nodelist2=$xpath->query($price_path);
        foreach ($Nodelist as $key)
        {
            
            echo $key->nodeValue. "<hr></br>";
           $result = mysqli_query($con, "INSERT INTO amazon(description)
            VALUES ( '$key->nodeValue' )");
            print_r($result);

        }
            foreach ($Nodelist1 as $key1)
        {
            
            echo $key1->nodeValue. "<hr></br>";
           $result = mysqli_query($con, "INSERT INTO amazon(rating)
            VALUES( '$key1->nodeValue' )");
            print_r($result);
        }
        
            foreach ($Nodelist2 as $key2)
        {
            
            echo $key2->nodeValue. "<hr></br>" ;
             $result = mysqli_query($con, "INSERT INTO amazon(price)
            VALUES ( '$key2->nodeValue' )");
            print_r($result);
          
       
        }
        
         
        
        
    }
    //mysqli_query($con, "SELECT * FROM dom");

    mysqli_close($con);

}
echo getFromPage($webAddresses,$des_path,$rating_path,$price_path);
echo "</br>";

?>
