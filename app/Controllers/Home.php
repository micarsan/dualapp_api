<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;

class Home extends BaseController
{
    use ResponseTrait;
    
    public function index()
    {
        //return view('welcome_message');
        $this->failUnauthorized();
    }
}
