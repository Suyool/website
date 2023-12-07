<?php

namespace App\Controller;

use App\Entity\ApiKeys\ApiKey;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiKeysController extends AbstractController
{
    private $mr;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('merchant-keys');
    }

    /**
     * @Route("/api/keys/generate", name="api_generate",methods="POST")
     */
    private function generateKey(Request $request){
        $merchantId = $request->request->get('merchant_id');
        $whiteListedIps = $request->request->get('whitelisted_ips');

        $generatedKey = $this->generateKey();

        $key = new ApiKey();

    }
}