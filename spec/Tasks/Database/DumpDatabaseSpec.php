<?php

namespace spec\Backup\Manager\Tasks\Database;

use Backup\Manager\Databases\Database;
use Backup\Manager\ShellProcessing\ShellProcessor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DumpDatabaseSpec extends ObjectBehavior
{
    public function it_is_initializable(Database $database, ShellProcessor $shellProcessor)
    {
        $this->beConstructedWith($database, 'path', $shellProcessor);
        $this->shouldHaveType('Backup\Manager\Tasks\Database\DumpDatabase');
    }

    public function it_should_execute_the_database_dump_command(Database $database, ShellProcessor $shellProcessor)
    {
        $database->getDumpCommandLine('path')->willReturn('dump path');
        $shellProcessor->process(Argument::any())->shouldBeCalled();

        $this->beConstructedWith($database, 'path', $shellProcessor);
        $this->execute();
    }
}
