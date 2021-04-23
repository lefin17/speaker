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
    
function HaarMinPower($data) //input HaarPower result in frames
    {   
       // value - positive function, possible zero and last frame not relevant
	$min = $data[0]; //принимаем первый фрейм как минимальный
	$frames = count($data);
	foreach($data as $frame => $subvalue)
	    foreach($subvalue as $order => $value)
		if ($frame < $frames)
		    if ($min[$order] > $value && !empty($value)) $min[$order] = $value;
	return $min;
    }
    

function HaarMaxPower($data) //input HaarPower result in frames
    {   
       // value - positive function, possible zero and last frame not relevant
	$max = $data[0]; //принимаем первый фрейм как минимальный
	$frames = count($data);
	foreach($data as $frame => $subvalue)
	    foreach($subvalue as $order => $value)
		if ($max[$order] < $value) $max[$order] = $value;
	return $max;
    }    
    
function HaarNormalize($data, $min)
    {
	//Берем логарифм отношения текущего сигнала к минимальному найденному 
	foreach($data as $frame => $subvalue)
	    foreach($subvalue as $order => $value)
		{
		if ($min[$order] == 0) $min[$order] = 1;
		$res[$frame][$order] = 20 * LOG($value / $min[$order]);
		}
	return $res;
    }        

function HaarLineNormalize($arr, $min)
    {
	//Берем логарифм отношения текущего сигнала к минимальному найденному 
	    foreach($arr as $order => $value)
		{
		if ($min[$order] == 0) $min[$order] = 1;
		$res[$order] = 20 * LOG($value / $min[$order]);
		}
	return $res;
    }        

