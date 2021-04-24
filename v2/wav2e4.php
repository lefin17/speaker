<?php
// https://www.sitepoint.com/extract-an-exceprt-from-a-wav-file/
require_once __DIR__."/vendor/autoload.php";

use Audero\WavExtractor\AuderoWavExtractor;
use Phpml\Clustering\KMeans;
// https://inside-out.xyz/technology/n-order-linear-equations-curve-fit-with-least-squares-and-php.html
// added MNK as filer...

include('tools/haar_wavelet.php');
require_once('tools/image_from_haar_hsl.php'); 

function energy($f)
{
$n = count($f);
$e = 0;
foreach($f as $v)
    $e += sqrt(($v*$v)/$n); //получаем мощность ... 
    
return $e;
}

# $inputFile = "./samples/aa_converted.wav";

# $a = new AuderoWavExtractor($inputFile);
#xit();
$inputFile = "./samples/aa-c-5600-7264.wav";
$inputFile = "./samples/s2.wav";
$outputFile = "./res01/aa-e"; //шаблон для сохранения
$startMax = 0 * 1000; // from 0 seconds
$endMax = 1.61 * 1000;  // to 2 seconds

//f - frame
//fr - frame result as properties

try {
    $ext = new AuderoWavExtractor($inputFile);
    $wav = $ext->getChunk($startMax, $endMax); //берем кусок данных из файла
    $w = $ext->getWav();
    $d = $ext->getWav()->getDuration(); 
    print "Duration: $d\n";
    $sampleRate = $w->getSampleRate();
}
catch (Exception $error) {
    echo "An error has occurred: " . $error->getMessage();
    exit();
}

if(!($data = unpack('s*', $wav))) 
	{ print "read data error"; 
	 exit();
	} //тип определяется на осное размерности данных
    //разбиение на фреймы
    
    
    $length = 256; //длина фрейма  

		   //кусок данных дополниться нулями до фрейма нужной длины. - по хорошему перейти к временной шкалы

for($dev = 0; $dev < 4; $dev++)
    {
    $e = null; 
    $f = null;
    $res = null;  
    
    $frame_length = round($length / pow(2, $dev));
    $f = array_chunk($data, $frame_length); //поделили все данные на массив
    $index = count($f) - 1;
    $f[$index] = array_pad($f[$index], $frame_length, 0);
    for($i = 0; $i < count($f); $i++)
	$e[$i] = array(energy($f[$i]));
//haar wavelet 

    foreach($f as $index => $frame)
	{
	$res[$index] = Haar($frame, 8 - $dev);
// 	$res[$index]["power"] = HaarPower($res[$index]);
	$en[$index] = HaarPower($res[$index]);
	}
//	print_r($res); 
//	die(); 
	if ($dev == 0) $minEn = HaarMinPower($en);  //расчет при максимальном окне
	$maxEn = HaarMaxPower($en);
	$hn = HaarLineNormalize($maxEn, $minEn);
	
	
//	print "\nEn.min: "; print_r($minEn); 
//	print "\nEn.max: "; print_r($maxEn);
	print "\nEn.normal: "; print_r($hn);
	
	$hnAll[$dev] = HaarNormalize($en, $minEn);
//	print_r($img);
	$offset[$dev] = floor(360 - max($hn));
	print "Offset [".$dev."]: ".$offset[$dev]."\n"; 
//	continue;
    }
    
    $img = null; //контейнер изображения    
    $off = min($offset);
    foreach($offset as $dev => $value)
	{
    	$img = createImageFromHaarArray($img, $hnAll[$dev], "./test_s2_hn-".$dev.".jpg", $off, [(int) 32 / pow(2, $dev),  16]); //create image with track... 
	$fn = fopen("res-".$dev.".log", "w");
	fwrite($fn, json_encode($hnAll[$dev]));
	fclose($fn);
	}
    imagedestroy($img);//освобождаем изображение 
    die();
//	die("complite image?"); 
	print "\nEn.nA: "; print_r($hnAll);
//	die("min Power");
    foreach($f as $index => $frame)
	foreach($en[$index] as $order => $value)
	    {
	    print $index;
	    $en2[$order][$index] = array($value);
	    }
	//    exit();
//    print_r($en2);
//    exit();
    
    foreach($en2 as $order => $arr)
	{
	   $k = new KMeans(4); //возможно снизить до трех множеств, или поднять до 5...  и тогда 2 и 3 переходные 
           $res_en[$order] = $k->cluster($en2[$order]); //пауза будет около 350мс для разреза файла
	}
	//отсортировать по энергии 
	print_r($res_en);
    	exit();
//    print_r($res_en);
//    exit();
    $minFrame = 0;
    
    foreach($res_en as $clu => $arr)
	foreach($arr as $index => $en)
	{
	$fr[$index]["energyCluster"] = $clu;
	$fr[$index]["energy"] = $en[0];
	if (!isset($cutCluster))
	    {
	    $cutCluster = $clu;
	    $cutEnergy = $en[0];
	    }
	    else 
	    if ($cutEnergy > $en[0]) { $cutCluster = $clu; $cutEnergy = $en[0]; $minFrame = $index; } 
	}
	


