<?php
// $in - which lettters already read
include("../connect.php");

$alfabet = "абвгдеёжзийклмнопрстуфхцчшщъыьэюя";
/// mb_internal_encoding("utf8");
$in = [];
$text = ''; 
for($i = 0; $i<iconv_strlen($alfabet); $i++)
    {
    $letter = iconv_substr($alfabet,$i,1);
//    $letter = $alfabet[$i];
//    print $letter;
    if (in_array($letter, $in)) { 
	 // print "s";
	 continue; }  
    $q = "SELECT `word` FROM `sr_dictionary_rus` WHERE `word` like '%".
	    $letter."%' ORDER BY RAND() LIMIT 1";
//	    print $q."\n";
    $r = mysqli_query($conn, $q);
    if (mysqli_num_rows($r) == 0) { print "ALERT EMPTY ".$letter."\n"; continue; } 
    
    list($word) = mysqli_fetch_row($r);
//    print $word."\n";
    $text .= (!empty($text) ? " " : "").$word; 
    for($j = 0; $j < iconv_strlen($word); $j++)
	{
	$tmp = iconv_substr($word, $j, 1);
	$in[] = $tmp;
	}  
    }
    
print $text."\n";

$q = "INSERT INTO `sr_samples` 
	(`sample_text`, 
         `created_on`,
         `type`) VALUES ('".$text."', NOW(), 'test_az')";
         
         print $q."\n";
$r = mysqli_query($conn, $q);
         