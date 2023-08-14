<?php

namespace App\Controller\Admin;

use App\Entity\emailsubscriber;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class emailsubscriberCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return emailsubscriber::class;
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
