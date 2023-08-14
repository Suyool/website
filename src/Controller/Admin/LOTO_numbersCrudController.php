<?php

namespace App\Controller\Admin;

use App\Entity\Loto\LOTO_numbers;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LOTO_numbersCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LOTO_numbers::class;
    }
}
