<?php namespace Backup\Manager\Tasks\Database;

use Backup\Manager\Tasks\Task;
use Backup\Manager\Databases\Database;
use Symfony\Component\Process\Process;
use Backup\Manager\ShellProcessing\ShellProcessor;
use Backup\Manager\ShellProcessing\ShellProcessFailed;

/**
 * Class RestoreDatabase
 * @package Backup\Manager\Tasks\Database
 */
class RestoreDatabase implements Task
{
    /** @var string */
    private $inputPath;
    /** @var ShellProcessor */
    private $shellProcessor;
    /** @var Database */
    private $database;

    /**
     * @param Database $database
     * @param $inputPath
     * @param ShellProcessor $shellProcessor
     */
    public function __construct(Database $database, $inputPath, ShellProcessor $shellProcessor)
    {
        $this->inputPath = $inputPath;
        $this->shellProcessor = $shellProcessor;
        $this->database = $database;
    }

    /**
     * @throws ShellProcessFailed
     */
    public function execute()
    {
        return $this->shellProcessor->process(
            Process::fromShellCommandline(
                $this->database->getRestoreCommandLine($this->inputPath)
            )
        );
    }
}
