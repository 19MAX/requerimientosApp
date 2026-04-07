<?php

namespace App\Controllers\Secretaria;

use App\Controllers\BaseController;
use App\Models\ClientsModel;

class ClientsController extends BaseController
{
    protected $clientsModel;

    public function __construct()
    {
        $this->clientsModel = new ClientsModel();
    }
    public function index()
    {
        $clients = $this->clientsModel->findAll();

        return view('secretaria/clients/index', [
            'clients' => $clients
        ]);
    }

    public function create()
    {
        try {
            // Extraemos solo los campos permitidos del POST
            $data = $this->request->getPost(['first_name', 'last_name', 'cedula', 'email', 'phone', 'address']);

            // El modelo valida, formatea (por tu callback) e inserta automáticamente
            if ($this->clientsModel->insert($data) === false) {
                // Si la validación falla, obtenemos los errores del modelo y los convertimos en un string
                $errores = implode('<br>', $this->clientsModel->errors());

                return redirect()->back()->withInput()->with('error', [
                    'text' => $errores,
                    'position' => 'center'
                ]);
            }

            return redirect()->to('secretaria/clients')->with('success', [
                'text' => 'Cliente creado correctamente',
                'position' => 'top-end'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en ClientsController::create: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', [
                'text' => 'Ocurrió un error al crear el cliente',
                'position' => 'center'
            ]);
        }
    }

    public function update()
    {
        try {
            $id = $this->request->getPost('id');

            // SOLUCIÓN: Agregamos 'id' al arreglo de campos que recibimos del POST
            $data = $this->request->getPost(['id', 'first_name', 'last_name', 'cedula', 'email', 'phone', 'address']);

            // Ahora el modelo encontrará $data['id'] y la validación is_unique funcionará perfecto
            if ($this->clientsModel->update($id, $data) === false) {
                $errores = implode('<br>', $this->clientsModel->errors());
                return redirect()->back()->withInput()->with('error', [
                    'text' => $errores,
                    'position' => 'center'
                ]);
            }

            return redirect()->to('secretaria/clients')->with('success', [
                'text' => 'Cliente actualizado correctamente',
                'position' => 'top-end'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en ClientsController::update: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', [
                'text' => 'Ocurrió un error al actualizar el cliente',
                'position' => 'center'
            ]);
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id');

        // Validación básica de seguridad
        if (!$id || !is_numeric($id)) {
            return redirect()->to('secretaria/clients')->with('error', [
                'text' => 'ID de cliente inválido',
                'position' => 'center'
            ]);
        }

        $this->clientsModel->delete($id);

        return redirect()->to('secretaria/clients')->with('success', [
            'text' => 'Cliente eliminado correctamente',
            'position' => 'top-end'
        ]);
    }

}
