<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
// use App\Models\NomeModel;
use Exception;

class MainEndpointController extends ResourceController
{
    use ResponseTrait;
    private $template = 'fia/ptpa/templates/main';
    private $message = 'fia/ptpa/message';
    private $app_message_card = 'fia/ptpa/AppMessageCard';
    private $app_message = 'fia/ptpa/AppMessage';
    private $app_footer = 'fia/ptpa/AppFooter';
    private $app_head = 'fia/ptpa/AppHead';
    private $app_menu = 'fia/ptpa/AppMenu';
    private $app_loading = 'fia/ptpa/AppLoading';
    private $app_json = 'fia/ptpa/AppJson';
    private $viewValidacao;
    private $viewPadroes;
    private $viewFormatacao;
    private $ModelResponse;
    private $uri;
    private $token;
    #
    public function __construct()
    {
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
        $this->viewValidacao = new SystemBaseController();
        $this->viewPadroes = new SystemBaseController();
        $this->viewFormatacao = new SystemBaseController();
        $this->token = isset($_COOKIE['token']) ? $_COOKIE['token'] : '123';
    }
    #
    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [view]
    public function index()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }

    # Consumo de API
    # route GET /www/index.php/fia/ptpa/principal/endpoint/public/(:any)
    # route POST /www/index.php/fia/ptpa/principal/endpoint/public/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function main($parameter = NULL)
    {
        // $this->token_csrf();
        $request = service('request');
        $getMethod = $request->getMethod();
        $getVar_page = $request->getVar('page');
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        $id = (isset($processRequest['id'])) ? ('/' . $processRequest['id']) : ('/' . $parameter);
        // $processRequest = eagarScagaire($processRequest);
        #
        $loadView = array(
            // $this->app_head,
            // $this->app_menu,
            $this->message,
            'fia/ptpa/principal/main_sso',
            // $this->app_footer,
        );
        try {
            $requestJSONform = array();
            $apiRespond = [
                'status' => 'success',
                'message' => 'API loading data (dados para carregamento da API)',
                'date' => date('Y-m-d'),
                'api' => [
                    'version' => '1.0',
                    'method' => $getMethod,
                    'description' => 'API Description',
                    'content_type' => 'application/x-www-form-urlencoded'
                ],
                // 'method' => '__METHOD__',
                // 'function' => '__FUNCTION__',
                'result' => $requestJSONform,
                'loadView' => $loadView,
                'metadata' => [
                    'page_title' => 'GOV.BR',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                    // Você pode adicionar campos comentados anteriormente se forem relevantes
                    // 'method' => '__METHOD__',
                    // 'function' => '__FUNCTION__',
                ]
            ];
            if ($json == 1) {
                $response = $this->response->setJSON($apiRespond, 201);
            }
        } catch (\Exception $e) {
            $apiRespond = [
                'status' => 'error',
                'message' => $e->getMessage(),
                'date' => date('Y-m-d'),
                'api' => [
                    'version' => '1.0',
                    'method' => $getMethod,
                    'description' => 'API Criar Method',
                    'content_type' => 'application/x-www-form-urlencoded'
                ],
                'metadata' => [
                    'page_title' => 'ERRO - API Method',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                ]
            ];
            if ($json == 1) {
                $response = $this->response->setJSON($apiRespond, 500);
            }
        }
        if ($json == 1) {
            return $apiRespond;
        } else {
            // return $apiRespond;
            return view($this->template, $apiRespond);
        }
    }

    # Consumo de API
    # route GET /www/index.php/fia/ptpa/principal/endpoint/indicadores/(:any)
    # route POST /www/index.php/fia/ptpa/principal/endpoint/indicadores/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function pointer($parameter = NULL)
    {
        // $this->token_csrf();
        $request = service('request');
        $getMethod = $request->getMethod();
        $getVar_page = $request->getVar('page');
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        $id = (isset($processRequest['id'])) ? ('/' . $processRequest['id']) : ('/' . $parameter);
        #
        $loadView1 = array(
            $this->app_head,
            $this->app_menu,
            $this->message,
            $this->app_message_card,
            $this->app_message,
            $this->app_loading,
            $this->app_json,
        );
        #
        $loadView2 = $this->viewValidacao->camposValidacao();
        $loadView3 = $this->viewPadroes->camposPadroes();
        $loadView4 = $this->viewFormatacao->camposFormatacao();
        #
        $loadView5 = array(
            'fia/ptpa/principal/main_pointer',
            $this->app_footer,
        );
        $loadView = array_merge($loadView1, $loadView2, $loadView3, $loadView4, $loadView5);
        // myPrint('$loadView :: ', $loadView);
        #
        try {
            $endPoint['programa'] = myEndPoint('index.php/fia/ptpa/programa/api/exibir', '123');
            $requestJSONform['programa'] = isset($endPoint['programa']['result']['dbResponse']) ? $endPoint['programa']['result']['dbResponse'] : array();
            // myPrint($requestJSONform, 'src\app\Controllers\MainEndpointController.php');
            $requestJSONform = array();
            $apiRespond = [
                'status' => 'success',
                'message' => 'API loading data (dados para carregamento da API)',
                'date' => date('Y-m-d'),
                'api' => [
                    'version' => '1.0',
                    'method' => $getMethod,
                    'description' => 'API Description',
                    'content_type' => 'application/x-www-form-urlencoded'
                ],
                // 'method' => '__METHOD__',
                // 'function' => '__FUNCTION__',
                'result' => $requestJSONform,
                'loadView' => $loadView,
                'metadata' => [
                    'page_title' => 'Indicadores',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                    // Você pode adicionar campos comentados anteriormente se forem relevantes
                    // 'method' => '__METHOD__',
                    // 'function' => '__FUNCTION__',
                ]
            ];
            if ($json == 1) {
                $response = $this->response->setJSON($apiRespond, 201);
            }
        } catch (\Exception $e) {
            $apiRespond = [
                'status' => 'error',
                'message' => $e->getMessage(),
                'date' => date('Y-m-d'),
                'api' => [
                    'version' => '1.0',
                    'method' => $getMethod,
                    'description' => 'API Criar Method',
                    'content_type' => 'application/x-www-form-urlencoded'
                ],
                'metadata' => [
                    'page_title' => 'ERRO - Indicadores',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                ]
            ];
            if ($json == 1) {
                $response = $this->response->setJSON($apiRespond, 500);
            }
        }
        if ($json == 1) {
            return $apiRespond;
        } else {
            // return $apiRespond;
            return view($this->template, $apiRespond);
        }
    }
}
