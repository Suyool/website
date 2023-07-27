<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\Helper;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DeepLinksController extends AbstractController
{
    /**
     * @Route("/app", name="suyoolapp")
     */
    public function deepLinks(): Response
    {
        $request = Request::createFromGlobals();
        // dd($request->query->get('browsertype', ''));

        $flag = $request->query->has('f') || $request->query->has('F') || $request->query->has('flag') || $request->query->has('Flag') ? $request->query->get('f') ?? $request->query->get('F') ?? $request->query->get('flag') ?? $request->query->get('Flag') : null;
        $currentUrl = $request->query->has('currentUrl')?$request->query->get('currentUrl'):$request->getSchemeAndHttpHost();
        $browser = $request->query->get('browsertype', '');

        $additionalInfo = $request->query->get('a') ?? $request->query->get('AdditionalInfo');


        if (stristr($_SERVER['HTTP_USER_AGENT'], 'mobi') !== FALSE) {
            // JavaScript redirect
            $redirectUrl = 'suyoolpay://suyool.com/suyool=?{"flag":"' . $flag . '","browsertype":"' . $browser . '","AdditionalInfo":"' . $additionalInfo . '","currentUrl":"' . $currentUrl . '"}';
            echo "<script>window.location.href = '{$redirectUrl}';</script>";
            exit();
        }
        //        elseif ($flag === '73') {
        //            // Redirect desktop devices landed on flag 73 to Merchant page
        //            return $this->redirectToRoute('merchant');
        //
        //        } elseif ($flag === '17') {
        //            return $this->redirectToRoute('homepage');
        //        }

        // Redirect desktop devices to homepage by default
        return $this->redirectToRoute('homepage');
    }
    /**
     * @Route("/application", name="suyoolapplication", methods={"GET"})
     */
    public function redirectionApp(): Response
    {
        if (stristr($_SERVER['HTTP_USER_AGENT'], 'mobi') !== false) {
            return new RedirectResponse('https://skashapp.page.link/app_install');
        } else {
            return $this->redirectToRoute('homepage');
        }
    }
}
