<?php

namespace App\Service;

class Memcached
{
    public function __construct()
    {
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getVouchers($lotoServices)
    {
        if ($_ENV['APP_ENV'] == 'prod') {
            $file = "../var/cache/prod/alfaVoucher.txt";
        } else {
            // $file = "../var/cache/dev/alfaVoucher.txt";
        }
        $clearingTime = time() - (60);
        $filter = null;

        if (file_exists($file) && (filemtime($file) > $clearingTime) && (filesize($file) > 0)) {
            $operationsjson = file_get_contents($file);
            return json_decode($operationsjson, true);
        } else {
            $filter = $lotoServices->VoucherFilter("ALFA");
            // dd($filter);

            foreach ($filter as &$item) {
                switch ($item['vouchertype']) {
                    case 1:
                        $item['desc1'] = "$1.22 Alfa recharge card";
                        $item['desc2'] = "$1.37 Credit Only without validity";
                        $item['desc3'] = "Credit Only";
                        // $item['priceUSD']="1.22";
                        $item['beforeTaxes'] = (float)explode("$", $item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format($item['priceUSD'] - (float)$item['beforeTaxes'], 2, '.');
                        $item['sayrafa'] = $item['priceLBP'] / $item['priceUSD'];
                        $item['image'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/alfa/bundleImg{$item['vouchertype']}h.png";
                        $item['icon'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/alfa/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 13:
                        $item['desc1'] = "$3.02 Alfa recharge card";
                        $item['desc2'] = "Credit and 13 Days Validity";
                        $item['desc3'] = "Credit and 13 Days Validity";
                        // $item['priceUSD']="3.02";
                        $item['beforeTaxes'] = (float)explode("$", $item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format((float)$item['priceUSD'] - (float)$item['beforeTaxes'], 2, '.');
                        $item['sayrafa'] = $item['priceLBP'] / $item['priceUSD'];
                        $item['image'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/alfa/bundleImg{$item['vouchertype']}h.png";
                        $item['icon'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/alfa/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 4:
                        $item['desc1'] = "$4.50 Alfa recharge card";
                        $item['desc2'] = "Credit and up to 35 Days";
                        $item['desc3'] = "Credit and up to 35 Days";
                        $item['beforeTaxes'] = (float)explode("$", $item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format((float)$item['priceUSD'] - (float)$item['beforeTaxes'], 2, '.');
                        $item['sayrafa'] = $item['priceLBP'] / $item['priceUSD'];
                        $item['image'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/alfa/bundleImg{$item['vouchertype']}h.png";
                        $item['icon'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/alfa/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 35:
                        $item['desc1'] = "$7.58 Alfa recharge card";
                        $item['desc2'] = "Credit and 35 Days Validity";
                        $item['desc3'] = "Credit and 35 Days Validity";
                        $item['beforeTaxes'] = (float)explode("$", $item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format((float)$item['priceUSD'] - (float)$item['beforeTaxes'], 2, '.');
                        $item['sayrafa'] = $item['priceLBP'] / $item['priceUSD'];
                        $item['image'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/alfa/bundleImg{$item['vouchertype']}h.png";
                        $item['icon'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/alfa/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 65:
                        $item['desc1'] = "$15.15 Alfa recharge card";
                        $item['desc2'] = "Credit and 65 Days Validity";
                        $item['desc3'] = "Credit and 65 Days Validity";
                        $item['beforeTaxes'] = (float)explode("$", $item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format((float)$item['priceUSD'] - (float)$item['beforeTaxes'], 2, '.');
                        $item['sayrafa'] = $item['priceLBP'] / $item['priceUSD'];
                        $item['image'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/alfa/bundleImg{$item['vouchertype']}h.png";
                        $item['icon'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/alfa/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 95:
                        $item['desc1'] = "$22.73 Alfa recharge card";
                        $item['desc2'] = "Credit and 95 Days Validity";
                        $item['desc3'] = "Credit and 95 Days Validity";
                        $item['beforeTaxes'] = (float)explode("$", $item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format((float)$item['priceUSD'] - (float)$item['beforeTaxes'], 2, '.');
                        $item['sayrafa'] = $item['priceLBP'] / $item['priceUSD'];
                        $item['image'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/alfa/bundleImg{$item['vouchertype']}h.png";
                        $item['icon'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/alfa/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 32:
                        $item['desc1'] = "$7.50 Alfa recharge card";
                        $item['desc2'] = "Waffer Credit and 30 Days Validity";
                        $item['desc3'] = "Waffer Credit and 30 Days Validity";
                        $item['beforeTaxes'] = (float)explode("$", explode(" ", $item['desc'])[1])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format((float)$item['priceUSD'] - (float)$item['beforeTaxes'], 2, '.');
                        $item['sayrafa'] = $item['priceLBP'] / $item['priceUSD'];
                        $item['image'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/alfa/bundleImg{$item['vouchertype']}h.png";
                        $item['icon'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/alfa/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 33:
                        $item['desc1'] = "Waffer Credit and 30 Days Validity";
                        $item['desc2'] = "Waffer Credit and 30 Days Validity";
                        $item['desc3'] = "Waffer Credit and 30 Days Validity";
                        $item['beforeTaxes'] = (float)explode("$", explode(" ", $item['desc'])[1])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format((float)$item['priceUSD'] - (float)$item['beforeTaxes'], 2, '.');
                        $item['sayrafa'] = $item['priceLBP'] / $item['priceUSD'];
                        $item['image'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/alfa/bundleImg{$item['vouchertype']}h.png";
                        $item['icon'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/alfa/Bundle{$item['vouchertype']}h.png";
                        break;
                    default:
                        $item['desc1'] = "default";
                        $item['desc2'] = "default";
                        $item['desc3'] = "default";
                        break;
                }
            }
            $jsonData = json_encode($filter);
            $myfile = fopen($file, "w") or die("Unable to open file!");
            fwrite($myfile, $jsonData);
            fclose($myfile);
        }

        return $filter;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getVouchersTouch($lotoServices)
    {
        if ($_ENV['APP_ENV'] == 'prod') {
            $file = "../var/cache/prod/touchVoucher.txt";
        } else {
            // $file = "../var/cache/dev/touchVoucher.txt";
        }

        $clearingTime = time() - (60);
        $filter = null;

        if (file_exists($file) && (filemtime($file) > $clearingTime) && (filesize($file) > 0)) {
            $operationsjson = file_get_contents($file);
            return json_decode($operationsjson, true);
        } else {
            $filter = $lotoServices->VoucherFilter("MTC");
            // dd($filter);
            foreach ($filter as &$item) {
                switch ($item['vouchertype']) {
                    case 1:
                        $item['desc1'] = "Credit Only";
                        $item['desc2'] = "$1.22 Touch recharge card";
                        $item['desc3'] = "$1.22 Touch recharge card";
                        $item['beforeTaxesPrice'] = "SOS Start $" . (float)explode("$", $item['desc'])[0];;
                        $item['beforeTaxes'] = (float)explode("$", $item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format($item['priceUSD'] - (float)$item['beforeTaxes'], 2, '.');
                        $item['sayrafa'] = $item['priceLBP'] / $item['priceUSD'];
                        $item['image'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/Touch/bundleImg{$item['vouchertype']}h.png";
                        $item['icon'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/Touch/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 10:
                        $item['desc1'] = "10 Days Validity & 5 Days Grace";
                        $item['desc2'] = "Credit and 13 Days Validity";
                        $item['desc3'] = "$3.79 Touch recharge card";
                        // $item['priceUSD']="3.79";
                        $item['beforeTaxesPrice'] = "$" . (float)explode("$", $item['desc'])[0];
                        $item['beforeTaxes'] = (float)explode("$", $item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format($item['priceUSD'] - (float)$item['beforeTaxes'], 2, '.');
                        $item['sayrafa'] = $item['priceLBP'] / $item['priceUSD'];
                        $item['image'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/Touch/bundleImg{$item['vouchertype']}h.png";
                        $item['icon'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/Touch/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 29:
                        $item['desc1'] = "30 Days Validity";
                        $item['desc2'] = "Credit and up to 35 Days";
                        $item['desc3'] = "$4.50 Touch recharge card";
                        // $item['priceUSD']="4.50";
                        $item['beforeTaxesPrice'] = "$" . (float)explode("$", $item['desc'])[0];
                        $item['beforeTaxes'] = (float)explode("$", $item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format($item['priceUSD'] - (float)$item['beforeTaxes'], 2, '.');
                        $item['sayrafa'] = $item['priceLBP'] / $item['priceUSD'];
                        $item['image'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/Touch/bundleImg{$item['vouchertype']}h.png";
                        $item['icon'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/Touch/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 30:
                        $item['desc1'] = "30 Days Validity & 5 Days Grace";
                        $item['desc2'] = "Credit and 35 Days Validity";
                        $item['desc3'] = "$7.58 Touch recharge card";
                        // $item['priceUSD']="7.58";
                        $item['beforeTaxesPrice'] = "$" . (float)explode("$", $item['desc'])[0];
                        $item['beforeTaxes'] = (float)explode("$", $item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format($item['priceUSD'] - (float)$item['beforeTaxes'], 2, '.');
                        $item['sayrafa'] = $item['priceLBP'] / $item['priceUSD'];
                        $item['image'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/Touch/bundleImg{$item['vouchertype']}h.png";
                        $item['icon'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/Touch/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 60:
                        $item['desc1'] = "60 Days Validity & 5 Days Grace";
                        $item['desc2'] = "Credit and 65 Days Validity";
                        $item['desc3'] = "$15.15 Touch recharge card";
                        // $item['priceUSD']="15.15";
                        $item['beforeTaxesPrice'] = "$" . (float)explode("$", $item['desc'])[0];
                        $item['beforeTaxes'] = (float)explode("$", $item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format($item['priceUSD'] - (float)$item['beforeTaxes'], 2, '.');
                        $item['sayrafa'] = $item['priceLBP'] / $item['priceUSD'];
                        $item['image'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/Touch/bundleImg{$item['vouchertype']}h.png";
                        $item['icon'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/Touch/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 90:
                        $item['desc1'] = "90 Days Validity & 5 Days Grace";
                        $item['desc2'] = "Credit and 95 Days Validity";
                        $item['desc3'] = "$22.73 Touch recharge card";
                        // $item['priceUSD']="22.73";
                        $item['beforeTaxesPrice'] = "$" . (float)explode("$", $item['desc'])[0];
                        $item['beforeTaxes'] = (float)explode("$", $item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format($item['priceUSD'] - (float)$item['beforeTaxes'], 2, '.');
                        $item['sayrafa'] = $item['priceLBP'] / $item['priceUSD'];
                        $item['image'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/Touch/bundleImg{$item['vouchertype']}h.png";
                        $item['icon'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/Touch/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 31:
                        $item['desc1'] = "30 Days Validity";
                        $item['desc2'] = "Waffer Credit and 30 Days Validity";
                        $item['desc3'] = "$4.50 Touch recharge card";
                        // $item['priceUSD']="4.50";
                        $item['beforeTaxesPrice'] = "Start $" . (float)explode("$", explode(" ", $item['desc'])[1])[0];
                        $item['beforeTaxes'] = (float)explode("$", explode(" ", $item['desc'])[1])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format((float)$item['priceUSD'] - (float)$item['beforeTaxes'], 2, '.');
                        $item['sayrafa'] = $item['priceLBP'] / $item['priceUSD'];
                        $item['image'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/Touch/bundleImg{$item['vouchertype']}h.png";
                        $item['icon'] = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . "/build/images/Touch/Bundle{$item['vouchertype']}h.png";
                        break;
                    default:
                        $item['desc1'] = "default";
                        $item['desc2'] = "default";
                        $item['desc3'] = "default";
                        $item['priceUSD'] = "default";
                        break;
                }
            }

            $jsonData = json_encode($filter);
            $myfile = fopen($file, "w") or die("Unable to open file!");
            fwrite($myfile, $jsonData);
            fclose($myfile);
        }

        return $filter;
    }


    // public function getAllCountriesBySimly($simlyServices)
    // {
    //     $file = ($_ENV['APP_ENV'] == 'prod') ? "../var/cache/prod/CountriesBySimly.txt" : "../var/cache/dev/CountriesBySimly.txt";

    //     if (file_exists($file)) {
    //         $fileModificationTime = filemtime($file);
    //     } else {
    //         $fileModificationTime = 0;
    //     }

    //     $cacheExpiration = 5;
    //     $currentTime = time();

    //     if ($fileModificationTime + $cacheExpiration > $currentTime && filesize($file) > 0) {
    //         $operationsjson = file_get_contents($file);
    //         $filter = json_decode($operationsjson, true);
    //     } else {
    //         $data = $simlyServices->GetCountriesPlans();

    //         // Initialize an empty array to hold the divided arrays
    //         $dividedArrays = [
    //             'Europe' => [],
    //             'Asia' => [],
    //             'North America' => [],
    //             'South America' => [],
    //             'Africa' => [],
    //             'Oceania' => [],
    //             // 'Middle East' => [],
    //             'Default' => [],
    //         ];

    //         // Define the mappings of ISO codes to regions
    //         $regionMappings  = [
    //             'Europe' => ['ALA', 'ALB', 'AND', 'AUT', 'BLR', 'BEL', 'BIH', 'BGR', 'HRV', 'CZE', 'DNK', 'EST', 'FRO', 'FIN', 'FRA', 'DEU', 'GRC', 'GIB', 'GRC', 'GGY', 'VAT', 'HUN', 'ISL', 'IRL', 'IMN', 'ITA', 'JEY', 'LVA', 'LIE', 'LTU', 'LUX', 'MLT', 'MCO', 'MNE', 'NLD', 'MKD', 'NOR', 'POL', 'PRT', 'MDA', 'ROU', 'RUS', 'SMR', 'SRB', 'SVK', 'SVN', 'ESP', 'SJM', 'SWE', 'CHE', 'UKR', 'GBR'],
    //             'Asia' => ['AFG', 'ARM', 'AZE', 'BHR', 'BGD', 'BTN', 'BRN', 'KHM', 'CHN', 'HKG', 'MAC', 'CYP', 'PRK', 'GEO', 'IND', 'IDN', 'IRN', 'ISR', 'JPN', 'JOR', 'KAZ', 'KWT', 'KGZ', 'LAO', 'LBN', 'MYS', 'MDV', 'MNG', 'MMR', 'NPL', 'OMN', 'PAK', 'PHL', 'QAT', 'KOR', 'SAU', 'SGP', 'LKA', 'PSE', 'SYR', 'TJK', 'THA', 'TLS', 'TUR', 'TKM', 'ARE', 'UZB', 'VNM', 'YEM'],
    //             'North America' => ['AIA', 'ATG', 'ABW', 'BHS', 'BRB', 'BLZ', 'BMU', 'BES', 'VGB', 'CAN', 'CYM', 'CRI', 'CUB', 'CUW', 'DMA', 'DOM', 'SLV', 'GRL', 'GRD', 'GLP', 'GTM', 'HTI', 'HND', 'JAM', 'MTQ', 'MEX', 'MSR', 'NIC', 'PAN', 'PRI', 'BLM', 'KNA', 'LCA', 'MAF', 'SPM', 'VCT', 'SXM', 'TTO', 'TCA', 'USA', 'VIR'],
    //             'South America' => ['ARG', 'BOL', 'BVT', 'BRA', 'CHL', 'COL', 'ECU', 'FLK', 'GUF', 'GUY', 'PRY', 'PER', 'SGS', 'SUR', 'URY', 'VEN'],
    //             'Africa' => ['DZA', 'AGO', 'BEN', 'BWA', 'IOT', 'BFA', 'BDI', 'CPV', 'CMR', 'CAF', 'TCD', 'COM', 'COG', 'CIV', 'COD', 'DJI', 'EGY', 'GNQ', 'ERI', 'SWZ', 'ETH', 'ATF', 'GAB', 'GMB', 'GHA', 'GIN', 'GNB', 'KEN', 'LSO', 'LBR', 'LBY', 'MDG', 'MWI', 'MLI', 'MRT', 'MUS', 'MYT', 'MAR', 'MOZ', 'NAM', 'NER', 'NGA', 'REU', 'RWA', 'SHN', 'STP', 'SEN', 'SYC', 'SLE', 'SOM', 'ZAF', 'SSD', 'SDN', 'TGO', 'TUN', 'UGA', 'TZA', 'ESH', 'ZMB', 'ZWE'],
    //             'Oceania' => ['ASM', 'AUS', 'CXR', 'CCK', 'COK', 'FJI', 'PYF', 'GUM', 'HMD', 'KIR', 'MHL', 'FSM', 'NRU', 'NCL', 'NZL', 'NIU', 'NFK', 'MNP', 'PLW', 'PNG', 'PCN', 'WSM', 'SLB', 'TKL', 'TON', 'TUV', 'UMI', 'VUT', 'WLF'],
    //             // 'Middle East' => [],
    //             'Default' => ['ATA']
    //         ];

    //         // Loop through the countries and assign them to the appropriate array
    //         foreach ($data['local'] as $country) {
    //             $region = $this->getRegionByCountryCode($country['isoCode'], $regionMappings);
    //             $dividedArrays[$region][] = $country;
    //         }

    //         $filter = $dividedArrays;

    //         $jsonData = json_encode($filter);
    //         file_put_contents($file, $jsonData);
    //     }

    //     return $filter;
    // }

    // // Function to get the region by country code
    // private function getRegionByCountryCode($isoCode, $regionMappings)
    // {
    //     foreach ($regionMappings as $region => $isoCodes) {
    //         if (in_array($isoCode, $isoCodes)) {
    //             return $region;
    //         }
    //     }
    //     return 'Default';
    // }

    public function getAllCountriesBySimly($simlyServices)
    {
        // $file = ($_ENV['APP_ENV'] == 'prod') ? "../var/cache/prod/CountriesBySimly.txt" : "../var/cache/dev/CountriesBySimly.txt";
        $file = ($_ENV['APP_ENV'] == 'prod') ? "../var/cache/prod/CountriesBySimly.txt" : (($_ENV['APP_ENV'] == 'test') ? "../var/cache/test/CountriesBySimly.txt" : (($_ENV['APP_ENV'] == 'sandbox') ? "../var/cache/sandbox/CountriesBySimly.txt" : "../var/cache/dev/CountriesBySimly.txt"));

        if (file_exists($file)) {
            $fileModificationTime = filemtime($file);
        } else {
            $fileModificationTime = 0;
        }

        $cacheExpiration = 5;
        $currentTime = time();

        if ($fileModificationTime + $cacheExpiration > $currentTime && filesize($file) > 0) {
            $operationsjson = file_get_contents($file);
            $filter = json_decode($operationsjson, true);
        } else {
            $data = $simlyServices->GetCountriesPlans();

            // Initialize an empty array to hold the divided arrays
            $dividedArrays = [
                'Europe' => [],
                'Asia' => [],
                'North America' => [],
                'South America' => [],
                'Africa' => [],
                'Oceania' => [],
                // 'Middle East' => [],
                'Default' => [],
            ];

            // Define the mappings of ISO codes to regions for the local array only
            $regionMappings  = [
                'Europe' => ['ALA', 'ALB', 'AND', 'AUT', 'BLR', 'BEL', 'BIH', 'BGR', 'HRV', 'CZE', 'DNK', 'EST', 'FRO', 'FIN', 'FRA', 'DEU', 'GRC', 'GIB', 'GRC', 'GGY', 'VAT', 'HUN', 'ISL', 'IRL', 'IMN', 'ITA', 'JEY', 'LVA', 'LIE', 'LTU', 'LUX', 'MLT', 'MCO', 'MNE', 'NLD', 'MKD', 'NOR', 'POL', 'PRT', 'MDA', 'ROU', 'RUS', 'SMR', 'SRB', 'SVK', 'SVN', 'ESP', 'SJM', 'SWE', 'CHE', 'UKR', 'GBR', 'IC', 'XKX'],
                'Asia' => ['AFG', 'ARM', 'AZE', 'BHR', 'BGD', 'BTN', 'BRN', 'KHM', 'CHN', 'HKG', 'MAC', 'CYP', 'PRK', 'GEO', 'IND', 'IDN', 'IRN', 'ISR', 'JPN', 'JOR', 'KAZ', 'KWT', 'KGZ', 'LAO', 'LBN', 'MYS', 'MDV', 'MNG', 'MMR', 'NPL', 'OMN', 'PAK', 'PHL', 'QAT', 'KOR', 'SAU', 'SGP', 'LKA', 'PSE', 'SYR', 'TJK', 'THA', 'TLS', 'TUR', 'TKM', 'ARE', 'UZB', 'VNM', 'YEM', 'IRQ', 'TWN'],
                'North America' => ['AIA', 'ATG', 'ABW', 'BHS', 'BRB', 'BLZ', 'BMU', 'BES', 'VGB', 'CAN', 'CYM', 'CRI', 'CUB', 'CUW', 'DMA', 'DOM', 'SLV', 'GRL', 'GRD', 'GLP', 'GTM', 'HTI', 'HND', 'JAM', 'MTQ', 'MEX', 'MSR', 'NIC', 'PAN', 'PRI', 'BLM', 'KNA', 'LCA', 'MAF', 'SPM', 'VCT', 'SXM', 'TTO', 'TCA', 'USA', 'VIR', 'US-HI'],
                'South America' => ['ARG', 'BOL', 'BVT', 'BRA', 'CHL', 'COL', 'ECU', 'FLK', 'GUF', 'GUY', 'PRY', 'PER', 'SGS', 'SUR', 'URY', 'VEN'],
                'Africa' => ['DZA', 'AGO', 'BEN', 'BWA', 'IOT', 'BFA', 'BDI', 'CPV', 'CMR', 'CAF', 'TCD', 'COM', 'COG', 'CIV', 'COD', 'DJI', 'EGY', 'GNQ', 'ERI', 'SWZ', 'ETH', 'ATF', 'GAB', 'GMB', 'GHA', 'GIN', 'GNB', 'KEN', 'LSO', 'LBR', 'LBY', 'MDG', 'MWI', 'MLI', 'MRT', 'MUS', 'MYT', 'MAR', 'MOZ', 'NAM', 'NER', 'NGA', 'REU', 'RWA', 'SHN', 'STP', 'SEN', 'SYC', 'SLE', 'SOM', 'ZAF', 'SSD', 'SDN', 'TGO', 'TUN', 'UGA', 'TZA', 'ESH', 'ZMB', 'ZWE'],
                'Oceania' => ['ASM', 'AUS', 'CXR', 'CCK', 'COK', 'FJI', 'PYF', 'GUM', 'HMD', 'KIR', 'MHL', 'FSM', 'NRU', 'NCL', 'NZL', 'NIU', 'NFK', 'MNP', 'PLW', 'PNG', 'PCN', 'WSM', 'SLB', 'TKL', 'TON', 'TUV', 'UMI', 'VUT', 'WLF'],
                // 'Middle East' => [],
                'Default' => ['ATA']
            ];

            // Loop through the countries in the local array and assign them to the appropriate array
            foreach ($data['local'] as $country) {
                $region = $this->getRegionByCountryCode($country['isoCode'], $regionMappings);
                $dividedArrays[$region][] = $country;
            }

            // dd($dividedArrays);
            $data['global'][0]['countryImageURL'] = "/build/images/simly/Global_Icon.svg";
            $data['regional'][0]['countryImageURL'] = "/build/images/simly/AfricaIcon.svg";
            $data['regional'][1]['countryImageURL'] = "/build/images/simly/AsiaIcon.svg";
            $data['regional'][2]['countryImageURL'] = "/build/images/simly/EuropeIcon.svg";
            $data['regional'][3]['countryImageURL'] = "/build/images/simly/Middle_East_Icon.svg";
            $data['regional'][4]['countryImageURL'] = "/build/images/simly/North_America_Icon.svg";
            $data['regional'][5]['countryImageURL'] = "/build/images/simly/South_America_Icon.svg";

            foreach ($data['regional'] as $index => $regionalData) {
                // Check if 'isoCode' is not set, then assign an empty array
                if (!isset($regionalData['isoCode'])) {
                    $data['regional'][$index] = [];
                }
            }

            // Loop through 'global' array
            foreach ($data['global'] as $index => $globalData) {
                // Check if 'isoCode' is not set, then assign an empty array
                if (!isset($globalData['isoCode'])) {
                    $data['global'][$index] = [];
                }
            }

            // Remove empty arrays from 'regional'
            $data['regional'] = array_filter($data['regional']);

            // Remove empty arrays from 'global'
            $data['global'] = array_filter($data['global']);
            // Keep the 'regional' and 'global' arrays unchanged
            $filter = [
                'local' => $dividedArrays,
                'regional' => $data['regional'],
                'global' => $data['global']
            ];

            $jsonData = json_encode($filter);
            file_put_contents($file, $jsonData);
        }

        return $filter;
    }

    // Function to get the region by country code
    private function getRegionByCountryCode($isoCode, $regionMappings)
    {
        foreach ($regionMappings as $region => $isoCodes) {
            if (in_array($isoCode, $isoCodes)) {
                return $region;
            }
        }
        return 'Default';
    }

    public function getAllCountriesBySimlyFromSimly($simlyServices)
    {
        $file = ($_ENV['APP_ENV'] == 'prod') ? "../var/cache/prod/CountriesBySimlyFromSimly.txt" : (($_ENV['APP_ENV'] == 'test') ? "../var/cache/test/CountriesBySimlyFromSimly.txt" : (($_ENV['APP_ENV'] == 'sandbox') ? "../var/cache/sandbox/CountriesBySimlyFromSimly.txt" : "../var/cache/dev/CountriesBySimlyFromSimly.txt"));


        if (file_exists($file)) {
            $fileModificationTime = filemtime($file);
        } else {
            $fileModificationTime = 0;
        }

        $cacheExpiration = 5;
        $currentTime = time();

        if ($fileModificationTime + $cacheExpiration > $currentTime && filesize($file) > 0) {
            $operationsjson = file_get_contents($file);
            $filteredDatInOrder = json_decode($operationsjson, true);
        } else {
            $data = $simlyServices->GetAllAvailableCountriesOfContinent();
            $filteredData = [];

            foreach ($data as &$continent) {
                if (isset($continent['GLOBAL'])) {
                    continue;
                } else {
                    $filteredContinent = [];
                    foreach ($continent as $isoCodes => &$countries) {
                        $filteredCountries = [];
                        foreach ($countries as $country) {
                            if ($country['isoCode'] !== "ISR") {
                                $filteredCountries[] = $country;
                            }
                        }
                        if (!empty($filteredCountries)) {
                            $filteredContinent[$isoCodes] = $filteredCountries;
                        }
                    }
                    if (!empty($filteredContinent)) {
                        $filteredData[] = $filteredContinent;
                    }
                }
            }
            if (isset($filteredData[2]) && isset($filteredData[4]) && isset($filteredData[3]) && isset($filteredData[1]) && isset($filteredData[0]) && isset($filteredData[5])) {
                $b = array(2, 4, 3, 1, 0, 5);
                $filteredDatInOrder = array();
                foreach ($b as $index) {
                    $filteredDatInOrder[$index] = $filteredData[$index];
                }
            } else {
                $filteredDatInOrder = $filteredData;
            }
            //            $b = array(2, 4, 3, 1, 0, 5);
            //            $filteredDatInOrder = array();
            //            foreach ($b as $index) {
            //                $filteredDatInOrder[$index] = $filteredData[$index];
            //            }
            // dd(array_merge($c));
            // dd($filteredData);
            // dd($filteredDatInOrder);

            foreach($filteredDatInOrder as $index=>&$value){
               foreach($value as $index2=>$values){
                // dd($index2);
                usort($values, function($a, $b) {
                    return strcmp($a['name'],$b['name']);
                });
               }
                $filteredDatInOrder[$index][$index2] = $values;
            }
            // dd($filteredDatInOrder);
            $jsonData = json_encode(array_merge($filteredDatInOrder));
            // dd($jsonData);
            file_put_contents($file, $jsonData);
        }
        // dd($filteredDatInOrder);
        return array_merge($filteredDatInOrder);
    }
}
