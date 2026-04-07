<?php

namespace App\Controllers\Secretaria;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    public function index()
    {
        return view('secretaria/dashboard');
    }
}
