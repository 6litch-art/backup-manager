<?php

namespace spec\Backup\Manager\Filesystems;

use PhpSpec\ObjectBehavior;

/**
 *
 */
class DropboxV2FilesystemSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Backup\Manager\Filesystems\DropboxV2Filesystem');
    }

    public function it_should_recognize_its_type_with_case_insensitivity()
    {
        foreach (['dropboxv2', 'DropBoxV2', 'DROPBOXV2'] as $type) {
            $this->handles($type)->shouldBe(true);
        }

        foreach ([null, 'foo'] as $type) {
            $this->handles($type)->shouldBe(false);
        }
    }

    public function it_should_provide_an_instance_of_a_dropbox_filesystem()
    {
        $this->get($this->getConfig())->getAdapter()
            ->shouldHaveType('Srmklive\Dropbox\Adapter\DropboxAdapter');
    }

    /**
     * @return string[]
     */
    public function getConfig()
    {
        return [
            'token' => 'token',
            'app' => 'app',
            'root' => 'some/directory/path',
        ];
    }
}
