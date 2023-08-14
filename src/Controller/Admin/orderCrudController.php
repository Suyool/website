<?php

namespace App\Controller\Admin;

use App\Entity\Loto\order;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class orderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return order::class;
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
