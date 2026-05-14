<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LeaderCategoryModel;

class LeaderCategoriesController extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new LeaderCategoryModel();
    }

    public function index()
    {
        $categories = $this->categoryModel->orderBy('name', 'asc')->findAll();

        return view('admin/leader_categories/index', [
            'categories' => $categories
        ]);
    }

    public function create()
    {
        try {
            $data = $this->request->getPost([
                'name',
                'slug',
                'description'
            ]);

            if ($this->categoryModel->insert($data) === false) {
                $errores = implode('<br>', $this->categoryModel->errors());
                return redirect()->back()->withInput()->with('error', [
                    'text' => $errores,
                    'position' => 'center'
                ]);
            }

            return redirect()->to('admin/leader-categories')->with('success', [
                'text' => 'Categoría creada correctamente',
                'position' => 'top-end'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en LeaderCategoriesController::create: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', [
                'text' => 'Ocurrió un error al crear la categoría',
                'position' => 'center'
            ]);
        }
    }

    public function update()
    {
        try {
            $id = $this->request->getPost('id');

            $data = $this->request->getPost([
                'id',
                'name',
                'slug',
                'description'
            ]);

            if ($this->categoryModel->update($id, $data) === false) {
                $errores = implode('<br>', $this->categoryModel->errors());
                return redirect()->back()->withInput()->with('error', [
                    'text' => $errores,
                    'position' => 'center'
                ]);
            }

            return redirect()->to('admin/leader-categories')->with('success', [
                'text' => 'Categoría actualizada correctamente',
                'position' => 'top-end'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en LeaderCategoriesController::update: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', [
                'text' => 'Ocurrió un error al actualizar la categoría',
                'position' => 'center'
            ]);
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id');

        if (!$id || !is_numeric($id)) {
            return redirect()->to('admin/leader-categories')->with('error', [
                'text' => 'ID de categoría inválido',
                'position' => 'center'
            ]);
        }

        $this->categoryModel->delete($id);

        return redirect()->to('admin/leader-categories')->with('success', [
            'text' => 'Categoría eliminada correctamente',
            'position' => 'top-end'
        ]);
    }
}