<?php 
include("../../connect.php");
include("../../config.php");
include("charClass.php");

$m = new makeChar($conn);
$m->loadChars();
$m->loadCharsFromSample(6);

$a = explode("\n", $m->sentence);
foreach($a as $s)
{
$m->sentence = trim($s);
$m->makeCharsFromSample();
$m->putChars();

}