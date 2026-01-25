<?php

namespace spec\Backup\Manager\Tasks\Database;

use Backup\Manager\Databases\Database;
use Backup\Manager\ShellProcessing\ShellProcessor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RestoreDatabaseSpec extends ObjectBehavior
{
    public function it_is_initializable(Database $database, ShellProcessor $shellProcessor)
    {
        $this->beConstructedWith($database, 'path', $shellProcessor);
        $this->shouldHaveType('Backup\Manager\Tasks\Database\RestoreDatabase');
    }

    public function it_should_execute_the_database_restore_command(Database $database, ShellProcessor $shellProcessor)
    {
        $database->getRestoreCommandLine('path')->willReturn('restore path');
        $shellProcessor->process(Argument::any())->shouldBeCalled();

        $this->beConstructedWith($database, 'path', $shellProcessor);
        $this->execute();
    }
}
