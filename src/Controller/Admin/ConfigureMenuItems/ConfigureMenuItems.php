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
        yield MenuItem::linkToRoute('loto', 'fa fa-poll', 'admin_loto');
        yield MenuItem::linkToCrud('Email Subscriber', 'fa fa-envelope', emailsubscriber::class);
        yield MenuItem::linkToCrud('Loto Price', 'fa fa-dollar-sign', LOTO_numbers::class);
        yield MenuItem::linkToCrud('Loto Draw', 'fa fa-dice', LOTO_draw::class);
        yield MenuItem::linkToCrud('Loto Result', 'fa fa-poll', LOTO_results::class);
        yield MenuItem::linkToCrud('Loto orders', 'fa fa-poll', order::class);
        yield MenuItem::linkToCrud('Alfa Logs', 'fa fa-poll', Logs::class);
        yield MenuItem::linkToCrud('Alfa Orders', 'fa fa-poll', \App\Entity\Alfa\Order::class);
        yield MenuItem::linkToCrud('Alfa Postpaid', 'fa fa-poll', Postpaid::class);
        yield MenuItem::linkToCrud('Alfa Postpaid Request', 'fa fa-poll', PostpaidRequest::class);
        yield MenuItem::linkToCrud('Alfa Prepaid', 'fa fa-poll', Prepaid::class);
        yield MenuItem::linkToCrud('Touch Logs', 'fa fa-poll', \App\Entity\Touch\Logs::class);
        yield MenuItem::linkToCrud('Touch Orders', 'fa fa-poll', \App\Entity\Touch\Order::class);
        yield MenuItem::linkToCrud('Touch Postpaid', 'fa fa-poll', \App\Entity\Touch\Postpaid::class);
        yield MenuItem::linkToCrud('Touch Postpaid Request', 'fa fa-poll', \App\Entity\Touch\PostpaidRequest::class);
        yield MenuItem::linkToCrud('Touch Prepaid', 'fa fa-poll', \App\Entity\Touch\Prepaid::class);
        yield MenuItem::linkToCrud('Estore Company', 'fa fa-poll', Company::class);
        yield MenuItem::linkToCrud('Estore Price', 'fa fa-dollar-sign', Price::class);
        yield MenuItem::linkToCrud('Estore Product', 'fab fa-product-hunt', Product::class);
        yield MenuItem::linkToCrud('Notification Content', 'fa fa-poll', content::class);
        yield MenuItem::linkToCrud('Notification Users', 'fa fa-poll', Users::class);
        yield MenuItem::linkToCrud('Notification Template', 'fa fa-poll', Template::class);
        yield MenuItem::linkToCrud('Notification', 'fas fa-sms', Notification::class);
        yield MenuItem::linkToCrud('Shopify Logs', 'fa fa-poll', \App\Entity\Shopify\Logs::class);
        yield MenuItem::linkToCrud('Shopify Merchant Cred', 'fas fa-key', MerchantCredentials::class);
        yield MenuItem::linkToCrud('Shopify Orders', 'fa fa-poll', Orders::class);
        yield MenuItem::linkToCrud('Shopify Test Orders', 'fa fa-poll', OrdersTest::class);
        yield MenuItem::linkToCrud('Shopify Requested Data', 'fa fa-poll', RequestedData::class);
        yield MenuItem::linkToCrud('Shopify Session', 'fa fa-poll', Session::class);
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class);
    }
}