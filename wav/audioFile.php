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
	
/*   protected static function parseHeader($handle)
    {
        return [
            'id'     => Helper::readString($handle, 4),
            'size'   => Helper::readLong($handle),
            'format' => Helper::readString($handle, 4),
        ];
    }

    protected static function parseFormatSection($handle)
    {
    print "parse format section\n";
        return [
            'id'               => Helper::readString($handle, 4),
            'size'             => Helper::readLong($handle),
            'audioFormat'      => Helper::readWord($handle),
            'numberOfChannels' => Helper::readWord($handle),
            'sampleRate'       => Helper::readLong($handle),
            'byteRate'         => Helper::readLong($handle),
            'blockAlign'       => Helper::readWord($handle),
            'bitsPerSample'    => Helper::readWord($handle),

*/

    function bin2int($length, $offset = 0)
	{
	// length & offset in samplerate [2] 
	$block = $this->formatSection['blockAlign'];
	if ($this->data["size"] - $offset * $block < $length * $block) { print "check offset and length"; return false;  } //смещение + длина чтения дают превышение массива
	$data = substr($this->data['raw'], ($offset * $block), ($length * $block));
	$unpack = unpack("v*", $data);
//	print_r($unpack);
	return $unpack;  
	}	
    }