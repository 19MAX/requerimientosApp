<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingsModel;
use CodeIgniter\HTTP\ResponseInterface;

class SettingsController extends BaseController
{
    protected $settingsModel;

    public function __construct()
    {
        $this->settingsModel = new SettingsModel();
        helper('form');
    }

    public function index()
    {
        // Siempre llamamos al registro con ID 1 (el que creamos en la migración)
        $settings = $this->settingsModel->find(1);

        return view('admin/settings/index', [
            'settings' => $settings
        ]);
    }

    public function update()
    {
        // 0. TRAMPA DE SEGURIDAD: Detectar si el archivo excedió el límite absoluto del servidor (php.ini)
        // Si hay un peso en la petición, pero $_POST y $_FILES están vacíos, PHP bloqueó la subida.
        if (isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > 0 && empty($_POST) && empty($_FILES)) {
            return redirect()->back()->with('error', [
                'text' => 'El archivo seleccionado es exageradamente pesado y el servidor no puede procesarlo. Por favor, sube una imagen más ligera (Máx 2MB).',
                'position' => 'center'
            ]);
        }

        // 1. Validaciones extra para la IMAGEN en el controlador
        $rules = [
            'site_name' => 'required',
            'site_logo' => [
                'label' => 'Logo del Sitio',
                // max_size[site_logo,2048] atrapará los archivos que pesen entre 2MB y 8MB
                'rules' => 'max_size[site_logo,2048]|is_image[site_logo]|ext_in[site_logo,png,jpg,jpeg,webp,svg]'
            ]
        ];

        if (!$this->validate($rules)) {
            $errores = implode('<br>', $this->validator->getErrors());
            return redirect()->back()->withInput()->with('error', [
                'text' => $errores,
                'position' => 'center'
            ]);
        }

        // 2. Preparamos los datos básicos (el modelo validará el site_name)
        $data = [
            'site_name' => $this->request->getPost('site_name')
        ];

        // 3. Lógica para procesar la subida del archivo (Logo)
        $fileLogo = $this->request->getFile('site_logo');

        if ($fileLogo && $fileLogo->isValid() && !$fileLogo->hasMoved()) {

            // Generamos un nombre aleatorio seguro (ej. 1629384729_a7b8c9.png)
            $newName = $fileLogo->getRandomName();

            // Movemos el archivo a la carpeta public/uploads/logos/
            $fileLogo->move(FCPATH . 'uploads/logos', $newName);

            // Agregamos el nuevo nombre al arreglo para guardarlo en la Base de Datos
            $data['site_logo'] = $newName;

            // (Opcional) Borrar el logo viejo del servidor para no acumular basura
            $oldSettings = $this->settingsModel->find(1);
            if (!empty($oldSettings['site_logo']) && file_exists(FCPATH . 'uploads/logos/' . $oldSettings['site_logo'])) {
                unlink(FCPATH . 'uploads/logos/' . $oldSettings['site_logo']);
            }
        }

        // 4. Actualizar en la base de datos (Siempre en el ID 1)
        if ($this->settingsModel->update(1, $data) === false) {
            $errores = implode('<br>', $this->settingsModel->errors());
            return redirect()->back()->withInput()->with('error', [
                'text' => $errores,
                'position' => 'center'
            ]);
        }
        // --- ¡NUEVO! ---
        // Destruimos la caché vieja para que el BaseController se vea obligado a consultar la BD y crear una nueva
        \Config\Services::cache()->delete('global_settings');
        // ---------------

        // 5. Retornar con éxito estilo Toast de SweetAlert2
        return redirect()->to('admin/settings')->with('success', [
            'text' => 'La configuración del sistema ha sido actualizada.',
            'position' => 'top-end'
        ]);
    }
}
