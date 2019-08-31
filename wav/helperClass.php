<?php

class Helper
{
//    ...
    public static function readString($handle, $length)
    {
        print "s";
        return self::readUnpacked($handle, 'a*', $length);
    }

    public static function readLong($handle)
    {
	print "l";
        return self::readUnpacked($handle, 'V', 4);
    }

    public static function readWord($handle)
    {  
        print "w";
        return self::readUnpacked($handle, 'v', 2);
    }

    protected function readUnpacked($handle, $type, $length)
    {
        $data = unpack($type, fread($handle, $length));
        if (!empty($data)) print "d";
        return array_pop($data);
    }
    
/*    public static function createFromArray($arr)
	{
	if (is_array($arr)) return $arr;
	return false;
	} */
//    ...
}