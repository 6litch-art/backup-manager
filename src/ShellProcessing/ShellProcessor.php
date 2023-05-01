<?php

namespace Backup\Manager\ShellProcessing;

use Symfony\Component\Process\Process;

/**
 * Class CommandProcessor.
 */
class ShellProcessor
{
    /**
     * @return string
     *
     * @throws ShellProcessFailed
     */
    public function process(Process $process)
    {
        $process->setTimeout(null);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ShellProcessFailed($process->getErrorOutput());
        }

        return $process->getOutput();
    }
}
