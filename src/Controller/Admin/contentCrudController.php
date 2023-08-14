<?php

namespace App\Controller\Admin;

use App\Entity\Notification\content;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class contentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return content::class;
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
