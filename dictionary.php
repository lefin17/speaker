<?php
include("connect.php");
include("dictionary_class.php");
$link = $argv[1];
print $link."\n";

$d = new Dictionary($link, $conn);

$d->getSourceId();
$d->readPage();
$d->statistic();

$d->getSentence();

$d->getWords();


