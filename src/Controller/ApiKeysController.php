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
    private $env;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('merchant-keys');
        $this->env = $_ENV['APP_ENV'] == 'prod';
    }

    public function generateStringKey($merchantId): string
    {
        return bin2hex(random_bytes(32) . $merchantId);
    }

    /**
     * @Route("/api/keys/generate", name="api_generate",methods="POST")
     */
    private function generateKey(Request $request){
        $merchantId = $request->request->get('merchant_id');
        $whiteListedIps = $request->request->get('whitelisted_ips');

        $generatedKey = $this->generateStringKey($merchantId);

        $apiKey = new ApiKey();
        $apiKey
            ->setApiKey($generatedKey)
            ->setMerchantId($merchantId)
            ->setEnv($this->env)
            ->setWhitelistedIps($whiteListedIps);

        dd($apiKey);



    }
}