<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AssignmentModel;
use App\Models\DocumentModel;
use App\Models\LeaderCategoryModel;
use App\Models\RolesModel;
use App\Models\UsersModel;

class UsersController extends BaseController
{
    protected $userModel;
    protected $rolesModel;
    protected $leaderCategoryModel;
    protected $documentModel;
    protected $assignmentModel;

    public function __construct()
    {
        $this->userModel = new UsersModel();
        $this->rolesModel = new RolesModel();
        $this->leaderCategoryModel = new LeaderCategoryModel();
        $this->documentModel = new DocumentModel();
        $this->assignmentModel = new AssignmentModel();
    }

    public function index()
    {
        $users = $this->userModel->select('users.*, roles.name as role_name, lc.name as category_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->join('leader_categories lc', 'lc.id = users.leader_category_id', 'left')
            ->where('users.role_id !=', 1)
            ->findAll();

        $roles = $this->rolesModel->select('id, name')->findAll();
        $categories = $this->leaderCategoryModel->findAll();
        $liderAreaRole = $this->rolesModel->where('slug', 'lider_area')->first();

        return view('admin/users/index', [
            'users' => $users,
            'roles' => $roles,
            'categories' => $categories,
            'liderAreaRoleId' => $liderAreaRole['id'] ?? null,
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
                'password'
            ]);

            // Solo agregar category si es líder
            if ($data['role_id'] == 4) {

                $leaderCategoryId = $this->request->getPost('leader_category_id');

                if (empty($leaderCategoryId)) {
                    return redirect()->back()->withInput()->with('error', [
                        'text' => 'Debe seleccionar una categoría para el líder.',
                        'position' => 'center'
                    ]);
                }

                $data['leader_category_id'] = $leaderCategoryId;

            } else {

                // Para otros roles mandamos null
                $data['leader_category_id'] = null;
            }

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

            $guardError = $this->assertCanModifyTarget((int) $id, $this->request->getPost());
            if ($guardError !== null) {
                return redirect()->back()->withInput()->with('error', [
                    'text' => $guardError,
                    'position' => 'center',
                ]);
            }

            $data = $this->request->getPost([
                'id',
                'name',
                'email',
                'phone',
                'role_id',
                'is_active'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Validar categoría solo para líderes
            |--------------------------------------------------------------------------
            */

            if ($data['role_id'] == 4) {

                $leaderCategoryId = $this->request->getPost('leader_category_id');

                if (empty($leaderCategoryId)) {

                    return redirect()->back()->withInput()->with('error', [
                        'text' => 'Debe seleccionar una categoría para el líder.',
                        'position' => 'center'
                    ]);
                }

                $data['leader_category_id'] = $leaderCategoryId;

            } else {

                // Si NO es líder eliminamos la categoría
                $data['leader_category_id'] = null;
            }

            /*
            |--------------------------------------------------------------------------
            | Password opcional
            |--------------------------------------------------------------------------
            */

            $password = $this->request->getPost('password');

            if (!empty($password)) {
                $data['password'] = $password;
            }

            /*
            |--------------------------------------------------------------------------
            | Actualizar usuario
            |--------------------------------------------------------------------------
            */

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

    // public function delete()
    // {
    //     $id = $this->request->getPost('id');

    //     if (!$id || !is_numeric($id)) {
    //         return redirect()->to('admin/users')->with('error', [
    //             'text' => 'ID de usuario inválido',
    //             'position' => 'center'
    //         ]);
    //     }

    //     if ($id == session()->get('user_id')) {
    //         return redirect()->to('admin/users')->with('error', [
    //             'text' => 'No puedes eliminar tu propio usuario.',
    //             'position' => 'center'
    //         ]);
    //     }

    //     $user = $this->userModel->find($id);
    //     if (!$user) {
    //         return redirect()->to('admin/users')->with('error', [
    //             'text' => 'Usuario no encontrado',
    //             'position' => 'center'
    //         ]);
    //     }

    //     $docsAsLeader = $this->documentModel
    //         ->where('reviewed_by', $id)
    //         ->countAllResults();

    //     $assignmentsActive = $this->assignmentModel
    //         ->where('assigned_to', $id)
    //         ->whereNotIn('status', ['cancelada'])
    //         ->countAllResults();

    //     if ($docsAsLeader > 0 || $assignmentsActive > 0) {
    //         $messages = [];
    //         if ($docsAsLeader > 0) {
    //             $messages[] = "{$docsAsLeader} documento(s) donde aparece como director";
    //         }
    //         if ($assignmentsActive > 0) {
    //             $messages[] = "{$assignmentsActive} asignación(es) donde aparece como líder";
    //         }

    //         return redirect()->to('admin/users')->with('error', [
    //             'text' => 'No se puede eliminar el usuario. Hay ' . implode(' y ', $messages) . '.',
    //             'position' => 'center'
    //         ]);
    //     }

    //     $this->userModel->delete($id);

    //     return redirect()->to('admin/users')->with('success', [
    //         'text' => 'Usuario eliminado correctamente',
    //         'position' => 'top-end'
    //     ]);
    // }

    private function countActiveAdmins(): int
    {
        return $this->userModel->where('role_id', 1)->where('is_active', 1)->countAllResults();
    }

    private function assertCanModifyTarget(int $targetId, ?array $post = null): ?string
    {
        $currentUserId = (int) session()->get('user_id');
        $currentRoleId = (int) session()->get('role_id');
        $isSelf        = ($targetId === $currentUserId);

        $target = $this->userModel->find($targetId);
        if (!$target) {
            return 'Usuario no encontrado.';
        }
        $targetRoleId = (int) $target['role_id'];

        // Guard 1: lateral block — no admin may modify ANOTHER admin. Self-edit allowed.
        if ($targetRoleId === 1 && !$isSelf) {
            log_message('warning', "UsersController: admin user_id={$currentUserId} attempted to modify other admin user_id={$targetId}");
            return 'No puedes modificar a otro administrador.';
        }

        // Guards 2 & 3: last-admin self-lockout. Skipped if other admins exist.
        if ($isSelf && $currentRoleId === 1 && $this->countActiveAdmins() === 1) {
            // Guard 2: no self-deactivation when last.
            $newIsActive = $post['is_active'] ?? null;
            if ($newIsActive !== null && (int) $newIsActive === 0) {
                log_message('warning', "UsersController: last active admin user_id={$currentUserId} attempted self-deactivation");
                return 'No puedes desactivarte siendo el último administrador activo.';
            }
            // Guard 3: no self-demotion when last.
            $newRoleId = $post['role_id'] ?? null;
            if ($newRoleId !== null && (int) $newRoleId !== 1) {
                log_message('warning', "UsersController: last active admin user_id={$currentUserId} attempted self-demotion to role_id={$newRoleId}");
                return 'No puedes cambiar tu rol siendo el último administrador activo.';
            }
        }

        return null;
    }
}
