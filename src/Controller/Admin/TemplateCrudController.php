<?php

namespace App\Controller\Admin;

use App\Entity\Notification\Template;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TemplateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Template::class;
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
