<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LoginModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;

class Auth extends BaseController
{

    public function __construct()
    {
        $this->db = Database::connect();
        $this->session = session();
        $this->loginModel = new LoginModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Login',
        ];

        return view('auth/login', $data);
    }

    public function login()
    {
        $credentials = [
            'username' => $this->request->getVar('username'),
            'password' => $this->request->getVar('password'),
        ];

        $dataUser = $this->loginModel->where('username', $credentials['username'])->first();

        if (!$dataUser) {
            return redirect()->to('/login')->with('error', 'Username atau password salah!');
        }

        if (password_verify($credentials['password'], $dataUser['password'])) {
            $this->session->set('isLoggedIn', true);
            $this->session->set('level', $dataUser['status']);
            return redirect()->to('/');
        } else {
            return redirect()->to('/login')->with('error', 'Username atau password salah!');
        }
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/login');
    }
}
