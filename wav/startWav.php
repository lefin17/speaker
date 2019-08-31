<?php

/* Задача получить из файла wav данные для дальнейшей обработки */

require_once("headerClass.php");
require_once("formatSection.php");
require_once("dataSection.php");
require_once("helperClass.php");
require_once("audioFile.php");
require_once("parserClass.php");

$a440 = Parser::fromFile("../tests/A440Hz.wav");

print_r($a440->header);
print_r($a440->formatSection);
print "audio size: ".$a440->data['size'];


//need to get 2 bites from raw from offcet
$a = unpack($a440->data['raw'], "N");

print_r($a);
