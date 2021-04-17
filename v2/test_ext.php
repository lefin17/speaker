<?php
// https://www.sitepoint.com/extract-an-exceprt-from-a-wav-file/
require_once __DIR__."/vendor/autoload.php";

use Audero\WavExtractor\AuderoWavExtractor;
#ar_dump(class_exists('Audero'));
#ar_dump(class_exists('Audero\WavExtractor'));
#ar_dump(get_declared_classes());


$inputFile = "./samples/aa_converted.wav";

# $a = new AuderoWavExtractor($inputFile);
#xit();
$inputFile = "./samples/aa_converted.wav";
$outputFile = "./excerpt.wav";
$start = 0 * 1000; // from 0 seconds
$end = 2 * 1000;  // to 2 seconds

try {
    $ext = new AuderoWavExtractor($inputFile);
    $wav = $ext->getChunk($start, $end); //берем кусок данных из файла
    
    print_r(get_class_methods(get_class($ext)));
    print "Data Rate: ".$ext->getWav()->getDataRate();
//    print "SampleRate: ".$ext->getWav()->getSampleRate();
    $w = $ext->getWav();
    print_r(get_class_methods(get_class($w)));
    $wc = $w->getSampleRate(); // Headers()[Chunk\Fmt::ID]->getSampleRate()->getValue();
    
    print $wc;
    die();
    // print_r($wc);
    // ::getSampleRate();
    
    foreach($wc as $key => $obj)
	{
	print $key."\n";
	print get_class($obj);
	print_r(get_class_methods(get_class($obj)));
	}
//    print_r($wav);
    
    $h = $w->getHeaders();
    foreach($h as $o)
	{
	//print get_class($o);
	if (in_array("getSampleRate", get_class_methods(get_class($o))))
	    {
	    $K = $o->getSampleRate();
	    print_r($K);
	//    foreach($K as $key => $value)
	//	 print "\n".$key." = ".$value."\n";
		 }
	}
//	$h->
//    print_r(get_class_methods(get_class($ext->getWav())));
    
//    print_r(get_class_methods(get_class($ext->getWav()->getHeaders())));
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
    $i = count($f) - 1;
    $f[$i] = array_pad($f[$i], $length, 0);

    //тут можно включать кластеризацию
    //
    //
    

    