<?php
// save image jpeg with colorized info HSL from matrix and square size, and hsl instruction
// hsl must be connected to main program
// used function HSL from https://stackoverflow.com/questions/20423641/php-function-to-convert-hsl-to-rgb-or-hex
require_once("hsl_transform.php"); 

function createImageFromArray($data, //matrix of data[][] two dimentional
			    $fn = "", //file name 
			    $size = [7, 7], //size of "pixel"
			    $palette = "HSL",
			    $HSL = ["Hue" => "value", "Saturation"=> 70, "Lightness"=>  60])
			    
{

$widthPix = count($data);
$heightPix = count($data[0]); 
$width = $widthPix * $size[0];
$height = $heightPix * $size[1];


$img = imagecreatetruecolor ( $width ,  $height );

for($x = 0; $x < $widthPix; $x++)
    for($y = 0; $y < $heightPix; $y++)
	{
	$value = $data[$x][$y];
	if ($palette == "HSL") //Hue Saturation Lightness palette
	    list($red, $green, $blue) = ColorHSLToRGB($value, 70, 70, false);
	$color = imagecolorallocate($img, $red, $green, $blue);
	imagerectangle($img, 
		       $x * $size[0], 
		       $y * $size[1],
		       ($x + 1) * $size[0],
		       ($y + 1) * $size[1],
		       $color);

	} //end create image... 	        
	imagejpeg($img, $fn);
	imagedestroy($img);	       	
} 