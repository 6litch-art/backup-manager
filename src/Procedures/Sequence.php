<?php

namespace Backup\Manager\Procedures;

use Backup\Manager\Tasks\Task;

/**
 * Class Sequence.
 */
class Sequence
{
    /** @var array|Task[] */
    private array $tasks = [];

    public function add(Task $task)
    {
        $this->tasks[] = $task;
    }

    /**
     * Run the procedure.
     *
     * @return void
     */
    public function execute()
    {
        foreach ($this->tasks as $task) {
            $task->execute();
        }
    }
}
