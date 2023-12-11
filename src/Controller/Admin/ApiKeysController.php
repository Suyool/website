<?php

namespace App\Controller\Admin;

use App\Entity\topup\ApiKey;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiKeysController extends AbstractController
{
    private $mr;
    private $env;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('topup');
        $this->env = $_ENV['APP_ENV'] == 'prod';
    }

    private function generateStringKey($merchantId): string
    {
        return bin2hex(random_bytes(32) . $merchantId);
    }

    /**
     * @Route("/admin/api/keys/generate", name="api_generate",methods="POST")
     */
    public function generateKey(Request $request){
        $merchantId = $request->request->get('merchant_id');
        $whiteListedIps = $request->request->get('whitelisted_ips');

        $generatedKey = $this->generateStringKey($merchantId);

        $apiKey = new ApiKey();
        $apiKey
            ->setApiKey($generatedKey)
            ->setMerchantId($merchantId)
            ->setEnv($this->env)
            ->setWhitelistedIps($whiteListedIps);

        $this->mr->persist($apiKey);
        $this->mr->flush();

        $response = new Response();
        $response->setContent(json_encode([
            'api_key' => $generatedKey
        ]));

        return $response;

    }
}