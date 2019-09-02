<?php


class Dictionary
{
var $conn; //connection reference
var $page; //page reference
var $debug = true;
var $source_id; //source_id
var $limit = 1; //limit of readed pages
var $text; //text of page
var $encoding = 'cp1251'; //koi8, cp1251 
var $sentences = null;
var $minWords = 5;

// task #13 - оптимизация ввода текста (возможно выделить в отдельный класс)
var $words = null; //ассоциативный массив содержащий слова из словаря
var $frq = Array(); //массив частотного анализа
var $update = Array(); // массив для создания временной таблицы обновления
var $insert = Array(); //массив добавления 
var $example = Array();

var $readMethod = "v2"; //метод работы со словарем - v2 - когда используются промежуточные значения переменных и предварительное чтение таблиц словаря

  

var $writeSequence = true;
var $querySequence = null;

var $exampleLength = 15; //рекомендуемое число слов в примере, который сохраняется в словаре
var $dict; //словарь

// var $dictionary_base = 'dictionary_rus;
function updateWords()
    {
        //все что накопилось для вставки в словарь нужно в него записать
        
    }
    
function readWords()
    {
    $q = "SELECT dict.`word_id`, -- указатель на слово
                 dict.`word`, -- слово 
                 frq.`all`, -- все вхождения
                 frq.`uppercase`, -- верхний регистр (CAPSLOCK)
                 frq.`lowercase`, -- нижний регистр
                 frq.`upfirst`, -- первая заглавная
                 frq.`other`, -- перемешка разных букв в слове
                 dict.`example` -- пример использования слова
                FROM `"._PREFIX_."dictionary_rus` as dict
                INNER JOIN `"._PREFIX_."freq_rus as frq ON frq.word_id = dict.word_id"; //читаем все - выделяем таблицу частоты использования в отдельную где будут собраны частоты
    
    $r = mysqli_query($this->conn, $q);
    
    while($row = mysqli_fetch_assoc($r))
        {
            $word = mb_strtolower($row["word"]);
            $word_id = $row["word_id"]; //номер слова в словаре       

            $this->words[$word] = $row["word_id"];
     
            $this->frq["frequency"][$word_id] = $row["frequency"]; //частота использования
            $this->frq["uppercase"][$word_id] = $row["uppercase"]; //написано капсом
            $this->frq["lowercase"][$word_id] = $row["lowercase"]; //нижний регистр букв
            $this->frq["upfirst"][$word_id] = $row["upfirst"]; //первая заглавная
            $this->frq["other"][$word_id] = $row["other"]; //перемешка с большими и маленькими буквами в слове

            $this->example[$word_id] = (!empty($row["example"]) ? $this->sentenceLength($row["example"]) : 0; 
                                  
        }
    print "Read words complite. Count words ".count($this->words)."\n";           
    } 

function Dictionary($page, $conn)
    {
    $this->page = mysqli_escape_string($conn, $page);
    $this->conn = $conn;
    $this->dict = "dictionary_rus";
    }
    
function getSourceId()
    {
    $q = "SELECT `source_id` FROM `sr_sources` WHERE `link`='".$this->page."'";
    // print $q."\n";
    $r = mysqli_query($this->conn, $q);
    if (mysqli_num_rows($r) == 0)
	{
	$q = "INSERT INTO `sr_sources` (`link`, `created_on`) VALUES ('".$this->page."', NOW())";
	$r = mysqli_query($this->conn, $q); 
        $this->source_id = mysqli_insert_id($this->conn);
	}
	else 
	{
	list($this->source_id) = mysqli_fetch_row($r); 
	}
	print "get source id - ".$this->source_id."\n";
	}
	
function readPage()
{
   $txt = $this->loadTmp();   
   $this->text = (!$txt) ? file_get_contents($this->page) : $txt;
   if (!$txt) $this->putTmp();
   
   if ($this->encoding != 'utf8')
    { 
	$this->text = iconv($this->encoding, 'utf8', $this->text); 
    } 
  //  print_r($this->text);
}


function putTmp()
    {
    print "put Tmp\n";
    print $this->source_id."\n";
    if (!empty($this->source_id)) 
	{
	print "try to put temp file\n";
	$fname = "./tmp/".(int)$this->source_id.".tmp";
	$fn = fopen($fname, "w");
	fwrite($fn, $this->text);
	fclose($fn);
	}
    }

function loadTmp()
    {
    print "load Temporary file\n";
    if ($this->source_id != 0)
	{
	$fn = "./tmp/".$this->source_id.".tmp";
	if (file_exists($fn)) return file_get_contents($fn);
	}
    print "no file loaded\n";	
    return false;
    }
    
function statistic()
    {
    print "length: ".strlen($this->text)."\n";
    $tmp = explode(" ", $this->text);
    print "count space ".count($tmp)."\n";
    } 

function getSentence()
    {
    preg_match_all("/[0-9А-Яа-яёЁ\s\,\-\(\)\"A-Za-z]+(\.|\!|\?)/u", $this->text, $res);
//    print_r($res);
    $this->sentences = $res[0];
    }
    
function sentenceLength($txt)
    {
    $txt = trim($txt);    
    $txt = str_replace('  ', ' ', $txt);
    $tmp = explode(" ", $txt);
    return count($tmp);
    }   
function checkExample($example)
    {
    if (empty($example)) return true;
    $q = "SELECT `id` FROM `"._PREFIX_.$this->dict."` WHERE `example` like '".mysqli_escape_string($this->conn, $example)."'";
    $r = mysqli_query($this->conn, $q);
    if (mysqli_num_rows($r) == 0) return false;
    return true;
    }    
    
function tryWord($word, $example)
    {
   $q = "SELECT `id`, `frequncy`, `example` FROM `"._PREFIX_.$this->dict."` WHERE `word` like '".$word."' LIMIT 1";
    $r = mysqli_query($this->conn, $q);
    if (mysqli_num_rows($r) != 0) 
	{ 
	list($id, $frequncy, $example_base) = mysqli_fetch_row($r);
	$slbase = $this->sentenceLength($example_base) - $this->exampleLength;
	$slnew = $this->sentenceLength($example) -$this->exampleLength; 
	
	$uExample = (abs($slbase) > abs($slnew)) ? ", `example` = '".mysqli_escape_string($this->conn, $example)."', `source_id` = '".$this->source_id."'" : "";
	if (!empty($uExample)) 
	if ($this->checkExample($example)) $uExample = ''; //do not update example if it already exists... 
	
	$q = "UPDATE `"._PREFIX_.$this->dict."` SET `frequncy` = ".(++$frequncy).$uExample." WHERE `id` = '".$id."'";
	$r = mysqli_query($this->conn, $q);
	
	print (!empty($uExample)) ? print "e" : "u";
	}
	else 
	{
	$q = "INSERT INTO `"._PREFIX_.$this->dict."` (`word`, `frequncy`, `source_id`) VALUES ('".$word."', '1', '".$this->source_id."')";
	$r = mysqli_query($this->conn, $q);
	print "i";
	$id = mysqli_insert_id($this->conn);
	
	  /*  */
	} /* */
	return $id;
    }    
    
function getWords()
    {
//	print_r($this->sentences);
	
    	foreach($this->sentences as $sentence_index => $s)	    
    	    {
    	    $s = trim($s);
    	    $s = str_replace("-\n", "-", $s); //решаем задачу переносов
    	    $s = str_replace("\n", " ", $s);
    	    $s = str_replace("\r", " ", $s);
    	    $s = str_replace("  ", " ", $s);
    	    
    	    $words = explode(" ", $s);
    	    
    	    if ($words < $this->minWords) { print "s"; continue; }
    	    
    	    $skipWordPosition = 0;
    	    
    	    foreach($words as $position => $w)
    		{ 
    		if (empty($w)) continue;
    		preg_match("/^[А-Яа-яЁё]+[\-]*[а-яё]*/u", $w, $res);
    		if (empty($res[0])) { $skipWordPosition++; print "1"; continue; } 
    		$w = $res[0];
    	//	print $s;
    		$word_id = $this->tryWord("$w", "$s");
    	//	break 2;
    		    $pos = $position - $skipWordPosition;
    		    $this->putSequence($word_id, $pos, $sentence_index);
    		}
    		$this->fixSequence();
    	    }
    	}
    	    
function fixSequence()
    {
    if (!$this->writeSequence) return false;
    if (empty($this->querySequence)) return false;
	$q = "INSERT INTO `"._PREFIX_."position_rus` 
		(`word_id`,
		 `position`,
		 `sentence_index`, 
		 `source_id`)
		 VALUES ".join(',', $this->querySequence);
	$r = mysqli_query($this->conn, $q);		 
	print "p";
	$this->querySequence = null;		 
    }   
     	    
function putSequence($word_id, $position, $sentence_index)
	{
	if (!$this->writeSequence) return false;
	if (empty($this->source_id)) return false;
	$this->querySequence[] = "('".$word_id."',
			 '".$position."', 
			 '".$sentence_index."',
			 '".$this->source_id."')"; //возможно ускорить объединив пачку запросов

	}
	 //end class

}