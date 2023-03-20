<?php namespace Backup\Manager\Compressors;

/**
 * Interface Compressor
 * @package Backup\Manager\Compressors
 */
interface Compressor
{
    /**
     * @param $type
     * @return bool
     */
    public function handles($type);

    /**
     * @param $inputPath
     * @return string
     */
    public function getCompressCommandLine($inputPath);

    /**
     * @param $outputPath
     * @return string
     */
    public function getDecompressCommandLine($outputPath);

    /**
     * @param $inputPath
     * @return string
     */
    public function getCompressedPath($inputPath);

    /**
     * @param $inputPath
     * @return string
     */
    public function getDecompressedPath($inputPath);
}
