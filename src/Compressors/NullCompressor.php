<?php

namespace Backup\Manager\Compressors;

/**
 * Class NullCompressor.
 */
class NullCompressor implements Compressor
{
    /**
     * @return bool
     */
    public function handles($type)
    {
        return 'null' == strtolower($type ?? '');
    }

    /**
     * @return string
     */
    public function getCompressCommandLine($inputPath)
    {
        return '';
    }

    /**
     * @return string
     */
    public function getDecompressCommandLine($outputPath)
    {
        return '';
    }

    /**
     * @return string
     */
    public function getCompressedPath($inputPath)
    {
        return $inputPath;
    }

    /**
     * @return string
     */
    public function getDecompressedPath($inputPath)
    {
        return $inputPath;
    }
}
