<?php

class Dictionary
{
var $conn; //connection reference
var $page; //page reference
var $debug = true;
var $source_id; //source_id
var $limit = 1; //limit of readed pages
var $text; //text of page
var $encoding = 'koi8'; 
var $sentences = null;
var $minWords = 5;
var $exampleLength = 15; //рекомендуемое число слов в примере, который сохраняется в словаре
var $dict; //словарь

// var $dictionary_base = 'dictionary_rus;


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
	  /*  */
	} /* */
    }    
    
function getWords()
    {
//	print_r($this->sentences);
	
    	foreach($this->sentences as $s)	    
    	    {
    	    $s = str_replace("\n", " ", $s);
    	    $s = str_replace("\r", " ", $s);
    	    $s = str_replace("  ", " ", $s);
    	    
    	    $words = explode(" ", $s);
    	    if ($words < $this->minWords) { print "s"; continue; }
    	    foreach($words as $w)
    		{ 
    		if (empty($w)) continue;
    		preg_match("/^[А-Яа-я]+[\-]*[А-Яа-я]*/u", $w, $res);
    		if (empty($res[0])) { print "1"; continue; } 
    		$w = $res[0];
    	//	print $s;
    		$this->tryWord("$w", "$s");
    	//	break 2;
    		}
    	    }
    }

}