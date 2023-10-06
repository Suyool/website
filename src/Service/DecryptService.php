<?php

namespace App\Service;

class DecryptService
{
   
    public static function decrypt($stringToDecrypt){
        $decrypted_string = openssl_decrypt($stringToDecrypt, $_ENV['CIPHER_ALGORITHME'], $_ENV['DECRYPT_KEY'], 0, $_ENV['INITIALLIZATION_VECTOR']);
        return $decrypted_string;
    }

}
