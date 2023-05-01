<?php

namespace Backup\Manager\Compressors;

/**
 * Class CompressorProvider.
 */
class CompressorProvider
{
    /** @var array|Compressor[] */
    private array $compressors = [];

    public function add(Compressor $compressor)
    {
        $this->compressors[] = $compressor;
    }

    /**
     * @return Compressor
     *
     * @throws CompressorTypeNotSupported
     */
    public function get($name)
    {
        foreach ($this->compressors as $compressor) {
            if ($compressor->handles($name)) {
                return $compressor;
            }
        }
        throw new CompressorTypeNotSupported('The requested compressor type `'.$name.'` is not currently supported.');
    }
}
