<?php
// https://www.sitepoint.com/extract-an-exceprt-from-a-wav-file/
require_once __DIR__."/vendor/autoload.php";

use Audero\WavExtractor\AuderoWavExtractor;
use Phpml\Clustering\KMeans;

#ar_dump(class_exists('Audero'));
#ar_dump(class_exists('Audero\WavExtractor'));
#ar_dump(get_declared_classes());

function energy($f)
{
$n = count($f);
$e = 0;
foreach($f as $v)
    $e += sqrt(($v*$v)/$n); //получаем мощность ... 
    
return $e;
}

$inputFile = "./samples/aa_converted.wav";

# $a = new AuderoWavExtractor($inputFile);
#xit();
$inputFile = "./samples/aa_converted.wav";
$outputFile = "./excerpt.wav";
$start = 0 * 1000; // from 0 seconds
$end = 2 * 1000;  // to 2 seconds

//f - frame
//fr - frame result as properties

try {
    $ext = new AuderoWavExtractor($inputFile);
    $wav = $ext->getChunk($start, $end); //берем кусок данных из файла
//    $ext->saveChunk($start, $end, $outputFile);
}
catch (Exception $e) {
    echo "An error has occurred: " . $e->getMessage();
    exit();
}

    if(!($data = unpack('s*', $wav))) 
	{ print "read data error"; 
	 exit();
	} //тип определяется на осное размерности данных
    //разбиение на фреймы
    $length = 256; //длина фрейма  //кусок данных дополниться нулями до фрейма нужной длины. - по хорошему перейти к временной шкале... 
    $f = array_chunk($data, 256);
    $index = count($f) - 1;
    $f[$index] = array_pad($f[$index], $length, 0);
    for($i = 0; $i < count($f); $i++)
	$e[$i] = array(energy($f[$i]));
    $k = new KMeans(3);
    
    $res = $k->cluster($e);
    foreach($res as $clu => $arr)
	foreach($arr as $index => $e)
	{
	$fr[$index]["energyCluster"] = $clu;
	$fr[$index]["energy"] = $e;
	}
	
    for($i = 0; $i < count($f); $i++)
	{
	print $fr[$i]["energyCluster"];
	}
    print_r($res);
    
    //тут можно включать кластеризацию
    //
    //
    

    