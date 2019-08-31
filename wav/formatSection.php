<?php

class FormatSection
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
     * @var int
     */
    protected $audioFormat;

    /**
     * @var int
     */
    protected $numberOfChannels;

    /**
     * @var int
     */
    protected $sampleRate;

    /**
     * @var int
     */
    protected $byteRate;

    /**
     * @var int
     */
    protected $blockAlign;

    /**
     * @var int
     */
    protected $bitsPerSample;

    public static function createFromArray($arr)
    {
    if (is_array($arr)) return $arr;
    return false;
    }
    
//    ...
}    