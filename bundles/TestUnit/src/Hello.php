<?php

namespace TestUnit;

use Symfony\Component\HttpFoundation\JsonResponse;

class Hello{
    public function test(){
        return new JsonResponse([
            'status'=>true,
            "message"=>'Congratulation For my first bundle'
        ]);
    }
}