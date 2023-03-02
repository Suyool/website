<?php

namespace App\Twig;

use App\Utils\Helper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('files', [$this, 'filesFilter']),
            new TwigFilter('slugifyText', [$this, 'slugifyTextFilter']),
            new TwigFilter('arabicOrdinalNumber', [$this, 'arabicOrdinalNumberFilter']),
            new TwigFilter('eventCategory', [$this, 'eventCategoryFilter']),
            new TwigFilter('slice', [$this, 'sliceTextFilter']),
            new TwigFilter('substr', [$this, 'subString']),
            new TwigFilter('serverAddress', [$this, 'serverAddress']),
            new TwigFilter('version', [$this, 'appendJsCssVersion'])
        ];
    }

    public function filesFilter($image, $packageName = 'original')
    {
        return (new Helper)->files($image, $packageName);
    }

    public function slugifyTextFilter($string, $type = 1)
    {
        return (new Helper)->slugifyText($string, $type);
    }

    public function arabicOrdinalNumberFilter($number)
    {
        return (new Helper)->getArabicOrdinalNumber($number);
    }

    public function eventCategoryFilter($routeName)
    {
        return ucwords(str_replace('_', ' ', $routeName));
    }

    public function sliceTextFilter($array,$length)
    {
        return array_slice($array, 0,$length);
    }

    public function subString($string,$offset)
    {
        return substr($string,$offset);
    }

    public function serverAddress()
    {
        return $_SERVER['SERVER_ADDR'];
    }

    public function appendJsCssVersion($fileName)
    {
        $params = parse_ini_file('params.ini');
        $fileName .= "?v=".$params['version'];

        return $fileName;
    }
}