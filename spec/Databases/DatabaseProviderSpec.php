<?php

namespace spec\Backup\Manager\Databases;

use Backup\Manager\Config\Config;
use Backup\Manager\Databases\MysqlDatabase;
use PhpSpec\ObjectBehavior;

/**
 *
 */
class DatabaseProviderSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(Config::fromPhpFile('spec/configs/database.php'));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Backup\Manager\Databases\DatabaseProvider');
    }

    public function it_should_provide_requested_databases_by_name()
    {
        $this->add(new MysqlDatabase());
        $this->get('development')->shouldHaveType('Backup\Manager\Databases\MysqlDatabase');
    }

    public function it_should_throw_an_exception_if_a_database_is_unsupported()
    {
        $this->shouldThrow('Backup\Manager\Databases\DatabaseTypeNotSupported')->during('get', ['unsupported']);
    }

    public function it_should_provide_a_list_of_available_databases()
    {
        $this->getAvailableProviders()->shouldBe(['development', 'developmentSingleTrans', 'production', 'unsupported', 'null']);
    }
}
