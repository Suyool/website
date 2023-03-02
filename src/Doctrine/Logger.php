<?php

namespace App\Doctrine;

use Doctrine\DBAL\Logging\SQLLogger;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\SQLParserUtils;

class Logger implements SQLLogger
{
    /**
     * @var Connection
     */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        if ($params) {
            if(!isset($params['explain'])) {
                list($sql, $params, $types) = SQLParserUtils::expandListParameters($sql, $params, $types);
                $query = vsprintf(str_replace('?', "%s", $sql), call_user_func(function () use ($params, $types) {
                    $quotedParams = array();
                    foreach ($params as $typeIndex => $value) {
                        $quotedParams[] = $this->connection->quote($value, $types[$typeIndex]);
                    }
                    return $quotedParams;
                }));
            }
        } else {
            $indexesJson = file_get_contents("/home/developer/public_html/elnashra.com/public/indexescount.log");
            if(!empty($indexesJson)){
                $indexesArray = json_decode($indexesJson,true);
            }
            if(strpos($sql,"FROM news ")){
                $explainQuery = $this->connection->executeQuery("EXPLAIN $sql", ['explain' => 2550]);

                $data= $explainQuery->fetchAll();
                foreach($data as $d){
                    if(isset($indexesArray)) {
                        $currentCount = (isset($indexesArray[$d['key']])) ? (int)$indexesArray[$d['key']] : 0;
                        $indexesArray[$d['key']] = $currentCount + 1;
                    }
                }

                file_put_contents("indexescount.log",json_encode($indexesArray));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {

    }
}