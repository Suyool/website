<?php

namespace App\Controller\Admin;

use App\Entity\Loto\LOTO_draw;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LOTO_drawCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LOTO_draw::class;
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
