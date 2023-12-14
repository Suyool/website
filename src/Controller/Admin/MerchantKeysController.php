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
     * @Route("/admin/merchant/keys", name="admin_merchant_keys")
     */
    public function index(Request $request)
    {

        //optional filters
        $merchantId = $request->query->get('merchant_id');

        if ($merchantId) {
            $apiKeys = $this->mr->getRepository(ApiKey::class)->findBy(['merchantId' => $merchantId]);
        } else {
            $apiKeys = $this->mr->getRepository(ApiKey::class)->findAll();
        }

        return $this->render('admin/MerchantKeys/index.html.twig', [
            'apiKeys' => $apiKeys
        ]);
    }

    /**
     * @Route("/admin/merchant/keys/delete/{id}", name="admin_merchantKey_delete")
     */
    public function delete($id)
    {
        $apiKey = $this->mr->getRepository(ApiKey::class)->find($id);
        $apiKey->setStatus('DELETED');
        $this->mr->persist($apiKey);
        $this->mr->flush();

        return $this->redirectToRoute('admin_merchant_keys');
    }

    /**
     * @Route("/admin/merchant/keys/create", name="admin_merchantKey_create")
     */
    public function create()
    {
        return $this->render('admin/MerchantKeys/create.html.twig');
    }

    /**
     * @Route("/admin/merchant/keys/generate", name="admin_merchantKey_generate",methods="POST")
     */
    public function generateKey(Request $request)
    {
        $description = $request->request->get('description');
        $merchantId = $request->request->get('merchant_id');
        $whiteListedIps = $request->request->get('whitelisted_ips');
        $expiryDate = $request->request->get('expiry_date');

        $generatedKey = $this->generateStringKey($merchantId);
        //sha256
        $hashedKey = hash('sha256', $generatedKey);

        $apiKey = new ApiKey();
        $apiKey
            ->setDescription($description)
            ->setApiKey($hashedKey)
            ->setMerchantId($merchantId)
            ->setEnv($this->env)
            ->setWhitelistedIps($whiteListedIps)
            ->setExpiryDate($expiryDate);

        $this->mr->persist($apiKey);
        $this->mr->flush();

        $response = new Response();
        $response->setContent(json_encode([
            'api_key' => $generatedKey
        ]));

        return $response;
    }
}