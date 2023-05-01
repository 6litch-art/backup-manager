<?php

namespace Backup\Manager\Tasks\Compression;

use Backup\Manager\Compressors\Compressor;
use Backup\Manager\ShellProcessing\ShellProcessFailed;
use Backup\Manager\ShellProcessing\ShellProcessor;
use Backup\Manager\Tasks\Task;
use Symfony\Component\Process\Process;

/**
 * Class DecompressFile.
 */
class DecompressFile implements Task
{
    private string $sourcePath;

    private ShellProcessor $shellProcessor;

    private Compressor $compressor;

    public function __construct(Compressor $compressor, $sourcePath, ShellProcessor $shellProcessor)
    {
        $this->sourcePath = $sourcePath;
        $this->shellProcessor = $shellProcessor;
        $this->compressor = $compressor;
    }

    /**
     * @throws ShellProcessFailed
     */
    public function execute()
    {
        return $this->shellProcessor->process(
            Process::fromShellCommandline(
                $this->compressor->getDecompressCommandLine($this->sourcePath)
            )
        );
    }
}
