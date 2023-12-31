<?php

namespace Backup\Manager\Procedures;

use Backup\Manager\Compressors\CompressorTypeNotSupported;
use Backup\Manager\Config\ConfigFieldNotFound;
use Backup\Manager\Config\ConfigNotFoundForConnection;
use Backup\Manager\Databases\DatabaseTypeNotSupported;
use Backup\Manager\Filesystems\FilesystemTypeNotSupported;
use Backup\Manager\Tasks;

/**
 * Class RestoreProcedure.
 */
class RestoreProcedure extends Procedure
{
    /**
     * @throws FilesystemTypeNotSupported
     * @throws ConfigFieldNotFound
     * @throws CompressorTypeNotSupported
     * @throws DatabaseTypeNotSupported
     * @throws ConfigNotFoundForConnection
     */
    public function run(string $sourceType, string $sourcePath, string $databaseName, string $compression = null)
    {
        $sequence = new Sequence();

        // begin the life of a new working file
        $localFilesystem = $this->filesystems->get('local');
        $workingFile = $this->getWorkingFile('local', uniqid() . basename($sourcePath));

        // download or retrieve the archived backup file
        $sequence->add(new Tasks\Storage\TransferFile(
            $this->filesystems->get($sourceType),
            $sourcePath,
            $localFilesystem,
            basename($workingFile)
        ));

        // decompress the archived backup
        $compressor = $this->compressors->get($compression);
        $sequence->add(new Tasks\Compression\DecompressFile(
            $compressor,
            $workingFile,
            $this->shellProcessor
        ));
        $workingFile = $compressor->getDecompressedPath($workingFile);

        // restore the database
        $sequence->add(new Tasks\Database\RestoreDatabase(
            $this->databases->get($databaseName),
            $workingFile,
            $this->shellProcessor
        ));

        // cleanup the local copy
        $sequence->add(new Tasks\Storage\DeleteFile(
            $localFilesystem,
            basename($workingFile)
        ));

        $sequence->execute();
    }
}
