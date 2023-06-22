<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\Helper;

class DeepLinksController extends AbstractController
{
    /**
     * @Route("/app", name="suyoolapp")
     */
    public function deepLinks(): Response
    {
        $request = Request::createFromGlobals();
        $flag = $request->query->has('f') ? $request->query->get('f') : $request->query->get('flag');
        $currentUrl = $request->getSchemeAndHttpHost();
        $browser = Helper::getBrowserType();

        $additionalInfo = $request->query->get('a') ?? $request->query->get('AdditionalInfo');

        if (stristr($_SERVER['HTTP_USER_AGENT'], 'mobi') !== FALSE) {
            header('Location: suyoolpay://suyool.com/sms=?{"flag":"' . $flag . '","browsertype":"' . $browser . '","AdditionalInfo":"' . $additionalInfo . '","currentUrl":"' . $currentUrl . '"}');

//        } elseif ($flag === '73') {
//            // Redirect desktop devices landed on flag 73 to Merchant page
//            return $this->redirectToRoute('merchant');
//
//        } elseif ($flag === '17') {
            return $this->redirectToRoute('homepage');
        }

        // Redirect desktop devices to homepage by default
        return $this->redirectToRoute('homepage');
    }

}
