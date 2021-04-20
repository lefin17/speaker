<?php

function edgeWindowPower($data)
{
    $power = 0;
    foreach($data as $s)
	$power += $s * $s;
    $power /= count($data); 
    return $power;
}

function edgeWindowDetector($L1, $L2, $position, $data, $minError = 0.1)
{
    //Edge Detector
    $from1 = $position - floor($L1 / 2);
    $from2 = $position - floor($L2 / 2); 

    $W1 = array_slice($data, $from1, $L1);

    $min = min($W1);
    $max = max($W1);

    $MIN = min($W2);
    $MAX = max($W2);

    $alpha1 = $MAX - $max;
    $alpha2 = $min - $MIN;

    $N = edgeWindowPower($W1);
    
    $error = ($max - $min) / $N;
    
    $res = 0;
    if ($alpha1 == $alpha2 && $error > $minError)
	$res = 1;
	
    return $res; 
}