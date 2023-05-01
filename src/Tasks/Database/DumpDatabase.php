<?php namespace Backup\Manager\Tasks\Database;

use Backup\Manager\Tasks\Task;
use Backup\Manager\Databases\Database;
use Symfony\Component\Process\Process;
use Backup\Manager\ShellProcessing\ShellProcessor;
use Backup\Manager\ShellProcessing\ShellProcessFailed;

/**
 * Class DumpDatabase
 * @package Backup\Manager\Tasks\Database\Mysql
 */
class DumpDatabase implements Task
{
    /** @var string */
    private string $outputPath;
    /** @var ShellProcessor */
    private ShellProcessor $shellProcessor;
    /** @var Database */
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
