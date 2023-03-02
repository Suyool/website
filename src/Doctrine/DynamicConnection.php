<?php
declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;

final class DynamicConnection extends Connection
{
    public function __construct(
        array $params,
        Driver $driver,
        ?Configuration $config = null,
        ?EventManager $eventManager = null
    ) {
        parent::__construct($params, $driver, $config, $eventManager);
    }
    public function selectDatabase($masterDbConnection): void
    {
        if ($this->isConnected()) {
            $this->close();
        }

        $params = $this->getParams();
        $params['dbname'] = $masterDbConnection['database_name'];
/*        $params['user'] = $masterDbConnection['database_user'];
        $params['password'] = $masterDbConnection['database_password'];
        $params['host'] = $masterDbConnection['database_host'];*/

        parent::__construct($params, $this->_driver, $this->_config, $this->_eventManager);
    }
}