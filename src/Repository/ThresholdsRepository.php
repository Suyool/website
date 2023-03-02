<?php

namespace App\Repository;

class ThresholdsRepository extends AppRepository
{
    public function getThresholds($predicates = false, $select = "t", $orderBy = false, $maxResults = false, $firstResult = false)
    {
        return $this->buildQuery("t",$predicates, $select, $orderBy, $maxResults, $firstResult)->getResult();
    }

    public function getThresholdHistory(){
        return $this->getThresholds(
            false,
            "t",
            ["t.createDate"=>"desc"]
        );
    }

    public function getCurrentThreshold(){
        $currentThreshold = $this->getThresholds(
            false,
            "t",
            ["t.createDate"=>"desc"],
            1
        );
        return (!empty($currentThreshold))?$currentThreshold[0]:false;
    }
}