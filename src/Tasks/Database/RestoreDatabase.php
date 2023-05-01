<?php

namespace Backup\Manager\Tasks\Database;

use Backup\Manager\Databases\Database;
use Backup\Manager\ShellProcessing\ShellProcessFailed;
use Backup\Manager\ShellProcessing\ShellProcessor;
use Backup\Manager\Tasks\Task;
use Symfony\Component\Process\Process;

/**
 * Class RestoreDatabase.
 */
class RestoreDatabase implements Task
{
    private string $inputPath;

    private ShellProcessor $shellProcessor;

    private Database $database;

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
