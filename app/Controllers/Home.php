<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $this->trace('index', 'ENTER | public landing page');
        return view('welcome_message');
    }
}
