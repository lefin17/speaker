<?php
//аналог построение на буквах
class blockTextAnalis
{
var $fn = '';

var $level = ["0" => "string", "1" => "block", "2" => "chapter", "3" => "text"];
var $limit = ["s" => 10, "b" => 10, "chapter" => 10, "text" => 1]; 

var $e = []; // энергия системы...
var $p = []; //мощность вхождения ]
var $expirience; 
//картинку бы посмотреть... 

var $chars = [' ', 'я', 'т']; //провести анализ только этих элементов просто по форме использования
/* задача провести анализ, показать картинку использования букв, разделить на множества, показать картинку по произведению... */
/*
например картинку буквы по произведению...
как характер звука... определяем звук... рисуем среднее по главе, далее определяем характер для текста... 
*/

function search_needle($haystack, $needle)
    {        
        
    $allpos = [];
    $pos = 0;
    $offset = 0;
   // print $needle; die(); //)    
    while (($pos = strpos($haystack, $needle, $offset)) !== FALSE) {
        $offset   = $pos + 1;
        $allpos[] = $pos;
    }
    return $allpos;    
    }

function blockTextAnalis($fn)
    
    {
        $this->fn = $fn;
    }


function strpos_all($haystack, $needle) {
    
    if (is_array($needle))
        {
            foreach($needle as $key => $n)
               $res[$n] = count($this->search_needle($haystack, $n));
        }
        else $res = count($this->search_needle($haystack, $needle));
    return $res;
}

function count_power($s, $chars)
    {
    $p = $this->strpos_all($s, $chars);
   // print_r($p); die();
    foreach($p as $char => $power)
        { // $char = str_replace(" ", "[Space]", $char);  
        $res[$char] = (is_array($power)) ? array_sum($power) : $power;
        } 
    return $res;     
    }    



function start()
{
$string = $s = 0;
$block = $b = 0;
$f = file($this->fn);
$p = []; 
$c = 0;
print $this->fn; 
      
print count($f);
///die();
$i = 0;
foreach($f as $string)
    {
        $i++;
        $string = trim($string, "\n");
        // read procedure
//        if ($i<100) continue;
        //$string = iconv("koi8", "cp1251", $string) ? '?' : '';
        
        $s++;
        
      //  if ($s>20) { $s = 0; print $string; }
        print "+";
  ///       print $string;
    //    if ($c < 2) print $string;
        $e[$c][$b][$s] = strlen($string); //влияние данной страки на нормирование
        $p[$c][$b][$s] = $this->count_power($string, $this->chars);
     //   print_r($p[$c][$b][$s]);
     //   die();
       
        if ($b >= 10) { $c++; $b = 0; } //блок
        if ($s >= 10) { $b++; $s = 0; } //номер строки
         if ($c > 10) { $text = 1; break; }               
    }
    $this->p = $p;
    $this->e = $e;
    
//print_r($p);
 } //end start
 
  function putInfo()
    {
      //  print_r($this->p); die();
        $t = '';
        foreach($this->p as $c => $arr)
            foreach($arr as $block => $arr2)
                foreach($arr2 as $string => $arr3)
                    {
                    $t .= "$c;$block;$string;".$this->e[$c][$block][$string].";";
  //                  print_r($arr3);
//                    die();
//print_r($this->chars);
//die();
                    foreach($arr3 as $char => $voltage)
                        {
                        $t .= $char.";$voltage;";
                         }
                     $t .= "\n";
                         
                     }    

        $output = fopen("resVawelet_text.csv", "w");
        fwrite($output, $t);
        fclose($output);
    }
} //end class
