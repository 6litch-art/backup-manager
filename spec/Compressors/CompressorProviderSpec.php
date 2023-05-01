<?php

namespace spec\Backup\Manager\Compressors;

use Backup\Manager\Compressors\GzipCompressor;
use PhpSpec\ObjectBehavior;

class CompressorProviderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Backup\Manager\Compressors\CompressorProvider');
    }

    public function it_should_provide_compressors_by_name()
    {
        $this->add(new GzipCompressor());
        $this->get('gzip')->shouldHaveType('Backup\Manager\Compressors\GzipCompressor');
    }

    public function it_should_throw_an_exception_if_it_cant_find_a_compressor()
    {
        $this->shouldThrow('Backup\Manager\Compressors\CompressorTypeNotSupported')->during('get', ['unsupported']);
    }
}
