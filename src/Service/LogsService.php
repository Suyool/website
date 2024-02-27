<?php 

namespace App\Service;

class LogsService {
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
        $this->repository->persist($class);
        $this->repository->flush();

        return true;
    }
}