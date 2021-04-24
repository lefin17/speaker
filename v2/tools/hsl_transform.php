<?php
// colorize some data
// first hsl transformation to RGB - for create image of value
if ($argv && $argv[0] && realpath($argv[0]) === __FILE__) {
// test transform    
//https://stackoverflow.com/questions/2413991/php-equivalent-of-pythons-name-main
    $hue = 209;
    $sat = 75;
    $lum = 60;


    $result = ColorHSLToRGB($hue, $sat, $lum);
    var_dump($result); echo '<br>';
//    printf("rgb = %d,%d,%d<br>", $result['r'], $result['g'], $result['b']);
} 



function ColorHSLToRGB($hue, $sat, $lum, $toHex=true) {
// https://stackoverflow.com/questions/20423641/php-function-to-convert-hsl-to-rgb-or-hex
    $hue /= 360;
    $sat /= 100;
    $lum /= 100;
    
    $h = $hue; $s = $sat; $l = $lum; 
        $r = $l;
        $g = $l;
        $b = $l;
        $v = ($l <= 0.5) ? ($l * (1.0 + $s)) : ($l + $s - $l * $s);
        if ($v > 0){
              $m;
              $sv;
              $sextant;
              $fract;
              $vsf;
              $mid1;
              $mid2;

              $m = $l + $l - $v;
              $sv = ($v - $m ) / $v;
              $h *= 6.0;
              $sextant = floor($h);
              $fract = $h - $sextant;
              $vsf = $v * $sv * $fract;
              $mid1 = $m + $vsf;
              $mid2 = $v - $vsf;

              switch ($sextant)
              {
                    case 0:
                          $r = $v;
                          $g = $mid1;
                          $b = $m;
                          break;
                    case 1:
                          $r = $mid2;
                          $g = $v;
                          $b = $m;
                          break;
                    case 2:
                          $r = $m;
                          $g = $v;
                          $b = $mid1;
                          break;
                    case 3:
                          $r = $m;
                          $g = $mid2;
                          $b = $v;
                          break;
                    case 4:
                          $r = $mid1;
                          $g = $m;
                          $b = $v;
                          break;
                    case 5:
                          $r = $v;
                          $g = $m;
                          $b = $mid2;
                          break;
              }
        }
    $r = round($r * 255, 0);
    $g = round($g * 255, 0);
    $b = round($b * 255, 0);

    if ($toHex) {
        $r = ($r < 15)? '0' . dechex($r) : dechex($r);
        $g = ($g < 15)? '0' . dechex($g) : dechex($g);
        $b = ($b < 15)? '0' . dechex($b) : dechex($b);
        return "#$r$g$b";
    } else {
        return array($r, $g, $b);    
    }
}
?>