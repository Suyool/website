<?php

namespace App\Controller\Admin;

use App\Entity\Loto\loto;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class lotoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return loto::class;
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
