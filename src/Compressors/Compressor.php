<?php

namespace Backup\Manager\Compressors;

/**
 * Interface Compressor.
 */
interface Compressor
{
    /**
     * @return bool
     */
    public function handles($type);

    /**
     * @return string
     */
    public function getCompressCommandLine($inputPath);

    /**
     * @return string
     */
    public function getDecompressCommandLine($outputPath);

    /**
     * @return string
     */
    public function getCompressedPath($inputPath);

    /**
     * @return string
     */
    public function getDecompressedPath($inputPath);
}
