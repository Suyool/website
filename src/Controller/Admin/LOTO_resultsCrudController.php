<?php

namespace App\Controller\Admin;

use App\Entity\Loto\LOTO_results;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LOTO_resultsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LOTO_results::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
