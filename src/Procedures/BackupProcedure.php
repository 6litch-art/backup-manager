<?php

namespace Backup\Manager\Procedures;

use Backup\Manager\Compressors\CompressorTypeNotSupported;
use Backup\Manager\Config\ConfigFieldNotFound;
use Backup\Manager\Config\ConfigNotFoundForConnection;
use Backup\Manager\Databases\DatabaseTypeNotSupported;
use Backup\Manager\Filesystems\Destination;
use Backup\Manager\Filesystems\FilesystemTypeNotSupported;
use Backup\Manager\Tasks;

/**
 * Class BackupProcedure.
 */
class BackupProcedure extends Procedure
{
    /**
     * @param Destination[] $destinations
     *
     * @throws FilesystemTypeNotSupported
     * @throws ConfigFieldNotFound
     * @throws CompressorTypeNotSupported
     * @throws DatabaseTypeNotSupported
     * @throws ConfigNotFoundForConnection
     */
    public function run(string $database, array $destinations, string $compression)
    {
        $sequence = new Sequence();

        // begin the life of a new working file
        $localFilesystem = $this->filesystems->get('local');
        $workingFile = $this->getWorkingFile('local');

        // dump the database
        $sequence->add(new Tasks\Database\DumpDatabase(
            $this->databases->get($database),
            $workingFile,
            $this->shellProcessor
        ));

        // archive the dump
        $compressor = $this->compressors->get($compression);
        $sequence->add(new Tasks\Compression\CompressFile(
            $compressor,
            $workingFile,
            $this->shellProcessor
        ));
        $workingFile = $compressor->getCompressedPath($workingFile);

        // upload the archive
        foreach ($destinations as $destination) {
            $sequence->add(new Tasks\Storage\TransferFile(
                $localFilesystem,
                basename($workingFile),
                $this->filesystems->get($destination->destinationFilesystem()),
                $compressor->getCompressedPath($destination->destinationPath())
            ));
        }

        // cleanup the local archive
        $sequence->add(new Tasks\Storage\DeleteFile(
            $localFilesystem,
            basename($workingFile)
        ));

        $sequence->execute();
    }
}
