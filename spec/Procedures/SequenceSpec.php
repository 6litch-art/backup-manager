<?php

namespace spec\Backup\Manager\Procedures;

use Backup\Manager\Tasks\Task;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SequenceSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Backup\Manager\Procedures\Sequence');
    }

    public function it_should_execute_a_programmed_sequence_of_tasks(Task $taskOne, Task $taskTwo)
    {
        $taskOne->execute()->shouldBeCalled();
        $taskTwo->execute()->shouldBeCalled();

        $this->add($taskOne);
        $this->add($taskTwo);

        $this->execute();
    }
}
