<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LeaderCategoryModel;
use App\Models\RolesModel;
use App\Models\UsersModel;

class UsersController extends BaseController
{
    protected $userModel;
    protected $rolesModel;
    protected $leaderCategoryModel;

    public function __construct()
    {
        $this->userModel = new UsersModel();
        $this->rolesModel = new RolesModel();
        $this->leaderCategoryModel = new LeaderCategoryModel();
    }

    public function index()
    {
        $users = $this->userModel->select('users.*, roles.name as role_name, lc.name as category_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->join('leader_categories lc', 'lc.id = users.leader_category_id', 'left')
            ->where('users.role_id !=', 1)
            ->findAll();

        $roles = $this->rolesModel->select('id, name')->where('id !=', 1)->findAll();
        $categories = $this->leaderCategoryModel->findAll();
        $liderAreaRole = $this->rolesModel->where('slug', 'lider_area')->first();

        return view('admin/users/index', [
            'users' => $users,
            'roles' => $roles,
            'categories' => $categories,
            'liderAreaRoleId' => $liderAreaRole['id'] ?? null
        ]);
    }

    public function create()
    {
        try {
            $data = $this->request->getPost([
                'name',
                'email',
                'phone',
                'role_id',
                'leader_category_id',
                'password'
            ]);
            $data['is_active'] = 1;

            if ($this->userModel->insert($data) === false) {
                $errores = implode('<br>', $this->userModel->errors());
                return redirect()->back()->withInput()->with('error', [
                    'text' => $errores,
                    'position' => 'center'
                ]);
            }

            return redirect()->to('admin/users')->with('success', [
                'text' => 'Usuario creado correctamente',
                'position' => 'top-end'
            ]);
        } catch (\Exception $e) {

            log_message('error', 'Error en UsersController::create: ' . $e->getMessage());

            return redirect()->back()->withInput()->with('error', [
                'text' => 'Ocurrió un error al crear el usuario',
                'position' => 'center'
            ]);
        }
    }

    public function update()
    {
        try {
            $id = $this->request->getPost('id');

            // Incluimos 'id' para que {id} funcione en is_unique, igual que en ClientsController
            $data = $this->request->getPost([
                'id',
                'name',
                'email',
                'phone',
                'role_id',
                'leader_category_id',
                'is_active'
            ]);

            // Si password viene vacía la excluimos del array:
            // cleanValidationRules la ignorará y hashPassword no la tocará
            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $data['password'] = $password;
            }

            if ($this->userModel->update($id, $data) === false) {
                $errores = implode('<br>', $this->userModel->errors());
                return redirect()->back()->withInput()->with('error', [
                    'text' => $errores,
                    'position' => 'center'
                ]);
            }

            return redirect()->to('admin/users')->with('success', [
                'text' => 'Usuario actualizado correctamente',
                'position' => 'top-end'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en UsersController::update: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', [
                'text' => 'Ocurrió un error al actualizar el usuario',
                'position' => 'center'
            ]);
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id');

        // Validación básica de seguridad
        if (!$id || !is_numeric($id)) {
            return redirect()->to('admin/users')->with('error', [
                'text' => 'ID de usuario inválido',
                'position' => 'center'
            ]);
        }

        // Evitar que el usuario logeado se elimine a sí mismo
        if ($id == session()->get('user_id')) {
            return redirect()->to('admin/users')->with('error',[
                'text' => 'No puedes eliminar tu propio usuario.',
                'position' => 'center'
            ]);
        }

        // Como tienes useSoftDeletes = true en tu modelo, esto hará un borrado lógico (llenará deleted_at)
        $this->userModel->delete($id);

        return redirect()->to('admin/users')->with('success', [
            'text' => 'Usuario eliminado correctamente',
            'position' => 'top-end'
        ]);
    }
}
