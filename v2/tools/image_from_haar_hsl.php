<?php
// save image jpeg with colorized info HSL from matrix and square size, and hsl instruction
// hsl must be connected to main program
// used function HSL from https://stackoverflow.com/questions/20423641/php-function-to-convert-hsl-to-rgb-or-hex
require_once("hsl_transform.php"); 

function createImageFromHaarArray(
			    $img, //resource of image
			    $data, //matrix of data[][] two dimentional
			    $fn = "", //file name 
			    // $degree_from = 3, //до какой степени вести детализацию
			    $offset = 180, //сдвиг в значении цветовой палитры (далее можно будет делать массивом) 
			    $size = [2, 2], //size of "pixel" of order from
			    $palette = "HSL",
			    $HSL = ["Hue" => "value", "Saturation"=> 70, "Lightness"=>  60])
			    
{
$debug = false; 
// получаем расчитанную мощность, единственно, перезаписываем изображение по новой при более мелком фрейме
$widthPix = count($data);
$heightPix = count($data[0]); //развернутый массив - отображаем пока по горизонтали 
$width = $widthPix * $size[0];
$height = $heightPix * $size[1];

if (empty($img))
    $img = imagecreatetruecolor ( $width ,  $height );
// print_r($img);
// die();
for($x = 0; $x < $widthPix; $x++)
    for($y = 0; $y < $heightPix; $y++)
	{
	    $value = round($data[$x][$y]) + $offset;
	if ($palette == "HSL") //Hue Saturation Lightness palette
	    list($red, $green, $blue) = ColorHSLToRGB($value, 70, 70, false);
	if ($debug) print "r:".$red.",g:".$green.",b:".$blue."\n";
	$color = imagecolorallocate($img, $red, $green, $blue);
	imagefilledrectangle($img, 
		       $x * $size[0], 
		       $y * $size[1],
		       ($x + 1) * $size[0],
		       ($y + 1) * $size[1],
		       $color);

	} //end create image... 	        
	imagejpeg($img, $fn);
//	imagedestroy($img);	   
	return $img;    	
} 