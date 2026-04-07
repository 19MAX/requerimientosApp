<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
        // 1. Iniciamos el servicio de caché
        $cache = \Config\Services::cache();

        // 2. Intentamos obtener la configuración de la caché
        $globalSettings = $cache->get('global_settings');

        // 3. Si no existe en caché (es null), entonces sí vamos a la Base de Datos
        if ($globalSettings === null) {
            $settingsModel = new \App\Models\SettingsModel();
            $globalSettings = $settingsModel->find(1);

            // Guardamos el resultado en la caché por mucho tiempo (ej. 30 días = 2592000 segundos)
            // Así, durante un mes nadie volverá a consultar la base de datos para esto.
            $cache->save('global_settings', $globalSettings, 2592000);
        }

        // 4. Compartimos la variable globalmente
        \Config\Services::renderer()->setVar('global_settings', $globalSettings);
    
    }
}
