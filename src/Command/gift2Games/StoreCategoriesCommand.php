<?php

namespace App\Command\gift2Games;

use App\Entity\Gift2Games\Categories;
use App\Service\Gift2GamesService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StoreCategoriesCommand extends Command
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
            ->setName('app:storeCategories');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Storing Categories'
        ]);

        // Truncate the Categories table
        $connection = $this->mr->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeStatement($platform->getTruncateTableSQL('category', true));

        // Get the list of specific categories and their children
        $categoriesToStore = [
            '1111' => ['646'],
            '1110' => ['414'],
            '1091' => ['1123', '730'],
            '454'  => ['514', '504'],
            '448'  => ['633', '624', '298'],
            '446'  => ['582', '581', '413'],
            '445'  => ['567', '562', '558'],
            '444'  => ['575'],
            '442'  => ['704', '703', '644', '642', '641', '636'],
            '441'  => ['664', '656', '617', '496'],
            '439'  => ['477', '472'],
            '434'  => ['469', '462', '457', '455', '428'],
            '406'  => ['645'],
            '302'  => ['417'],
            '282'  => ['647'],
            '277'  => ['905'],
            '76'   => ['343'],
        ];

        $results = $this->gamesService->getCategories();
        $data = $results['data'];
        $data = json_decode($data, true);
        $data = $data['data'];
        $categories = [];

        foreach ($data as $categoryData) {
            $categoryId = $categoryData['id'];

            // Check if the category should be stored based on the provided list
            if (isset($categoriesToStore[$categoryId])) {
                // Create a new Categories entity for the parent category
                $parentCategory = new Categories();
                $parentCategory->setCategoryId($categoryData['id']);
                $parentCategory->setTitle($categoryData['title']);
                $parentCategory->setShortTitle($categoryData['short_title']);
                $parentCategory->setImage($categoryData['image']);

                $this->mr->persist($parentCategory);
                $categories[$categoryId] = $parentCategory;

                // Process child categories recursively
                $this->processCategoriesToStore($categoriesToStore[$categoryId], $categoryData, $parentCategory, $categories);
            }
        }

        // Flush changes to the database
        $this->mr->flush();
        return Command::SUCCESS;
    }

    private function processCategoriesToStore($childIdsToStore, $categoryData, $parentCategory, &$categories)
    {
        $childCategories = $categoryData['childs'] ?? [];

        foreach ($childCategories as $childData) {
            $childId = $childData['id'];

            // Check if the child should be stored based on the provided list
            if (in_array($childId, $childIdsToStore)) {
                // Create a new Categories entity for the child category
                $childCategory = new Categories();
                $childCategory->setCategoryId($childData['id']);
                $childCategory->setTitle($childData['title']);
                $childCategory->setShortTitle($childData['short_title']);
                $childCategory->setImage($childData['image']);
                $childCategory->setParent($parentCategory);

                $this->mr->persist($childCategory);
                $categories[$childId] = $childCategory;

                // Recursively process child categories
                $this->processCategoriesToStore([], $childData, $childCategory, $categories);
            }
        }
    }
}
