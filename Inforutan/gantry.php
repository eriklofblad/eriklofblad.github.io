<?php

require_once __DIR__ . "/vendor/autoload.php";

$time =  microtime(true);

$dom = new PHPHtmlParser\Dom;
$dom->loadFromUrl('http://gantry.episerverhosting.com/login.aspx?ReturnUrl=%2fmetoder&username=huddo&password=huddo2013');

echo "Loading time= " . (microtime(true) - $time) . "<br>";

$time =  microtime(true);

$methods = $dom->find('form.jump-to div.row div.checkbox');

foreach($methods as $method){
    $methodbase = $method->find('a');
    $methodname = $methodbase->innerHtml;
    $methodlink = $methodbase->getAttribute('href');
    $methodbase->setAttribute('href', "http://gantry.episerverhosting.com". $methodlink);
    $methodbase->setAttribute('target','_blank');

    echo $methodbase . "<br>";
}

echo "Parsing time= " . (microtime(true) - $time) . "<br>" ;


?>