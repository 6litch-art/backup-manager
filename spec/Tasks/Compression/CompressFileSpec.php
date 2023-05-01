<?php

namespace spec\Backup\Manager\Tasks\Compression;

use Backup\Manager\Compressors\Compressor;
use Backup\Manager\ShellProcessing\ShellProcessor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 *
 */
class CompressFileSpec extends ObjectBehavior
{
    public function it_is_initializable(Compressor $compressor, ShellProcessor $shellProcessor)
    {
        $this->beConstructedWith($compressor, 'path', $shellProcessor);
        $this->shouldHaveType('Backup\Manager\Tasks\Compression\CompressFile');
    }

    public function it_should_execute_the_compression_command(Compressor $compressor, ShellProcessor $shellProcessor)
    {
        $compressor->getCompressCommandLine('path')->willReturn('compress path');

        $shellProcessor->process(Argument::any())->shouldBeCalled();

        $this->beConstructedWith($compressor, 'path', $shellProcessor);
        $this->execute();
    }
}
