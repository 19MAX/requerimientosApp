<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class ProfileController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        helper('form');
        $this->userModel = new UsersModel();
    }

    /**
     * Muestra la vista del perfil con los datos del usuario logueado
     */
    public function index()
    {
        // Asumiendo que guardas el ID del usuario en la sesión al hacer login
        $userId = session()->get('user_id');

        // Obtenemos los datos frescos de la base de datos
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('login')->with('error', 'Usuario no encontrado.');
        }

        return view('admin/profile/index', [
            'user' => $user
        ]);
    }

    /**
     * Actualiza la información básica delegando la validación al UsersModel
     */
    public function updateInfo()
    {
        try {
            $userId = session()->get('user_id');

            $data = [
                'id' => $userId,
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'phone' => $this->request->getPost('phone'),
            ];

            if ($this->userModel->update($userId, $data) === false) {
                $errores = implode('<br>', $this->userModel->errors());

                return redirect()->back()->withInput()->with('error', [
                    'text' => $errores,
                    'position' => 'center'
                ]);
            }

            session()->set('name', $data['name']);
            session()->set('email', $data['email']);

            return redirect()->to('admin/profile')->with('success', [
                'text' => 'Tu información básica ha sido actualizada.',
                'position' => 'top-end'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en ProfileController::updateInfo: ' . $e->getMessage());

            return redirect()->back()->withInput()->with('error', [
                'text' => 'Ocurrió un error al actualizar tu información.',
                'position' => 'center'
            ]);
        }
    }

    /**
     * Actualiza la contraseña combinando validación del Controlador y del Modelo
     */
    public function updatePassword()
    {
        try {
            $userId = session()->get('user_id');
            $user = $this->userModel->find($userId);

            $rules = [
                'current_password' => [
                    'label' => 'Contraseña Actual',
                    'rules' => 'required'
                ],
                'password_confirm' => [
                    'label' => 'Confirmación de Contraseña',
                    'rules' => 'required|matches[password]',
                ]
            ];

            if (!$this->validate($rules)) {
                $errores = implode('<br>', $this->validator->getErrors());
                return redirect()->back()->with('error', [
                    'text' => $errores,
                    'position' => 'center'
                ]);
            }

            $currentPassword = $this->request->getPost('current_password');
            $newPassword = $this->request->getPost('password');

            if (!password_verify($currentPassword, $user['password'])) {
                return redirect()->back()->with('error', [
                    'text' => 'La contraseña actual ingresada es incorrecta.',
                    'position' => 'center'
                ]);
            }

            $data = [
                'id' => $userId,
                'password' => $newPassword
            ];

            // 4. Actualizamos en la BD
            if ($this->userModel->update($userId, $data) === false) {
                $errores = implode('<br>', $this->userModel->errors());
                return redirect()->back()->with('error', $errores);
            }

            return redirect()->to('admin/profile')->with('success', [
                'text' => 'Tu contraseña ha sido actualizada con éxito.',
                'position' => 'center'
            ]);

        } catch (\Exception $e) {

            log_message('error', 'Error en ProfileController::updatePassword: ' . $e->getMessage());

            return redirect()->back()->with('error', [
                'text' => 'Ocurrió un error al actualizar tu contraseña.',
                'position' => 'center'
            ]);
        }
    }
}
