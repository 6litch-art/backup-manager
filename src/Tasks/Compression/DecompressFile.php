<?php namespace Backup\Manager\Tasks\Compression;

use Symfony\Component\Process\Process;
use Backup\Manager\ShellProcessing\ShellProcessFailed;
use Backup\Manager\Tasks\Task;
use Backup\Manager\Compressors\Compressor;
use Backup\Manager\ShellProcessing\ShellProcessor;

/**
 * Class DecompressFile
 * @package Backup\Manager\Tasks\Compression
 */
class DecompressFile implements Task
{
    /** @var string */
    private $sourcePath;
    /** @var ShellProcessor */
    private $shellProcessor;
    /** @var Compressor */
    private $compressor;

    /**
     * @param Compressor $compressor
     * @param $sourcePath
     * @param ShellProcessor $shellProcessor
     */
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
