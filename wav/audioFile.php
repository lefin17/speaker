<?php

class AudioFile
    {
    var $header;
    var $formatSection;
    var $data;
    
    function AudioFile($header, $formatSection, $data)
	{
	$this->header = $header;
	$this->formatSection = $formatSection;
	$this->data = $data;
	}
    }