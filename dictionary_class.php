<?php
/* Dictionary Class 
- make dictionary with structure of sentence

сохраняет слова 
- freq - таблица частоты использования слов с учетом регистра
- dict - таблица словаря
- position_custom - слова с нестандартным написанием
- position - таблица расположения слов - должна иметь свойство обратного преобразования, например для примеров
- example - запись примера для слова... через временную таблицу?
- sources - источники для чтения
- map - таблица новых источников для чтения и анализа


 - один пример на одно слово? (в теории можно больше)
 - пример на слово хранится если оно того достойно? встретилось больше одного раза
 - пример можно забыть?  
 
 - определить большие или маленкие буквы
*/

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

var $example_md5 = Array();

// task #13 - оптимизация ввода текста (возможно выделить в отдельный класс)
var $words = null; //ассоциативный массив содержащий слова из словаря
var $frq = Array(); //массив частотного анализа
var $queryFrq = Array();
var $update = Array(); // массив для создания временной таблицы обновления
var $insert = Array(); //массив добавления 
var $example = Array();

var $readMethod = "v2"; //метод работы со словарем - v2 - когда используются промежуточные значения переменных и предварительное чтение таблиц словаря

var $queryExamples = null; //запросы на добавление примеров  

var $writeSequence = true;
var $querySequence = null;

var $exampleLength = 15; //рекомендуемое число слов в примере, который сохраняется в словаре
var $dict = "_rus"; //словарь

// var $dictionary_base = 'dictionary_rus;
function updateWords()
    {
        //все что накопилось для вставки в словарь нужно в него записать
        
    }
    
function featureRead()
    {
        $q = "SELECT `feature_id`, `field`, `value` FROM `sr_features` WHERE 1";
        $r = mysqli_query($this->conn, $q);
        while($row = mysqli_fetch_assoc($r))
            {
                $this->feature[$row["field"]][$row["feature_id"]] = $row["value"];
            }
    }    
    
function fillFreq()
    {
    //если нет в таблице freq    
    }
    
