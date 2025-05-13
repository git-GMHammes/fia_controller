<?php

namespace App\Controllers\Teste;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\TokenCsrfController;

use Exception;

class EndpointController extends ResourceController
{
    private $template = 'fia/ptpa/templates/main';
    private $app_message_card = 'fia/ptpa/AppMessageCard';
    private $app_loading = 'fia/ptpa/AppLoading';
    private $app_footer = 'fia/ptpa/AppFooter';
    private $app_json = 'fia/ptpa/AppJson';
    private $app_head = 'fia/ptpa/AppHead';
    private $app_menu = 'fia/ptpa/AppMenu';
    private $message = 'fia/ptpa/message';
    private $viewFormatacao;
    private $ModelResponse;
    private $viewValidacao;
    private $viewPadroes;
    private $tokenCsrf;
    private $token;
    private $uri;
    #
    public function __construct()
    {
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
        // $this->viewFormatacao = new SystemBaseController();
        // $this->viewValidacao = new SystemBaseController();
        // $this->viewPadroes = new SystemBaseController();
        // $this->tokenCsrf = new TokenCsrfController();
        $this->tokenCsrf = new TokenCsrfController();
        $this->token = isset($_COOKIE['token']) ? $_COOKIE['token'] : '123';
    }

    private function setAQpiRespond(string $status = null, string $message = '', array $requestJSONform = array(), array $loadView = array(), string $getMethod = null)
    {
        $apiRespond = [
            'status' => $status,
            'message' => $message !== '' ? $message : 'API loading data (dados para carregamento da API)',
            'date' => date('Y-m-d'),
            'api' => [
                'version' => '1.0',
                'method' => $getMethod,
                'description' => 'API Description',
                'content_type' => 'application/x-www-form-urlencoded'
            ],
            'result' => $requestJSONform,
            'loadView' => $loadView,
            'metadata' => [
                'page_title' => 'FIA/PTPA',
                'getURI' => $this->uri->getSegments(),
                'environment' => ENVIRONMENT_CHOICE,
                'method' => '__METHOD__',
                'function' => '__FUNCTION__',
            ]
        ];
        return $apiRespond;
    }
    private function setRequestJSONform(string $id = NULL, int $page = 1, int $limit = 10)
    {
        $requestJSONform = array(
            'id' => $id,
            'page' => $page,
            'limit' => $limit,
        );
        return $requestJSONform;
    }

