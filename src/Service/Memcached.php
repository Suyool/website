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
                        $item['beforeTaxes']=(float)explode("$",$item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format($item['priceUSD'] - (float)$item['beforeTaxes'],2,'.');
                        $item['sayrafa']=$item['priceLBP'] / $item['priceUSD'];
                        $item['image']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/alfa/bundleImg{$item['vouchertype']}h.png";
                        $item['icon']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/alfa/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 13:
                        $item['desc1'] = "$3.02 Alfa recharge card";
                        $item['desc2'] = "Credit and 13 Days Validity";
                        $item['desc3'] = "Credit and 13 Days Validity";
                        // $item['priceUSD']="3.02";
                        $item['beforeTaxes']=(float)explode("$",$item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format((float)$item['priceUSD'] - (float)$item['beforeTaxes'],2,'.');
                        $item['sayrafa']=$item['priceLBP'] / $item['priceUSD'];
                        $item['image']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/alfa/bundleImg{$item['vouchertype']}h.png";
                        $item['icon']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/alfa/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 4:
                        $item['desc1'] = "$4.50 Alfa recharge card";
                        $item['desc2'] = "Credit and up to 35 Days";
                        $item['desc3'] = "Credit and up to 35 Days";
                        $item['beforeTaxes']=(float)explode("$",$item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format((float)$item['priceUSD'] - (float)$item['beforeTaxes'],2,'.');
                        $item['sayrafa']=$item['priceLBP'] / $item['priceUSD'];
                        $item['image']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/alfa/bundleImg{$item['vouchertype']}h.png";
                        $item['icon']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/alfa/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 35:
                        $item['desc1'] = "$7.58 Alfa recharge card";
                        $item['desc2'] = "Credit and 35 Days Validity";
                        $item['desc3'] = "Credit and 35 Days Validity";
                        $item['beforeTaxes']=(float)explode("$",$item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format((float)$item['priceUSD'] - (float)$item['beforeTaxes'],2,'.');
                        $item['sayrafa']=$item['priceLBP'] / $item['priceUSD'];
                        $item['image']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/alfa/bundleImg{$item['vouchertype']}h.png";
                        $item['icon']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/alfa/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 65:
                        $item['desc1'] = "$15.15 Alfa recharge card";
                        $item['desc2'] = "Credit and 65 Days Validity";
                        $item['desc3'] = "Credit and 65 Days Validity";
                        $item['beforeTaxes']=(float)explode("$",$item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format((float)$item['priceUSD'] - (float)$item['beforeTaxes'],2,'.');
                        $item['sayrafa']=$item['priceLBP'] / $item['priceUSD'];
                        $item['image']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/alfa/bundleImg{$item['vouchertype']}h.png";
                        $item['icon']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/alfa/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 95:
                        $item['desc1'] = "$22.73 Alfa recharge card";
                        $item['desc2'] = "Credit and 95 Days Validity";
                        $item['desc3'] = "Credit and 95 Days Validity";
                        $item['beforeTaxes']=(float)explode("$",$item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format((float)$item['priceUSD'] - (float)$item['beforeTaxes'],2,'.');
                        $item['sayrafa']=$item['priceLBP'] / $item['priceUSD'];
                        $item['image']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/alfa/bundleImg{$item['vouchertype']}h.png";
                        $item['icon']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/alfa/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 32:
                        $item['desc1'] = "$7.50 Alfa recharge card";
                        $item['desc2'] = "Waffer Credit and 30 Days Validity";
                        $item['desc3'] = "Waffer Credit and 30 Days Validity";
                        $item['beforeTaxes']=(float)explode("$",explode(" ",$item['desc'])[1])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format((float)$item['priceUSD'] - (float)$item['beforeTaxes'],2,'.');
                        $item['sayrafa']=$item['priceLBP'] / $item['priceUSD'];
                        $item['image']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/alfa/bundleImg{$item['vouchertype']}h.png";
                        $item['icon']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/alfa/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 33:
                        $item['desc1'] = "Waffer Credit and 30 Days Validity";
                        $item['desc2'] = "Waffer Credit and 30 Days Validity";
                        $item['desc3'] = "Waffer Credit and 30 Days Validity";
                        $item['beforeTaxes']=(float)explode("$",explode(" ",$item['desc'])[1])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format((float)$item['priceUSD'] - (float)$item['beforeTaxes'],2,'.');
                        $item['sayrafa']=$item['priceLBP'] / $item['priceUSD'];
                        $item['image']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/alfa/bundleImg{$item['vouchertype']}h.png";
                        $item['icon']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/alfa/Bundle{$item['vouchertype']}h.png";
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
                        $item['beforeTaxesPrice']="SOS Start $" . (float)explode("$",$item['desc'])[0];;
                        $item['beforeTaxes']=(float)explode("$",$item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format($item['priceUSD'] - (float)$item['beforeTaxes'],2,'.');
                        $item['sayrafa']=$item['priceLBP'] / $item['priceUSD'];
                        $item['image']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/Touch/bundleImg{$item['vouchertype']}h.png";
                        $item['icon']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/Touch/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 10:
                        $item['desc1'] = "10 Days Validity & 5 Days Grace";
                        $item['desc2'] = "Credit and 13 Days Validity";
                        $item['desc3'] = "$3.79 Touch recharge card";
                        // $item['priceUSD']="3.79";
                        $item['beforeTaxesPrice']="$" .(float)explode("$",$item['desc'])[0];
                        $item['beforeTaxes']=(float)explode("$",$item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format($item['priceUSD'] - (float)$item['beforeTaxes'],2,'.');
                        $item['sayrafa']=$item['priceLBP'] / $item['priceUSD'];
                        $item['image']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/Touch/bundleImg{$item['vouchertype']}h.png";
                        $item['icon']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/Touch/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 29:
                        $item['desc1'] = "30 Days Validity";
                        $item['desc2'] = "Credit and up to 35 Days";
                        $item['desc3'] = "$4.50 Touch recharge card";
                        // $item['priceUSD']="4.50";
                        $item['beforeTaxesPrice']="$" .(float)explode("$",$item['desc'])[0];
                        $item['beforeTaxes']=(float)explode("$",$item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format($item['priceUSD'] - (float)$item['beforeTaxes'],2,'.');
                        $item['sayrafa']=$item['priceLBP'] / $item['priceUSD'];
                        $item['image']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/Touch/bundleImg{$item['vouchertype']}h.png";
                        $item['icon']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/Touch/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 30:
                        $item['desc1'] = "30 Days Validity & 5 Days Grace";
                        $item['desc2'] = "Credit and 35 Days Validity";
                        $item['desc3'] = "$7.58 Touch recharge card";
                        // $item['priceUSD']="7.58";
                        $item['beforeTaxesPrice']="$" .(float)explode("$",$item['desc'])[0];
                        $item['beforeTaxes']=(float)explode("$",$item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format($item['priceUSD'] - (float)$item['beforeTaxes'],2,'.');
                        $item['sayrafa']=$item['priceLBP'] / $item['priceUSD'];
                        $item['image']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/Touch/bundleImg{$item['vouchertype']}h.png";
                        $item['icon']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/Touch/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 60:
                        $item['desc1'] = "60 Days Validity & 5 Days Grace";
                        $item['desc2'] = "Credit and 65 Days Validity";
                        $item['desc3'] = "$15.15 Touch recharge card";
                        // $item['priceUSD']="15.15";
                        $item['beforeTaxesPrice']="$" .(float)explode("$",$item['desc'])[0];
                        $item['beforeTaxes']=(float)explode("$",$item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format($item['priceUSD'] - (float)$item['beforeTaxes'],2,'.');
                        $item['sayrafa']=$item['priceLBP'] / $item['priceUSD'];
                        $item['image']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/Touch/bundleImg{$item['vouchertype']}h.png";
                        $item['icon']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/Touch/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 90:
                        $item['desc1'] = "90 Days Validity & 5 Days Grace";
                        $item['desc2'] = "Credit and 95 Days Validity";
                        $item['desc3'] = "$22.73 Touch recharge card";
                        // $item['priceUSD']="22.73";
                        $item['beforeTaxesPrice']="$" .(float)explode("$",$item['desc'])[0];
                        $item['beforeTaxes']=(float)explode("$",$item['desc'])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format($item['priceUSD'] - (float)$item['beforeTaxes'],2,'.');
                        $item['sayrafa']=$item['priceLBP'] / $item['priceUSD'];
                        $item['image']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/Touch/bundleImg{$item['vouchertype']}h.png";
                        $item['icon']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/Touch/Bundle{$item['vouchertype']}h.png";
                        break;
                    case 31:
                        $item['desc1'] = "30 Days Validity";
                        $item['desc2'] = "Waffer Credit and 30 Days Validity";
                        $item['desc3'] = "$4.50 Touch recharge card";
                        // $item['priceUSD']="4.50";
                        $item['beforeTaxesPrice']="Start $" . (float)explode("$",explode(" ",$item['desc'])[1])[0];
                        $item['beforeTaxes']=(float)explode("$",explode(" ",$item['desc'])[1])[0];
                        // $item['priceUSDaftertaxes']="1.37";
                        $item['fees'] = number_format((float)$item['priceUSD'] - (float)$item['beforeTaxes'],2,'.');
                        $item['sayrafa']=$item['priceLBP'] / $item['priceUSD'];
                        $item['image']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/Touch/bundleImg{$item['vouchertype']}h.png";
                        $item['icon']= (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']."/build/images/Touch/Bundle{$item['vouchertype']}h.png";
                        break;
                    default:
                        $item['desc1'] = "default";
                        $item['desc2'] = "default";
                        $item['desc3'] = "default";
                        $item['priceUSD']="default";
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
}
