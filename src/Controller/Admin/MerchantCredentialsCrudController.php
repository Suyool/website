<?php

namespace App\Controller\Admin;

use App\Entity\Shopify\MerchantCredentials;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MerchantCredentialsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MerchantCredentials::class;
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
