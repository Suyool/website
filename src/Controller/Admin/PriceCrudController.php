<?php

namespace App\Controller\Admin;

use App\Entity\Estore\Price;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PriceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Price::class;
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
