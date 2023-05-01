<?php

namespace spec\Backup\Manager\Procedures;

use Backup\Manager\Compressors\CompressorProvider;
use Backup\Manager\Databases\DatabaseProvider;
use Backup\Manager\Filesystems\FilesystemProvider;
use Backup\Manager\Procedures\Sequence;
use Backup\Manager\ShellProcessing\ShellProcessor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 *
 */
class BackupProcedureSpec extends ObjectBehavior
{
    public function it_is_initializable(FilesystemProvider $filesystemProvider, DatabaseProvider $databaseProvider, CompressorProvider $compressorProvider, ShellProcessor $shellProcessor, Sequence $sequence)
    {
        $this->beConstructedWith($filesystemProvider, $databaseProvider, $compressorProvider, $shellProcessor, $sequence);
        $this->shouldHaveType('Backup\Manager\Procedures\BackupProcedure');
    }
}
