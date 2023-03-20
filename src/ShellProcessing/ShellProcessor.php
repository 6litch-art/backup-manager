<?php namespace Backup\Manager\ShellProcessing;

use Symfony\Component\Process\Process;

/**
 * Class CommandProcessor
 * @package Backup\Manager
 */
class ShellProcessor
{
    /**
     * @param Process $process
     * @return string
     * @throws ShellProcessFailed
     */
    public function process(Process $process)
    {
        $process->setTimeout(null);
        $process->run();
        
        if (! $process->isSuccessful()) {
            throw new ShellProcessFailed($process->getErrorOutput());
        }
        
        return $process->getOutput();
    }
}
