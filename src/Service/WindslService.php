<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Utils\Helper;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class WindslService
{
    private $WIN_DSL_HOST;
    private $client;
    private $username;
    private $password;
    private $METHOD_POST;
    private $helper;
    private $loggerInterface;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params, Helper $helper, LoggerInterface $loggerInterface)
    {
        $this->client = $client;
        $this->WIN_DSL_HOST = 'https://adminportal.win-dsl.com/api/v1/';
        $this->METHOD_POST = $params->get('METHOD_POST');
        $this->helper = $helper;
        $this->loggerInterface = $loggerInterface;
        $this->username = $_ENV['WINDSLUSERNAME'];
        $this->password = $_ENV['WINDSLPASSWORD'];
    }

    public function login($username, $password)
    {
        // $formData = new FormDataPart([
        //     'username' => $username,
        //     'password' => $password
        // ]);

        $body = [
            'username'=>$username,
            'password'=>$password
        ];

        $response = $this->client->request('POST', $this->WIN_DSL_HOST . "authenticate_user", [
            'body' => $body,
            'auth_basic' => [$this->username, $this->password],
        ]);
        $content = $response->toArray(false);
        // dd($content);

        if($content[0] == "success"){
            return array(true,$content[1],json_encode($content),json_encode($body),$this->WIN_DSL_HOST . "authenticate_user",$response->getStatusCode());
        }

        return array(false,@$content[1],json_encode($content),json_encode($body),$this->WIN_DSL_HOST . "authenticate_user",$response->getStatusCode());
    }

    public function checkBalance($userid)
    {
        $response = $this->client->request('GET', $this->WIN_DSL_HOST . "get_balance/{$userid}", [
            'auth_basic' => [$this->username, $this->password],
        ]);

        $content = $response->toArray(false);
        if($content['status'] && !is_null($content['username'])){
            return array(true,$content['balance'],json_encode($content), $this->WIN_DSL_HOST . "get_balance/{$userid}",$response->getStatusCode());
        }
        return array(false,null,json_encode($content), $this->WIN_DSL_HOST . "get_balance/{$userid}",$response->getStatusCode());
    }

    public function topup($userid,$amount,$currency)
    {
        $body = [
            'userId'=>$userid,
            'amount'=>$amount,
            'method'=>'cash',
        ];

        $response = $this->client->request('POST', $this->WIN_DSL_HOST . "make_payment", [
            'body' => $body,
            'auth_basic' => [$this->username, $this->password],
        ]);

        $content = $response->toArray(false);
        if($content['status'] == "success"){
            $checkbalance = $this->checkBalance($userid);
            return array(true,json_encode($body),json_encode($content),$this->WIN_DSL_HOST . "make_payment",$response->getStatusCode(),$checkbalance[1]);
        }

            return array(false,json_encode($body),json_encode($content),$this->WIN_DSL_HOST . "make_payment",$response->getStatusCode());


    }
}
