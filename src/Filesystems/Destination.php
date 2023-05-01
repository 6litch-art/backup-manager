<?php

namespace Backup\Manager\Filesystems;

final class Destination
{
    /** @var string */
    private string $destination_filesystem;

    /** @var string */
    private string $destination_path;

    /**
     * @param string $a_destination_filesystem
     * @param string $a_destination_path
     */
    public function __construct(string $a_destination_filesystem, string $a_destination_path)
    {
        $this->destination_filesystem = $a_destination_filesystem;
        $this->destination_path = $a_destination_path;
    }

    /**
     * @return string
     */
    public function destinationFilesystem()
    {
        return $this->destination_filesystem;
    }

    /**
     * @return string
     */
    public function destinationPath()
    {
        return $this->destination_path;
    }
}
