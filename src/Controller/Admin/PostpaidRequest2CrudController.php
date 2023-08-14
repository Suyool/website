<?php

namespace App\Controller\Admin;

use App\Entity\Touch\PostpaidRequest;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PostpaidRequest2CrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PostpaidRequest::class;
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
