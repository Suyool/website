<?php

namespace App\Command\gift2Games;

use App\Entity\Gift2Games\Products;
use App\Service\Gift2GamesService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateProductsCommand extends Command
{
    private $mr;
    private $gamesService;

    public function __construct(ManagerRegistry $mr, Gift2GamesService $gamesService)
    {
        parent::__construct();

        $this->mr = $mr->getManager('gift2games');
        $this->gamesService = $gamesService; // Add this line
    }

    protected function configure()
    {
        //php bin/console
        $this
            ->setName('app:updateProducts');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Updating Products..'
        ]);

        $existingProducts = $this->mr->getRepository(Products::class)->findAll();

        // Create a map for existing products based on productId for faster lookup
        $existingProductsMap = [];
        foreach ($existingProducts as $existingProduct) {
            $existingProductsMap[$existingProduct->getProductId()] = $existingProduct;
        }

        // Fetch products data from the API
        $results = $this->gamesService->getProducts();
        $apiData = $results['data'];
        $apiData = json_decode($apiData, true);
        $apiData = $apiData['data'];

        $chunkSize = 50;
        $existingProductsChunks = array_chunk($existingProductsMap, $chunkSize, true);

        foreach ($existingProductsChunks as $chunk) {
            foreach ($apiData as $productData) {
                $productId = $productData['id'];

                // Check if the product with the same productId exists in the chunk
                if (isset($chunk[$productId])) {
                    $existingProduct = $chunk[$productId];

                    // If the product exists, update the price, inStock, and image
                    $existingProduct->setPrice($productData['price']);
                    $existingProduct->setInStock($productData['inStock']);
                    $existingProduct->setImage($productData['image']);

                    $this->mr->persist($existingProduct);
                }
            }

            $this->mr->flush();
        }
        return Command::SUCCESS;
    }
}
