<?php

class DataSection
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
     * @var int[]
     */
    protected $raw;

//    ...
  public static function createFromArray($arr)
    {
    if (is_array($arr)) return $arr;
    return false;
    }

}    