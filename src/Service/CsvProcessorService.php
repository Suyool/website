<?php
// src/Service/CsvProcessorService.php

namespace App\Service;

use App\Entity\Gift2Games\Products;
use Doctrine\Persistence\ManagerRegistry;
use League\Csv\Reader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CsvProcessorService
{
    private $mr;


    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('gift2games');

    }

    public function processCsv(UploadedFile $csvFile): void
    {
        $csvData = $this->parseCsvFile($csvFile);

        foreach ($csvData as $rowData) {
            // Create or update Products entity
            $productId = $rowData['productId']; // Make sure this key matches your CSV column name
            $product = $this->mr->getRepository(Products::class)->findOneBy(['productId' => $productId]);

            if (!$product) {
                $product = new Products();
            }

            // Set entity properties from CSV data
            $product->setProductId($rowData['productId']);
            $product->setCategoryId($rowData['categoryId']);
            $product->setTitle($rowData['title']);
            $product->setImage($rowData['image']);
            $product->setSellPrice($rowData['sellPrice']);
            $product->setPrice($rowData['price']);
            $product->setDiscountRate($rowData['discountRate']);
            $product->setInStock($rowData['inStock']);
            $product->setCurrency($rowData['currency']);
            $product->setCanceled($rowData['canceled']);

            // Save entity to the database
            $this->mr->persist($product);
        }

        // Flush changes to the database
        $this->mr->flush();
    }

    private function parseCsvFile(UploadedFile $csvFile): array
    {
        // Initialize CSV reader
        $csv = Reader::createFromPath($csvFile->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        // Get CSV records
        $records = $csv->getRecords();

        // Convert CSV records to an array
        $csvData = iterator_to_array($records);

        return $csvData;
    }
}
