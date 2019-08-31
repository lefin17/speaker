class Helper
{
//    ...
    public static function readString($handle, $length)
    {
        return self::readUnpacked($handle, 'a*', $length);
    }

    public static function readLong($handle)
    {
        return self::readUnpacked($handle, 'V', 4);
    }

    public static function readWord($handle)
    {
        return self::readUnpacked($handle, 'v', 2);
    }

    protected function readUnpacked($handle, $type, $length)
    {
        $data = unpack($type, fread($handle, $length));

        return array_pop($data);
    }
//    ...
}