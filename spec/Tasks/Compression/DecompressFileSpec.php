<?php

namespace spec\Backup\Manager\Tasks\Compression;

use Backup\Manager\Compressors\Compressor;
use Backup\Manager\ShellProcessing\ShellProcessor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 *
 */
class DecompressFileSpec extends ObjectBehavior
{
    public function it_is_initializable(Compressor $compressor, ShellProcessor $shellProcessor)
    {
        $this->beConstructedWith($compressor, 'path', $shellProcessor);
        $this->shouldHaveType('Backup\Manager\Tasks\Compression\DecompressFile');
    }

    public function it_should_execute_the_decompression_command(Compressor $compressor, ShellProcessor $shellProcessor)
    {
        $compressor->getDecompressCommandLine('path')->willReturn('decompress path');
        $shellProcessor->process(Argument::any())->shouldBeCalled();

        $this->beConstructedWith($compressor, 'path', $shellProcessor);
        $this->execute();
    }
}
