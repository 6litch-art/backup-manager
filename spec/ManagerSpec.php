<?php

namespace spec\Backup\Manager;

use Backup\Manager\Compressors\CompressorProvider;
use Backup\Manager\Databases\DatabaseProvider;
use Backup\Manager\Filesystems\FilesystemProvider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ManagerSpec extends ObjectBehavior
{
    public function let(FilesystemProvider $filesystems, DatabaseProvider $databases, CompressorProvider $compressors)
    {
        $this->beConstructedWith($filesystems, $databases, $compressors);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Backup\Manager\Manager');
    }

    public function it_should_create_a_backup_procedure()
    {
        $this->makeBackup()->shouldHaveType('Backup\Manager\Procedures\BackupProcedure');
    }

    public function it_should_create_a_restore_procedure()
    {
        $this->makeRestore()->shouldHaveType('Backup\Manager\Procedures\RestoreProcedure');
    }
}
