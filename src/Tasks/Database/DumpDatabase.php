<?php

namespace Backup\Manager\Tasks\Database;

use Backup\Manager\Databases\Database;
use Backup\Manager\ShellProcessing\ShellProcessFailed;
use Backup\Manager\ShellProcessing\ShellProcessor;
use Backup\Manager\Tasks\Task;
use Symfony\Component\Process\Process;

/**
 * Class DumpDatabase.
 */
class DumpDatabase implements Task
{
    private string $outputPath;

    private ShellProcessor $shellProcessor;

    private Database $database;

    /**
     * @param Database $database
     * @param $outputPath
     * @param ShellProcessor $shellProcessor
     */
    public function __construct(Database $database, $outputPath, ShellProcessor $shellProcessor)
    {
        $this->outputPath = $outputPath;
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
                $this->database->getDumpCommandLine($this->outputPath)
            )
        );
    }
}
