<?php
//аналог построение на буквах
class blockTextAnalis
{
var $fn = file($h);

var $level = ["0" => "string", "1" => "block", "2" => "chapter", "3" => "text"];
var $limit = ["s" => 10, "b" => 10, "chapter" => 10, "text" => 1]; 

var $e = []; // энергия...
var $p = []; //мощность вхождения ]
var $expirience; 
//картинку бы посмотреть... 

var $chars = [' ', 'f', 'k']; //провести анализ только этих элементов просто по форме использования
/* задача провести анализ, показать картинку использования букв, разделить на множества, показать картинку по произведению... */
/*
например картинку буквы по произведению...
как характер звука... определяем звук... рисуем среднее по главе, далее определяем характер для текста... 
*/

function search_needle($h, $n)
    {
    $offset = 0;    
    while (($pos = strpos($haystack, $needle, $offset)) !== FALSE) {
        $offset   = $pos + 1;
        $allpos[] = $pos;
    }
    return $allpos;    
    }


function strpos_all($haystack, $needle) {
    if (is_array($needle))
        {
            foreach($needle as $n)
               $res[] = $this->search_needle($heystack, $needle);
        }
        else $res = $this->search_needle($haystack, $needle)
    return $allpos;
}

function count_power($s, $chars)
    {
    $p = strpos_all($s, $chars);
    foreach($p as $char => $power)
        { $char = str_replace(" ", "[Space]", $char); } 
        $res[$char] = array_sum($power);
    return $res;     
    }    



function start()
{
$string = $s = 0;
$block = $b = 0;
        
foreach($this->fn as $string)
    {
        // read procedure
        $s++; 
        $this->e[$c][$b][$s] = iconv_strlen($string); //влияние данной страки на нормирование
        $this->p[$c][$b][$s] = count_power($string, $this->chars);
        if ($c => 10) { $text = 1; break; }
        if ($b => 10) { $c++; $b = 0; } //блок
        if ($s => 10) { $b++; $s = 0; } //номер строки               
    }
    $this->power = $p;

 } //end start
 
  function putInfo()
    {
        $t = '';
        foreach($this->power as $c => $arr)
            foreach($arr as $block => $arr2)
                foreach($arr as $string => $arr3)
                    foreach($arr3 as $char => $voltage)
                        $t .= "$c;$block;$string;$voltage;".$this->e[$c][$block][$string]."\n";
                       

        $output = fopen("resVawelet_text.csv", "w");
        fwrite($output, $t);
        fclose($output);
    }
} //end class
