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
            $redirectUrl = 'suyoolpay://suyool.com/suyool=?{"flag":"' . $flag . '","browsertype":"' . $browser . '","AdditionalInfo":"' . $additionalInfo . '","currentUrl":"' . $currentUrl . '"}';
            return new RedirectResponse($redirectUrl);
        }

        return $this->redirectToRoute('homepage');
    }
    /**
     * @Route("/app_install", name="suyoolapplication")
     */
    public function redirectionApp(): Response
    {
            return new RedirectResponse('https://suyoolapp.page.link/app');
    }
}
