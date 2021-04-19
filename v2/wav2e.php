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

# $inputFile = "./samples/aa_converted.wav";

# $a = new AuderoWavExtractor($inputFile);
#xit();
$inputFile = "./samples/aa_converted.wav";
$outputFile = "./res01/aa-c"; //шаблон для сохранения
$startMax = 0 * 1000; // from 0 seconds
$endMax = 11 * 1000;  // to 2 seconds

//f - frame
//fr - frame result as properties

try {
    $ext = new AuderoWavExtractor($inputFile);
    $wav = $ext->getChunk($startMax, $endMax); //берем кусок данных из файла
    $w = $ext->getWav();
    $d = $ext->getWav()->getDuration(); 
    print "Duration: $d\n";
    $sampleRate = $w->getSampleRate();
//    $ext->saveChunk($start, $end, $outputFile);
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
		   //кусок данных дополниться нулями до фрейма нужной длины. - по хорошему перейти к временной шкале... 
    
    $f = array_chunk($data, 256);
    $index = count($f) - 1;
    $f[$index] = array_pad($f[$index], $length, 0);
    for($i = 0; $i < count($f); $i++)
	$e[$i] = array(energy($f[$i]));
    
    $k = new KMeans(5);
    $res = $k->cluster($e); //пауза будет около 350мс для разреза файла
    $minFrame = 0;
    
    foreach($res as $clu => $arr)
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
	
    $cutLength = 6; //if pause more then this -> it's cut time (с текущим sample rate - около 0.3 сек
    $liveLength = 10; //if live length > 10 it's enouht to remember
    $dieLength = 4; //will disapear not borned
    $born = false;  	
    $mass = 0; 
    $p = clone $ext;
	
    for($i = 0; $i < count($f); $i++)
	{
	print $mass;
	$c = $fr[$i]["energyCluster"];
	
	if ($c != $cutCluster && !$born)
	    { $start_fr = $i; 
	      $born = true; 
	      $mass = 1;
	      $cutEvent = 0;
	      print "B"; } else 
	      if ($c != $cutCluster) { 
	      print "L"; $mass++;
	      $cutEvent = 0;
	      }  // 
	
	if (!$born && $c == $cutCluster)
	    { print "."; } 
 	
	if ($born && $c == $cutCluster)
	    { 
	      if ($mass < $liveLength) $mass -= 1;
	      if ($mass < -($dieLength)) {  $born = false; print "D"; } 
	      $cutEvent++; 
	      print "C";
	     }
	    
	if ($born && $cutEvent >= $cutLength)
	    {
	    $start_fr = ($start_fr > 10) ? $start_fr : 0; //перевести в милисекунды если не ноль, добавить 0.2с 
	    $end_fr = $i;
	    
	    // это справедливо если был нулевой offcet
	    $start = round($start_fr * $length / $sampleRate * 1000);
	    $end = round(($end_fr + 1) * $length / $sampleRate * 1000);
	    
//	    print "\nSTART: ". $start;
//	    print "END: ".$end."\n"; 
	    $fn = $outputFile."-".$start."-".$end.".wav"; 
//	    print "fn - $fn";
	
//	    print "Duraion: ".$p->getWav()->getDuration();
	
	    // $wav = $ext->getChunk($startMax, $endMax);	    	    
	    
	    $ext->saveChunk($start, $end, $fn);
	    $ext = new AuderoWavExtractor($inputFile); //пересоздание так как меняется массив при сохранении
	    $cutEvent = 0;
	    $born = false;
	    $mass = 0;
	    //теперь сохранить из исходника файл с указанием номеров фреймов или лучше милисекунд
	    print " cut ";
	    }  
	    
	    
	    
	    
	}
//     print_r($res);

    print "\nMIN ENERGY FRAME: ".$cutEnergy."\n";
    print "CUT CLUSTER: ".$cutCluster."\n";
    print "MIN FRAME: ".$minFrame."\n";
    //тут можно включать кластеризацию
    //
    //
    

    
    