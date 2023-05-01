<?php namespace Backup\Manager\Procedures;

use Backup\Manager\Tasks\Task;

/**
 * Class Sequence
 * @package Backup\Manager\Procedures
 */
class Sequence
{
    /** @var array|Task[] */
    private array $tasks = [];

    /**
     * @param Task $task
     */
    public function add(Task $task)
    {
        $this->tasks[] = $task;
    }

    /**
     * Run the procedure.
     * @return void
     */
    public function execute()
    {
        foreach ($this->tasks as $task) {
            $task->execute();
        }
    }
}