    private function setView(array $loadView5 = array())
    {
        $loadView1 = array(
            $this->app_head,
            $this->app_menu,
            $this->message,
            $this->app_message_card,
            $this->app_loading,
            $this->app_json,
        );
        #
        // $loadView2 = $this->viewValidacao->camposValidacao();
        // $loadView3 = $this->viewPadroes->camposPadroes();
        // $loadView4 = $this->viewFormatacao->camposFormatacao();
        if (
            isset($loadView2) &&
            isset($loadView3) &&
            isset($loadView4) &&
            isset($loadView5) &&
            $loadView5 !== array()
        ) {
            $loadView6 = array(
                // $this->app_footer,
            );
            $loadView = array_merge($loadView1, $loadView2, $loadView3, $loadView4, $loadView5, $loadView6);
        } elseif (
            isset($loadView5) &&
            $loadView5 !== array()
        ) {
            $loadView = $loadView5;
        } else {
            $loadView6 = array(
                // $this->app_footer,
            );
            $loadView = array_merge($loadView1, $loadView6);
        }

        return $loadView;
    }
    # Consumo de API
    # route GET /www/index.php/exemple/group/endpoint/teste/(:any)
    # route POST /www/index.php/exemple/group/endpoint/teste/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function react($parameter = NULL)
    {
        $request = service('request');
        $getMethod = $request->getMethod();
        $pageGet = $this->request->getGet('page');
        $limit = (isset($limitGet) && !empty($limitGet)) ? ($limitGet) : (10);
        $processRequest = (array) $request->getVar();
        $json = 1;
        $id = (isset($processRequest['id'])) ? ('/' . $processRequest['id']) : ('/' . $parameter);
        $page = (isset($pageGet) && !empty($pageGet)) ? ($pageGet) : (1);
        $limitGet = $this->request->getGet('limit');
        $this->tokenCsrf->token_csrf();
        #
        $loadView = $this->setView(
            [
                'caminho/pasta/subpasta',
            ]
        );
        #
        #
        try {
            $requestJSONform = $this->setRequestJSONform($id, $page, $limit);
            $apiRespond = $this->setAQpiRespond('success', '', $requestJSONform, $loadView, $getMethod);
            if ($json == 1) {
                $response = $this->response->setStatusCode(201)->setJSON($apiRespond);
            }
        } catch (\Exception $e) {
            $apiRespond = $this->setAQpiRespond('error', $e->getMessage(), $requestJSONform, $loadView, $getMethod);
            if ($json == 1) {
                $response = $this->response->setStatusCode(500)->setJSON($apiRespond);
            }
        }
        if ($json == 1) {
            return $response;
        } else {
            return view($this->template, $apiRespond);
        }
    }

    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [view]
    public function index()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }

    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [view]
    public function dbRead()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }

    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [view]
    public function dbFilter()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }

    # Consumo de Endpoint
    # route GET /www/teste/group/endpoint/cadastrar/(:any)
    # route POST /www/teste/group/endpoint/cadastrar/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function dbCreate($parameter = NULL)
    {
        $request = service('request');
        $getMethod = $request->getMethod();
        $pageGet = $this->request->getGet('page');
        $limit = (isset($limitGet) && !empty($limitGet)) ? ($limitGet) : (10);
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        $id = (isset($processRequest['id'])) ? ('/' . $processRequest['id']) : ('/' . $parameter);
        $page = (isset($pageGet) && !empty($pageGet)) ? ($pageGet) : (1);
        $limitGet = $this->request->getGet('limit');
        $this->tokenCsrf->token_csrf();
        #
        $loadView = $this->setView(
            [
                'analise/teste/AppForm',
                'analise/teste/AppCadastrar',
            ]
        );
        #
        try {
            $requestJSONform = $this->setRequestJSONform($id, $page, $limit);
            $apiRespond = $this->setAQpiRespond('success', '', $requestJSONform, $loadView, $getMethod);
            if ($json == 1) {
                $response = $this->response->setStatusCode(201)->setJSON($apiRespond);
            }
        } catch (\Exception $e) {
            $apiRespond = $this->setAQpiRespond('error', $e->getMessage(), $requestJSONform, $loadView, $getMethod);
            if ($json == 1) {
                $response = $this->response->setStatusCode(500)->setJSON($apiRespond);
            }
        }
        if ($json == 1) {
            return $response;
        } else {
            return view($this->template, $apiRespond);
        }
    }

    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [view]
    public function dbUpdate()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }

    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [view]
    public function dbDelete()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }

    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [view]
    public function dbCleanner()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }

    # Consumo de API
    # route GET /# www/index.php/teste/group/endpoint/select/(:any)
    # route POST /# www/index.php/teste/group/endpoint/select/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function dbSelect($parameter = NULL)
    {
        $request = service('request');
        $getMethod = $request->getMethod();
        $pageGet = $this->request->getGet('page');
        $limit = (isset($limitGet) && !empty($limitGet)) ? ($limitGet) : (10);
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        $id = (isset($processRequest['id'])) ? ('/' . $processRequest['id']) : ('/' . $parameter);
        $page = (isset($pageGet) && !empty($pageGet)) ? ($pageGet) : (1);
        $limitGet = $this->request->getGet('limit');
        $this->tokenCsrf->token_csrf();
        #
        $loadView = $this->setView(
            [
                'analise/TestaSelect',
            ]
        );

        try {
            $requestJSONform = $this->setRequestJSONform($id, $page, $limit);
            $apiRespond = $this->setAQpiRespond('success', '', $requestJSONform, $loadView, $getMethod);
            if ($json == 1) {
                $response = $this->response->setStatusCode(201)->setJSON($apiRespond);
            }
        } catch (\Exception $e) {
            $apiRespond = $this->setAQpiRespond('error', $e->getMessage(), $requestJSONform, $loadView, $getMethod);
            if ($json == 1) {
                $response = $this->response->setStatusCode(500)->setJSON($apiRespond);
            }
        }
        if ($json == 1) {
            return $response;
        } else {
            return view($this->template, $apiRespond);
        }
    }


}

?>