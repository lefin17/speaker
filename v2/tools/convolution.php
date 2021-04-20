<?php
function flip($data)
{
// https://coderoad.ru/797251/%D0%A2%D1%80%D0%B0%D0%BD%D1%81%D0%BF%D0%BE%D0%BD%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5-%D0%BC%D0%BD%D0%BE%D0%B3%D0%BE%D0%BC%D0%B5%D1%80%D0%BD%D1%8B%D1%85-%D0%BC%D0%B0%D1%81%D1%81%D0%B8%D0%B2%D0%BE%D0%B2-%D0%B2-PHP
    $out = array();
    foreach($data as $x => $subarr)
	foreach($subarr as $y => $subvalue)
	    $out[$y][$x] = $subvalue;
	    
    return $out;
}

function convoluion2D($data, $matrix, $offset = 0) 
{

if (!is_array($matrix)) { print "NOT A MATRIX"; return $data; }
if (!is_array($matrix[0])) { print "NOT A MATRIX"; return $data; } 

$M = flip($matrix); 

$I = $data; //2 dementional array

$height = count($data[0]); 
$length = count($data);
$windowOffsetX = floor(count($M) / 2);
$windowOffsetY = floor(count($M[0]) / 2);

for($y = -$windowOffsetY; $y < $height; $y++) 
    for($x = -$windowOffsetX; $x<0; $x++) $I[$x, $y] = 0; //пограничные условия не рассматриваются как значимые
    for($x = $length; $x<$length + $windowOffsetX; $x++) $I[$x,y] = 0; //расширение матрицы
    
for($x = -$windowOffsetX; $x < $length; $x++) 
    for($y = -$windowOffsetX; $y < 0; $y++) $I[$x, $y] = 0; //пограничные условия не рассматриваются как значимые
    for($y = $height; $y < $height + $windowOffsetY; $y++) $I[$x,y] = 0; //расширение матрицы
    
foreach($data as $y => $arr) 
    foreach($arr as $x => $value) 
	{
	$out[$x][$y] = 0;
	for($i = 0; $i < count($M); $i++)
	    	for($j = 0; $j< count($M[$i]); $j++)
	    	    $out[$x][$y] += $I[$i - $windowOffsetX][$j - $windowOffsetY] * $M[$i][$j];
        	  
        $out[$x][$y] += $offset;
	} //должна работать в преграничных условиях  
	
	return $out;
}