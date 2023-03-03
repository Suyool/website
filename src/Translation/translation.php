<?php

namespace App\Translation;

use Symfony\Component\CssSelector\XPath\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;

class translation{

    public function translation($request,$translator){
        // Get the locale from the URL parameter
    $locale = $request->query->get('lang');
    // dd($locale);

    // Set the locale for the translator
    if(isset($locale)){
        unset($_COOKIE['lang']);
    $translator->setLocale($locale);
    setcookie('lang', $locale, time() + (86400 * 30), "/");
    }
    if(isset($_COOKIE['lang'])){
        $translator->setLocale($_COOKIE['lang']);
    }
    }

}