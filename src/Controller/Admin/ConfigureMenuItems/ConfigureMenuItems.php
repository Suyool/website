<?php

namespace App\Controller\Admin\ConfigureMenuItems;

use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use App\Entity\Admin;
use App\Entity\Alfa\Logs;
use App\Entity\Alfa\Postpaid;
use App\Entity\Alfa\PostpaidRequest;
use App\Entity\Alfa\Prepaid;
use App\Entity\emailsubscriber;
use App\Entity\Estore\Company;
use App\Entity\Estore\Price;
use App\Entity\Estore\Product;
use App\Entity\Loto\loto;
use App\Entity\Loto\LOTO_draw;
use App\Entity\Loto\LOTO_numbers;
use App\Entity\Loto\LOTO_results;
use App\Entity\Loto\order;
use App\Entity\Notification\content;
use App\Entity\Notification\Notification;
use App\Entity\Notification\Template;
use App\Entity\Notification\Users;
use App\Entity\Shopify\MerchantCredentials;
use App\Entity\Shopify\Orders;
use App\Entity\Shopify\OrdersTest;
use App\Entity\Shopify\RequestedData;
use App\Entity\Shopify\Session;
use App\Entity\User;

class ConfigureMenuItems 
{
    public function configureMenuItems() : Iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Email Subscriber', 'fa fa-envelope', emailsubscriber::class);
        yield MenuItem::linkToRoute('Users', 'fa fa-user', 'admin_users');
        yield MenuItem::subMenu('Loto Management')
            ->setSubItems([
                MenuItem::linkToRoute('Loto', '', 'admin_loto'),
            ]);
        yield MenuItem::subMenu('Alfa Management')
            ->setSubItems([
                MenuItem::linkToCrud('Logs', 'fa fa-poll', Logs::class),
                MenuItem::linkToCrud('Orders', 'fa fa-poll', \App\Entity\Alfa\Order::class),
                MenuItem::linkToCrud('Postpaid', 'fa fa-poll', Postpaid::class),
                MenuItem::linkToCrud('Postpaid Request', 'fa fa-poll', PostpaidRequest::class),
                MenuItem::linkToCrud('Prepaid', 'fa fa-poll', Prepaid::class),
            ]);
        yield MenuItem::subMenu('Touch Management')
            ->setSubItems([
                MenuItem::linkToCrud('Logs', 'fa fa-poll', \App\Entity\Touch\Logs::class),
                MenuItem::linkToCrud('Orders', 'fa fa-poll', \App\Entity\Touch\Order::class),
                MenuItem::linkToCrud('Postpaid', 'fa fa-poll', \App\Entity\Touch\Postpaid::class),
                MenuItem::linkToCrud('Postpaid Request', 'fa fa-poll', \App\Entity\Touch\PostpaidRequest::class),
                MenuItem::linkToCrud('Prepaid', 'fa fa-poll', \App\Entity\Touch\Prepaid::class),
        ]);

        yield MenuItem::subMenu('Estore Management')
            ->setSubItems([
                MenuItem::linkToCrud('Company', 'fa fa-poll', Company::class),
                MenuItem::linkToCrud('Price', 'fa fa-dollar-sign', Price::class),
                MenuItem::linkToCrud('Product', 'fab fa-product-hunt', Product::class),
            ]);

        yield MenuItem::subMenu('Notification Management')
            ->setSubItems([
                MenuItem::linkToCrud('Content', 'fa fa-poll', content::class),
                MenuItem::linkToCrud('Users', 'fa fa-poll', Users::class),
                MenuItem::linkToCrud('Template', 'fa fa-poll', Template::class),
                MenuItem::linkToCrud('Notification', 'fas fa-sms', Notification::class),
            ]);
        yield MenuItem::subMenu('Shopify Management')
            ->setSubItems([
                MenuItem::linkToCrud('Logs', 'fa fa-poll', \App\Entity\Shopify\Logs::class),
                MenuItem::linkToCrud('Merchant Cred', 'fas fa-key', MerchantCredentials::class),
                MenuItem::linkToCrud('Orders', 'fa fa-poll', Orders::class),
                MenuItem::linkToCrud('Test Orders', 'fa fa-poll', OrdersTest::class),
                MenuItem::linkToCrud('Requested Data', 'fa fa-poll', RequestedData::class),
                MenuItem::linkToCrud('Session', 'fa fa-poll', Session::class),
            ]);

    }
}