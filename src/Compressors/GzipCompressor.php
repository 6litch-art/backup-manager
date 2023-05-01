<?php

namespace Backup\Manager\Compressors;

/**
 * Class GzipCompressor.
 */
class GzipCompressor implements Compressor
{
    /**
     * @return bool
     */
    public function handles($type)
    {
        return 'gzip' == strtolower($type ?? '');
    }

    /**
     * @return string
     */
    public function getCompressCommandLine($inputPath)
    {
        return 'gzip ' . escapeshellarg($inputPath);
    }

    /**
     * @return string
     */
    public function getDecompressCommandLine($outputPath)
    {
        return 'gzip -d ' . escapeshellarg($outputPath);
    }

    /**
     * @return string
     */
    public function getCompressedPath($inputPath)
    {
        return $inputPath . '.gz';
    }

    /**
     * @return string
     */
    public function getDecompressedPath($inputPath)
    {
        return preg_replace('/\.gz$/', '', $inputPath);
    }
}
