<?php

namespace Backup\Manager\Filesystems;

/**
 * Interface Filesystem.
 */
interface Filesystem
{
    /**
     * Test fitness of visitor.
     *
     * @return bool
     */
    public function handles($type);

    /**
     * @return \League\Flysystem\Filesystem
     */
    public function get(array $config);
}
