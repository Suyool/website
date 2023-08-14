<?php

namespace App\Controller\Admin;

use App\Entity\Touch\Prepaid;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class Prepaid2CrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Prepaid::class;
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
