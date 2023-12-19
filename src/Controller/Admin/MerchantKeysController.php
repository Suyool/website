<?php

namespace App\Controller\Admin;

use App\Entity\topup\MerchantKey;
use App\Entity\topup\merchants;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MerchantKeysController extends AbstractController
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
        $merchants = $this->mr->getRepository(merchants::class)->findAll();

        //optional filters
        $merchantId = $request->query->get('merchant_id');

        if ($merchantId) {
            $merchantKeys = $this->mr->getRepository(MerchantKey::class)->findBy(['merchant' => $merchantId]);
        } else {
            $merchantKeys = $this->mr->getRepository(MerchantKey::class)->findAll();
        }

        return $this->render('admin/MerchantKeys/index.html.twig', [
            'apiKeys' => $merchantKeys,
            'merchants' => $merchants,
            'merchantId' => $merchantId
        ]);
    }

    /**
     * @Route("/admin/merchant/keys/delete/{id}", name="admin_merchantKey_delete")
     */
    public function delete($id)
    {
        $apiKey = $this->mr->getRepository(MerchantKey::class)->find($id);
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
        $merchants = $this->mr->getRepository(merchants::class)->findAll();

        return $this->render('admin/MerchantKeys/create.html.twig', [
            'merchants' => $merchants
        ]);
    }

    /**
     * @Route("/admin/merchant/keys/generate", name="admin_merchantKey_generate",methods="POST")
     */
    public function generateKey(Request $request)
    {
        $description = $request->request->get('description');
        $merchantId = $request->request->get('merchant_id');
        $whiteListedIps = $request->request->get('whitelisted_ips');
        $expiryDateStr = $request->request->get('expiry_date');
        $expiryDate = new \DateTime($expiryDateStr);
        $merchant = $this->mr->getRepository(merchants::class)->find($merchantId);

        $generatedKey = $this->generateStringKey($merchantId);
        //sha256
        $hashedKey = hash('sha256', $generatedKey);

        $merchantKey = new MerchantKey();
        $merchantKey
            ->setDescription($description)
            ->setApiKey($hashedKey)
            ->setMerchant($merchant)
            ->setEnv($this->env)
            ->setWhitelistedIps($whiteListedIps)
            ->setStatus('ACTIVE')
            ->setExpiryDate($expiryDate);

        $this->mr->persist($merchantKey);
        $this->mr->flush();
        $merchantName = $this->mr->getRepository(merchants::class)->find($merchantId)->getName();
        return $this->render(
            'admin/MerchantKeys/key_generated.html.twig',
            [
                'merchant_key' => $merchantKey->getApiKey(),
                'merchant_name' => $merchantName
            ]
        );
    }
}