<?php namespace Backup\Manager\Tasks\Compression;

use Symfony\Component\Process\Process;
use Backup\Manager\ShellProcessing\ShellProcessFailed;
use Backup\Manager\Tasks\Task;
use Backup\Manager\Compressors\Compressor;
use Backup\Manager\ShellProcessing\ShellProcessor;

/**
 * Class CompressFile
 * @package Backup\Manager\Tasks\Compression
 */
class CompressFile implements Task
{
    /** @var string */
    private string $sourcePath;
    /** @var ShellProcessor */
    private ShellProcessor $shellProcessor;
    /** @var Compressor */
    private Compressor $compressor;

    /**
     * @param Compressor $compressor
     * @param $sourcePath
     * @param ShellProcessor $shellProcessor
     */
    public function __construct(Compressor $compressor, $sourcePath, ShellProcessor $shellProcessor)
    {
        $this->compressor = $compressor;
        $this->sourcePath = $sourcePath;
        $this->shellProcessor = $shellProcessor;
    }

    /**
     * @throws ShellProcessFailed
     */
    public function execute()
    {
        return $this->shellProcessor->process(
            Process::fromShellCommandline(
                $this->compressor->getCompressCommandLine($this->sourcePath)
            )
        );
    }
}
