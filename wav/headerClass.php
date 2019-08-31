<?php

class Header
{
//    ...
    /**
     * @var string
     */
    protected $id;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var string
     */
    protected $format;

 //  ...
   public static function createFromArray($arr)
    {
    if (is_array($arr)) return $arr;
    return false;
    }

 
}