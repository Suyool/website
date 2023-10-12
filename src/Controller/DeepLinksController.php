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

        $flag = $request->query->has('f') || $request->query->has('F') || $request->query->has('flag') || $request->query->has('Flag') ? $request->query->get('f') ?? $request->query->get('F') ?? $request->query->get('flag') ?? $request->query->get('Flag') : null;
        $currentUrl = $request->query->has('currentUrl')?$request->query->get('currentUrl'):$request->getSchemeAndHttpHost();
        $browser = $request->query->get('browsertype', '');

        $additionalInfo = $request->query->get('a') ?? $request->query->get('AdditionalInfo');


        if (stristr($_SERVER['HTTP_USER_AGENT'], 'mobi') !== FALSE) {
            $redirectUrlAndroid = 'suyoolpay://suyool.com/sms=?{"flag":"' . $flag . '"}';
            $redirectUrlIOS = 'suyoolpay://suyool.com/sms=?{"flag":"' . $flag . '","browsertype":"' . $browser . '","AdditionalInfo":"' . $additionalInfo . '","currentUrl":"' . $currentUrl . '"}';

                $parameters['redirectUrlAndroid'] =$redirectUrlAndroid;
                $parameters['redirectUrlIOS'] =$redirectUrlIOS;

                return $this->render('deeplink/index.html.twig',$parameters);
            }


        return $this->redirectToRoute('homepage');
    }
    /**
     * @Route("/app-install", name="suyoolapplication")
     */
    public function redirectionApp(): Response
    {
            return new RedirectResponse('https://suyoolapp.page.link/app');
    }

    /**
     * @Route("/update-app", name="updateApp")
     */
    public function updateApp(Request $request)
    {
        // Get the User-Agent header from the request
        $userAgent = $request->headers->get('User-Agent');

        // Check if the user agent contains "Android" to detect Android devices
        if (strpos($userAgent, 'Android') !== false) {
            // Redirect to the Google Play Store for Android
            return new RedirectResponse('https://play.google.com/store/apps/details?id=com.suyool.suyoolapp');
        }
        // Check if the user agent contains "iPhone" or "iPad" to detect iOS devices
        elseif (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            // Redirect to the App Store for iOS
            return new RedirectResponse('https://apps.apple.com/lb/app/suyool/id6450320657');
        }
        // If it's not a mobile device, redirect to suyool.com
        else {
            return new RedirectResponse('https://www.suyool.com');
        }
    }
}


