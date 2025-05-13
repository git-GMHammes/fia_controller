<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
// use App\Models\NomeModel;
use Exception;

class UnidDisponibilidadeEndpointController extends ResourceController
{
    use ResponseTrait;
    private $template = 'exemplo/templates/main';
    private $message = 'exemplo/message';
    private $footer = 'exemplo/AppFooter';
    private $head = 'exemplo/AppHad';
    private $menu = 'exemplo/AppMenu';
    private $ModelResponse;
    private $uri;
    private $token;
    #
    public function __construct()
    {
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
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
    # route GET /www/exemple/group/endpoint/teste/(:any)
    # route POST /www/exemple/group/endpoint/teste/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function displayAPI($parameter = NULL)
    {
        // $this->token_csrf();
        $request = service('request');
        $getMethod = $request->getMethod();
        $getVar_page = $request->getVar('page');
        $processRequest = (array)$request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        $id = (isset($processRequest['id'])) ? ('/' . $processRequest['id']) : ('/' . $parameter);
        // $processRequest = eagarScagaire($processRequest);
        #
        $loadView = array(
            $this->app_head,
            $this->app_menu,
            $this->message,
            'fia/ptpa/camposFormatacao/AppDataPtBr',
            'exemplo/principal/main',
            $this->app_footer,
        );
        try {
            # URI da API                                                                                                          
            // $endPoint['objeto'] = myEndPoint('index.php/projeto/endereco/api/verbo', '123');
            $requestJSONform['objeto'] = isset($endPoint['objeto']['result']) ? $endPoint['objeto']['result'] : array();
            #
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
                'result' => $processRequest,
                'loadView' => $loadView,
                'metadata' => [
                    'page_title' => 'Application title',
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
    # route GET /exemple/group/endpoint/formatualizar/(:any)
    # route POST /exemple/group/endpoint/formatualizar/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function formUpdate($parameter = NULL)
    {
        // $this->token_csrf();
        $request = service('request');
        $getMethod = $request->getMethod();
        $pageGet = $this->request->getGet('pager');
        $pager = (isset($pageGet) && !empty($pageGet)) ? ('?pager=' . $pageGet) : ('?pager=1');
        $processRequest = (array)$request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        $parameter = ($parameter == NULL) ? ($parameter) : ('/' . $parameter);
        $id = (isset($processRequest['id'])) ? ('/' . $processRequest['id']) : ($parameter);
        // $processRequest = eagarScagaire($processRequest);
        #
        $loadView = array(
            'fia/ptpa/head',
            'fia/ptpa/menu',
            'fia/ptpa/message',
            'exemplo/principal/formUpdate',
            'fia/ptpa/footer',
        );
        try {
            # URI da API                                                                                                          
            $endPoint['adolescente'] = myEndPoint('index.php/fia/ptpa/adolescente/api/exibir' . $id . $pager, '123');
            $requestJSONform['adolescente']['list'] = isset($endPoint['adolescente']['result']['dbResponse']) ? $endPoint['adolescente']['result']['dbResponse'] : array();
            $requestJSONform['adolescente']['paginate'] = isset($endPoint['adolescente']['result']['linksArray']) ? $endPoint['adolescente']['result']['linksArray'] : array();
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
                'result' => $processRequest,
                'loadView' => $loadView,
                'metadata' => [
                    'page_title' => 'Application title',
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
}
