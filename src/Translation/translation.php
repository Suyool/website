<?php

namespace App\Translation;

use Symfony\Component\CssSelector\XPath\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;

class translation
{

    public function translation($request, $translator)
    {
        $parameters['lang'] = 'en';
        $locale = $request->query->get('lang');

        // Set the locale for the translator
        if ($locale) {
            $translator->setLocale($locale);
            setcookie('lang', $locale, time() + (86400 * 30), "/");
            $parameters['lang'] = $locale;
        } else {
            if ($request->cookies->get('lang')) {
                $parameters['lang'] = $request->cookies->get('lang');
                $translator->setLocale($request->cookies->get('lang'));
            }
        }
        return $parameters;
    }
}
