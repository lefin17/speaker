class Parser
{
    ...
    public static function fromFile($filename)
    {
        ...
        $handle = fopen($filename, 'rb');

        try {
            $header         = Header::createFromArray(self::parseHeader($handle));
            $formatSection  = FormatSection::createFromArray(self::parseFormatSection($handle));
            $dataSection    = DataSection::createFromArray(self::parseDataSection($handle));
        } finally {
            fclose($handle);
        }

        return new AudioFile($header, $formatSection, $dataSection);
    }

    protected static function parseHeader($handle)
    {
        return [
            'id'     => Helper::readString($handle, 4),
            'size'   => Helper::readLong($handle),
            'format' => Helper::readString($handle, 4),
        ];
    }

    protected static function parseFormatSection($handle)
    {
        return [
            'id'               => Helper::readString($handle, 4),
            'size'             => Helper::readLong($handle),
            'audioFormat'      => Helper::readWord($handle),
            'numberOfChannels' => Helper::readWord($handle),
            'sampleRate'       => Helper::readLong($handle),
            'byteRate'         => Helper::readLong($handle),
            'blockAlign'       => Helper::readWord($handle),
            'bitsPerSample'    => Helper::readWord($handle),
        ];
    }

    protected static function parseDataSection($handle)
    {
        $data = [
            'id' => Helper::readString($handle, 4),
            'size' => Helper::readLong($handle),
        ];

        if ($data['size'] > 0) {
            $data['raw'] = fread($handle, $data['size']);
        }

        return $data;
    }
}    