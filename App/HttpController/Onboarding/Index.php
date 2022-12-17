<?php

namespace App\HttpController\Onboarding;

use App\HttpController\Auth;

class Index extends Auth
{

    public function hello() {

        $this->writeRet();
    }

    public function complete() {
        $form = $this->getForm();
        $username = $form["username"];


    }
}