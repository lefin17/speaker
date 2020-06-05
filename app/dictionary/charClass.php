<?php
class makeChar
{
var $conn;
var $sentense;
var $active = false;
var $exists_char = [];
var $query = [];
var $char = [];

function loadChars()
    {
    $q = "SELECT CONCAT(`prefix`,`letter`,`postfix`), `char_id` FROM `sr_samples_char_rus` WHERE 1 ORDER BY `letter` DESC";
    print $q;
    $r = mysqli_query($this->conn, $q);
    
    print "chars in table: ".mysqli_num_rows($r);
    while($row = mysqli_fetch_row($r))
	{
	$this->exists_char[mb_strtolower($row[0])] = true;
	$this->char[$row[0]] = $row[1];
	}
    }

function makeChar($conn)
    { 
    $this->conn = $conn;
    }
 
function loadCharsFromSample($id)
    {
    $this->active = false;
    $q = "SELECT `sample_text` FROM `"._PREFIX_.
	    "samples_rus` WHERE `sample_id` = '".$id."'";
	    
	//    print $q."\n";
    $r = mysqli_query($this->conn, $q);
    if (mysqli_num_rows($r) == 0) return false;
    list($sentence) = mysqli_fetch_row($r);
    $sentence = trim($sentence);
    
    $this->sentence = mb_strtolower($sentence);
    print $this->sentence;
    
    $this->active = true;
    }
    
function makeCharsFromSample()
    {
    if (!$this->active) return false;
    $s = $this->sentence;
    for($i = 0; $i < iconv_strlen($s); $i++)
	{
	print "+";
	$s = $this->sentence;
	$letter = iconv_substr($s, $i, 1);
	$prefix = ($i>0) ? iconv_substr($s, ($i-1), 1) : "|";
	$postfix = ($i < iconv_strlen($s) - 1) ? iconv_substr($s, ($i + 1), 1) : "|";
	
	$e = $prefix.$letter.$postfix;
	if (!empty($this->exists_char[$e])) 
	    { 
		print "e"; 
		continue; 
	    }
	$this->exists_char[$e] = true; 
	$this->query[] = "('".$prefix."', '".$letter."', '".$postfix."')";    
	}    

    }
    
function putChars()
	{
	if (empty($this->query)) { print "E"; return false; } 
	$q = "INSERT INTO `"._PREFIX_."samples_char_rus` (`prefix`, `letter`, `postfix`) VALUES ".join(",", $this->query);
	$q = str_replace("ё", "Ё", $q); //решение проблемы буквы ё... (и звука)
	$r = mysqli_query($this->conn, $q);
	$this->query = [];
	print $q;
	print "i"; 
	}
	
    

	
    
}    