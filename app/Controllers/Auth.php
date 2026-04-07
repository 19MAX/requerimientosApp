<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    protected $rolesModel;
    protected $userModel;

    public function __construct()
    {
        $this->rolesModel = new \App\Models\RolesModel();
        $this->userModel = new \App\Models\UsersModel();
    }

    public function login()
    {
        // Si el usuario ya está logeado, lo redirigimos a su panel correspondiente
        if (session()->get('is_logged_in')) {
            return $this->redirectByRole(session()->get('role_slug'));
        }

        return view('auth/login');
    }

    public function loginProcess()
    {
        $session = session();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            $session->setFlashdata('error', 'Credenciales incorrectas.');
            return redirect()->to('login')->withInput();
        }

        if ($user['is_active'] == 0) {
            $session->setFlashdata('error', 'Tu cuenta está inactiva. Contacta a administración.');
            return redirect()->to('login');
        }

        if (password_verify($password, $user['password'])) {

            $role = $this->rolesModel->where('id', $user['role_id'])->first();
            $roleSlug = $role ? $role['slug'] : '';

            $sessionData = [
                'user_id'      => $user['id'],
                'role_id'      => $user['role_id'],
                'role_slug'    => $roleSlug,
                'name'         => $user['name'],
                'email'        => $user['email'],
                'is_logged_in' => true
            ];

            $session->set($sessionData);

            $this->userModel->update($user['id'], [
                'last_login_at' => date('Y-m-d H:i:s')
            ]);

            // Redirigir usando el helper switch
            return $this->redirectByRole($roleSlug);
        }

        $session->setFlashdata('error', 'Credenciales incorrectas.');
        return redirect()->to('login')->withInput();
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }

    /**
     * Helper para redirigir según el rol
     */
    private function redirectByRole($roleSlug)
    {
        switch ($roleSlug) {
            case 'admin':
                return redirect()->to('admin');
            case 'secretaria':
                return redirect()->to('secretaria');
            case 'director':
                return redirect()->to('director');
            case 'lider_area':
                return redirect()->to('lider');
            default:
                session()->destroy();
                return redirect()->to('login')->with('error', 'Rol no reconocido.');
        }
    }
}
