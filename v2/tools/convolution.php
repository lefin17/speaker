<?php
function flip($data)
{
    for($x = 0; $x < count($data); $x++)
	for($y = 0; $y < count($data[$x]); $y++)
	    $out[$y][$x] = $data[$x][$y];
	    
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