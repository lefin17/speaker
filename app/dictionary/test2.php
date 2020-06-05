<?php
/* Частотный анализ на поиск устойчивых комбинаций из найденных на основе текста */
include("../../connect.php");
include("../../config.php");
include("charClass.php");

$m = new makeChar($conn);

$m->loadChars();



$a = file_get_contents("tur_gam.txt");

$a = iconv("koi8", "utf8", $a);

$a = mb_strtolower($a);

$a = str_replace("-\n", "", $a);

//$a = str_replace("\n", "", $a);

$sentences  = explode(".", $a);

$k = 0;
// $limit = rand(0, count($sentences));
foreach($sentences as $s)
{
$k++;

print "s";
$prefix = "|";
$postfix = iconv_substr($s, 0, 1);
$length = iconv_strlen($s);

for($i = 0; $i < $length; $i++)
    {
     $prefix = ($i == 0) ? "|" : $letter;
     $letter = $postfix;
     $postfix = iconv_substr($s, $i, 1);
     $search = $prefix.$letter.$postfix;
     if (!empty($m->exists_char[$search]))
        {
        $index = $m->char[$search];
        if (empty($res[$index])) { $res[$index] = 1; } else $res[$index]++;
	    $word[$index] = $search;
        print "+";
        }
        else 
        {
        print "-";
        }
    }
}

if (!empty($res))
{
$t = '"index";"word";"qnt"'."\n";
foreach($res as $index => $qnt)
    {
       $t .= "\"".$index."\";\"".$word[$index]."\";\"".$qnt."\"\n"; 
    }
    
$fn = fopen("./res2.csv", "w");
fwrite($fn, $t);
fclose($fn);    
}    