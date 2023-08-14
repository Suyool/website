<?php

namespace App\Controller\Admin;

use App\Entity\Shopify\RequestedData;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RequestedDataCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RequestedData::class;
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
