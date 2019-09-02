<?php
include('../config.php');

function wordWay($word)
    {
        $res = _WAY_CUSTOM_;   
        if ($word == mb_strtoupper($word)) { $res = _WAY_UPPER_; }
        elseif ($word == mb_strtolower($word)) { $res = _WAY_LOWER_; }
        elseif ($word == mb_convert_case($word, MB_CASE_TITLE, "UTF-8")) { $res = _WAY_UCFIRST_; }
        return $res;
    }

if (wordWay("ЧЬЁ") == _WAY_UPPER_) 
    { print "upper done\n"; }
    else { print "erron in upper case way detect"; }
    
if (wordWay("чьё") == _WAY_LOWER_)     
{ print "lower done\n"; }
    else { print "erron in lower case way detect"; }
    
if (wordWay("Чьё") == _WAY_UCFIRST_)
{ print "ucfirst done\n"; }
    else { print "erron in ucfirst case way detect"; }
    
$word = "ЧьёМоё";    
if (wordWay($word) == _WAY_CUSTOM_)
{ print "custom done\n"; }
    else { print "erron in custom case way detect";
    print "wordWay: ".wordWay($word)."\n";
    print "ucfirst: ".ucfirst($word)."\n";
     }
        