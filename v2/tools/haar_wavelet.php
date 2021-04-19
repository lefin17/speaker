<?php

function HaarDiff($data)
{
    //Haar Wavelet transformation
    
    for($i = 0; $i * 2 < count($data); $i++)
	{
	$sum[$i] = ($data[$i * 2] + $data[$i * 2 + 1]) / 2;
	$diff[$i] = ($data[$i * 2] - $data[$i * 2 + 1]) / 2; 
	}
    return array($sum, $diff);
}

function Haar($data, $order = 2) //for example 2 order of filter
{
    for($i = 0; $i < $order; $i++)
	list($data, $diff[$i]) = HaarDiff($data); //на вход подается сумма предыдущей итерации
    
    //$diff[] = $data;
return array("sum" => $data, "diff" => $diff);
}

function HaarPower($data)
    {
	foreach($data["diff"] as $index => $order)
	    {
	    $power[$index] = 0;
	    foreach($order as $s) $power[$index] += $s * $s;
	    $power[$index] /= count($order);
	    }
//	$power[] = $data["sum"] * $data["sum"]; //мощность среднего сигнала в основании фрейма
    return $power;
    }

