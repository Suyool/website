<?php

namespace App\Repository;

class RatesRepository extends AppRepository
{
    public function getRates($predicates = false, $select = "r", $orderBy = false, $maxResults = false, $firstResult = false)
    {
        return $this->buildQuery("r",$predicates, $select, $orderBy, $maxResults, $firstResult)->getResult();
    }

    public function getRateHistory(){
        return $this->getRates(
            false,
            "r",
            ["r.createDate"=>"desc"]
        );
    }

    public function getCurrentRate(){
        $currentRate = $this->getRates(
            false,
            "r",
            ["r.createDate"=>"desc"],
            1
        );
        return (!empty($currentRate))?$currentRate[0]:false;
    }
}