<?php

namespace App\Controllers;

class TestAdminLTE extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Test AdminLTE'
        ];
        
        return view('admin', $data);
    }
}