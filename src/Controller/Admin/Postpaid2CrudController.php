<?php

namespace App\Controller\Admin;

use App\Entity\Touch\Postpaid;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class Postpaid2CrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Postpaid::class;
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
