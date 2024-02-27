<?php 

namespace App\Service;

class Logs {
    private $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    public function pushLogs($class,$identifier,$request,$response,$url)
    {
        $class->setidentifier($identifier);
        $class->seturl($url);
        $class->setrequest($request);
        $class->setresponse($response);
        // $logs = $this->repository->
    }
}