function readWords()
    {
    $q = "SELECT dict.`word_id`, -- указатель на слово
                 dict.`word`, -- слово 
              --   frq.`all`, -- все вхождения
              --   frq.`uppercase`, -- верхний регистр (CAPSLOCK)
              --   frq.`lowercase`, -- нижний регистр
              --   frq.`ucfirst`, -- первая заглавная
              --   frq.`custom`, -- перемешка разных букв в слове
                 ex.md5 -- md5 пример использования слова
                FROM `"._PREFIX_."dict_".$this->dict."` as dict
                LEFT JOIN `"._PREFIX_."dict_example".$this->dict." as ex ON ex.word_id = dict.word_id"; //читаем все - выделяем таблицу частоты использования в отдельную где будут собраны частоты
                
    $r = mysqli_query($this->conn, $q);
    
    while($row = mysqli_fetch_assoc($r))
        {
            $word = mb_strtolower($row["word"]);
            $word_id = $row["word_id"]; //номер слова в словаре       

            $this->words[$word] = $row["word_id"];

            $frq["total"] = 0; //частота использования
            //Значения будут просуммированы     
            // $this->frq["frequency"][$word_id] = 0; //$row["frequency"]; //частота использования
            $frq["way"][_WAY_UPPER_] = 0; //$row["uppercase"]; //написано капсом
            $frq["way"][_WAY_LOWER_] = 0; //$row["lowercase"]; //нижний регистр букв
            $frq["way"][_WAY_UCFIRST_] = 0; //$row["ucfirst"]; //первая заглавная
            $frq["way"][_WAY_CUSTOM_] = 0; //$row["other"]; //перемешка с большими и маленькими буквами в слове


            $this->frq[$word_id] = $frq;
            
            $this->example[$word_id] = (!empty($row["example"]) ? $this->sentenceLength($row["example"]) : 0; 
            
            $this->freqQuery["query"] = null; //массив запросов для добавления во временную таблицу (по ключу слова)                         
        }
    print "Read words complite. Count words ".count($this->words)."\n";           
    } 

function checkWord2($word)
    {
        
        //create query whith word_id for freq... and insert new words, and check uppercase or other #14, #13
        
    }
    
function createTmpFreq()
    {
        if (empty($this->frq["query"])) return false;
        print "f";
//        print "Create temporary table for frequency";
            
        $q = "CREATE TEMPORARY TABLE tmp_freq ( 
                    `word_id` INT PRIMARY KEY,
                    `total` INT(11),
                    `uppercase` INT(11),
                    `lowercase` INT(11),
                    `ucfirst` INT(11),
                    `custom` INT(11),
                    `source_id` INT(6)
                    )";             
        mysqli_query($this->conn, $q);
        
        $q = "INSERT INTO `tmp_freq` (`word_id`, `all`, `uppercase`, `lowercase`, `ucfirst`, `other`, `source_id`) VALUES ";                

        $j = 0;
        foreach($this->frq["query"] as $word_id => $query)
            {
                $j++;
                $qi[] = $query;
                    if ($j > 100)
                        {
                            print "+";
                            mysqli_query($this->conn, ($q.join(",".$qi)));  
                            $qi = null; 
                            $j = 0;
                        }                                
            }
         if ($j > 0) mysqli_query($this->conn, ($q.join(",".$qi)));
         //очистка переменной для записи в таблицу - так как туда уже все записали
         $this->frq["query"] = [];
         
            print "Temporary table for save frequency created and filled [".count($this->frq["query"])."]\n"; //закончено формирование временной таблицы                   
    }

function checkTmp($table)
    {
        //what if not exists?
        print "check if exists new data... ";
        $q = "SELECT count(*) FROM ".$table." WHERE 1";
        $r = mysqli_query($this->conn, $q);
        list($count) = mysqli_fetch_row($r)
        $res = (!empty($count)) ? true : false;
        print (($res) ? "exists" : "not exists")."\n";
        return $res;
    }
    
function putTmpFreq()
    {   
        
        if (!$this->checkTmp(`tmp_freq`)) { print "there is no tmp table (tmp_freq)"; return false; } 
         
        print "start update frequency table with temporary data\n";
        
        $q = "UPDATE 
                freq  
              SET
                freq.total = freq.total + tmp.total,
                freq.uppercase = freq.uppercase + tmp.uppercase, 
                freq.lowercase = freq.lowercase + tmp.lowercase, 
                freq.ucfirst = freq.ucfirst + tmp.ucfirst,
                freq.custom = freq.custom + tmp.custom,
                freq.source_id = '".$this->source_id."' -- time when updated
              FROM 
                `"._PREFIX_."freq".$this->dict."` as freq
                INNER JOIN `tmp_freq` as tmp
                ON freq.word_id = tmp.word_id
              -- WHERE STATMENT";
        
           mysqli_query($this->conn, $q);
                                          
          // print "Temporary table merged with frequency table\n";                    
           print "F";                 
    }    

function Dictionary($page, $conn)
    {
    $this->page = mysqli_escape_string($conn, $page);
    $this->conn = $conn;
 //   $this->dict = "dictionary_rus";
    }
    
function getSourceId()
    {
    $q = "SELECT `source_id` FROM `"._PREFIX_."dict_sources".$this->dict."` WHERE `link`='".$this->page."'";
    // print $q."\n";
    $r = mysqli_query($this->conn, $q);
    if (mysqli_num_rows($r) == 0)
	{
	$q = "INSERT INTO `"._PREFIX_."dict_sources.".$this->dict."` (`link`, `created_on`) VALUES ('".$this->page."', NOW())";
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
   $txt = $this->loadSourceTmp();   
   $this->text = (!$txt) ? file_get_contents($this->page) : $txt;
   if (!$txt) $this->putSourceTmp();
   
   if ($this->encoding != 'utf8')
    { 
	$this->text = iconv($this->encoding, 'utf8', $this->text); 
    } 
  //  print_r($this->text);
}

function wordWay($word)
    {
        /* function test test_dict_way.php */
        $res = _WAY_CUSTOM_;   
        if ($word == mb_strtoupper($word)) { $res = _WAY_UPPER_; }
        elseif ($word == mb_strtolower($word)) { $res = _WAY_LOWER_; }
        elseif ($word == mb_convert_case($word, MB_CASE_TITLE, "UTF-8")) { $res = _WAY_UCFIRST_; }
        return $res;
    }

function putSourceTmp()
    {
    print "put Tmp\n";
    print $this->source_id."\n";
    if (!empty($this->source_id)) 
	{
	print "try to put temp file\n";
	$fname = "./tmp/".(int)$this->source_id.$this->dict.".tmp";
	$fn = fopen($fname, "w");
	fwrite($fn, $this->text);
	fclose($fn);
	}
    }

function loadSourceTmp()
    {
    print "load Temporary file\n";
    if ($this->source_id != 0)
	{
	$fn = "./tmp/".(int)$this->source_id.$this->dict.".tmp";
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
    $q = "SELECT `example_id`, `md5`, ` FROM `"._PREFIX_."dict_example".$this->dict."` WHERE `md5` like '".md5(mysqli_escape_string($this->conn, $example)."'";
    $r = mysqli_query($this->conn, $q);
    if (mysqli_num_rows($r) == 0) return false;
    $row = mysqli_fetch_assoc($r);
    $this->examples[$row["md5"]] = $row;
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
    		    $this->putSequence(["word_id" => $word_id,
    		                        "position" => $pos,
    		                        "word" => $word, 
    		                        "sentence_index" => $sentence_index);
    		}
    		$this->fixSequence();
    	    }
    	}
    	    
function fixSequence()
    {
    if (!$this->writeSequence) return false;
    if (empty($this->querySequence)) return false;
	$q = "INSERT INTO `"._PREFIX_."dict_position.".$this->dict."` 
		(`word_id`, -- word index
		 `position`, -- position in words
		 `sentence_index`, --index of sentence in text
		 `source_id`, --source id
		 `prefix`, -- what is before word
		 `postfix`, -- what is after word
		 `way`) -- how the word is writting
		 VALUES ".join(',', $this->querySequence);
	$r = mysqli_query($this->conn, $q);		 
	print "p";
	$this->querySequence = null;		 
    }   
     	
     	
function putCustom($row)
    {
        $q = "INSERT INTO `"._PREFIX_."dict_position_custom".$this->dict."` (`position_id`, `word`) 
                VALUES '".$row["position_id"]."', '".$row["word"]."'";
        mysqli_query($this->conn, $q);       
        print "C"; //custom insert
    }     	    
    
function putSequence($row)
	{
	//в последовательность добавляется кастомизированное написание слов для восстановления и анализа
	
	if (!$this->writeSequence) return false;
	if (empty($this->source_id)) return false;
	$this->querySequence[] = "('".$options["word_id"]."',
			 '".$row["position"]."', 
			 '".$row["sentence_index"]."',
			 '".$this->source_id."',
			 '".(mysqli_escape_string($this->conn, $row["prefix"]))."',
			 '".(mysqli_escape_string($this->conn, $row["postfix"]))."',
			 '".$row["way"]."')"; //возможно ускорить объединив пачку запросов

    if ($row["way"] == _WAY_CUSTOM_)
        {
            $this->fixSequence(); //записываем в любом случае последовательность и пишем кастомное слово
            $this->putCustom([mysqli_insert_id($this->conn), $row["word"]]);
        }

	}
	 //end class

}