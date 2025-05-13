<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\TokenCsrfController;

use Exception;

class AdolescenteEndpointController extends ResourceController
{
    use ResponseTrait;
    private $template = 'fia/ptpa/templates/main';
    private $message = 'fia/ptpa/message';
    private $app_message_card = 'fia/ptpa/AppMessageCard';
    private $app_message = 'fia/ptpa/AppMessage';
    private $app_loading = 'fia/ptpa/AppLoading';
    private $app_footer = 'fia/ptpa/AppFooter';
    private $app_head = 'fia/ptpa/AppHead';
    private $app_menu = 'fia/ptpa/AppMenu';
    private $app_json = 'fia/ptpa/AppJson';
    private $ModelResponse;
    private $viewValidacao;
    private $viewPadroes;
    private $viewFormatacao;
    private $tokenCsrf;
    private $uri;
    private $token;
    #
    public function __construct()
    {
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
        $this->tokenCsrf = new TokenCsrfController();
        $this->token = isset($_COOKIE['token']) ? $_COOKIE['token'] : '123';
        $this->viewValidacao = new SystemBaseController();
        $this->viewPadroes = new SystemBaseController();
        $this->viewFormatacao = new SystemBaseController();
    }
    #
    # route POST /www/index.php/fia/ptpa/adolescente/endpoint/sucessocadastro/(:any)
    # route GET /www/index.php/fia/ptpa/adolescente/endpoint/sucessocadastro/(:any)
    # Informação sobre o controller
    # retorno do controller [view]
    public function index()
    {
        $loadView = array(
            'fia/ptpa/adolescentes/AppPortalCadastroFia'
        );
        try {
            $apiRespond = [
                'status' => 'success',
                'message' => 'API loading data (dados para carregamento da API)',
                'date' => date('Y-m-d'),
                'api' => [
                    'version' => '1.0',
                    'method' => 'get',
                    'description' => 'API Description',
                    'content_type' => 'application/x-www-form-urlencoded'
                ],
                // 'method' => '__METHOD__',
                // 'function' => '__FUNCTION__',
                'result' => 'imagem',
                'loadView' => $loadView,
                'metadata' => [
                    'page_title' => 'CADASTRAR ADOLESCENTE',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                    // Você pode adicionar campos comentados anteriormente se forem relevantes
                    // 'method' => '__METHOD__',
                    // 'function' => '__FUNCTION__',
                ]
            ];
        } catch (\Exception $e) {
            $apiRespond = [
                'status' => 'error',
                'message' => $e->getMessage(),
                'date' => date('Y-m-d'),
                'api' => [
                    'version' => '1.0',
                    'method' => 'GET',
                    'description' => 'API Criar Method',
                    'content_type' => 'application/x-www-form-urlencoded'
                ],
                'metadata' => [
                    'page_title' => 'ERRO - CADASTRAR ADOLESCENTE',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                ]
            ];
        }
        return view($this->template, $apiRespond);
    }

    # Consumo de API
    # route GET /www/index.php/fia/ptpa/profissional/endpoint/consultar/(:any)
    # route POST /www/index.php/fia/ptpa/profissional/endpoint/consultar/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function dbConsult($parameter = NULL)
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
            'fia/ptpa/AppLoadingPage',
            'fia/ptpa/adolescentes/AppForm',
            'fia/ptpa/adolescentes/AppConsultar',
            $this->app_footer,
        );
        $loadView = array_merge($loadView1, $loadView2, $loadView3, $loadView4, $loadView5);
        #
        try {
            $this->tokenCsrf->token_csrf();
            $processRequest = array(
                'atualizar_id' => $id,
            );
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
                    'page_title' => 'CONSULTAR ADOLESCENTE',
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
                    'method' => $processRequest,
                    'description' => 'API Criar Method',
                    'content_type' => 'application/x-www-form-urlencoded'
                ],
                'metadata' => [
                    'page_title' => 'ERRO - CONSULTAR ADOLESCENTE',
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
    # route GET /www/index.php/fia/ptpa/adolescente/endpoint/exibir/(:any)
    # route POST /www/index.php/fia/ptpa/adolescente/endpoint/exibir/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function dbRead($parameter = NULL)
    {
        $request = service('request');
        $getMethod = $request->getMethod();
        $getVar_page = $request->getVar('page');
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
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
            'fia/ptpa/adolescentes/AppForm',
            'fia/ptpa/adolescentes/AppListar_conteudo',
            'fia/ptpa/adolescentes/AppListar',
            $this->app_footer,
        );
        $loadView = array_merge($loadView1, $loadView2, $loadView3, $loadView4, $loadView5);
        #
        $this->tokenCsrf->token_csrf();
        try {
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
                    'page_title' => 'ADOLESCENTE',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                    'getVar_page' => $getVar_page,
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
                    'description' => 'API Description',
                    'content_type' => 'application/x-www-form-urlencoded'
                ],
                'metadata' => [
                    'page_title' => 'ERRO - ADOLESCENTE',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                    'getVar_page' => $getVar_page,
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
    # route GET /www/index.php/fia/ptpa/adolescente/endpoint/setDrupal/(:any)
    # route POST /www/index.php/fia/ptpa/adolescente/endpoint/setDrupal/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function setDrupal($parameter = NULL)
    {
        $request = service('request');
        $getMethod = $request->getMethod();
        $getVar_page = $request->getVar('page');
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        $id = (isset($processRequest['id'])) ? ('/' . $processRequest['id']) : ('/' . $parameter);
        #
        $loadView1 = array(
            $this->app_message_card,
            $this->app_loading,
            $this->app_json,
        );
        #
        $loadView2 = $this->viewValidacao->camposValidacao();
        $loadView3 = $this->viewPadroes->camposPadroes();
        $loadView4 = $this->viewFormatacao->camposFormatacao();
        #
        $loadView5 = array(
            'fia/ptpa/AppLoadingPage',
            'fia/ptpa/adolescentes/AppTermosUso',
            'fia/ptpa/adolescentes/AppForm',
            'fia/ptpa/adolescentes/AppCadastrar',
        );
        $loadView = array_merge($loadView1, $loadView2, $loadView3, $loadView4, $loadView5);
        // myprint($loadView, 'src\app\Controllers\AdolescenteEndpointController.php');
        #
        try {
            $this->tokenCsrf->token_csrf();
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
                    'page_title' => 'CADASTRAR ADOLESCENTE',
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
                    'page_title' => 'ERRO - CADASTRAR ADOLESCENTE',
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
    # route GET /www/index.php/fia/ptpa/adolescente/endpoint/setDrupal/(:any)
    # route POST /www/index.php/fia/ptpa/adolescente/endpoint/setDrupal/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function setDrupal2($parameter = NULL)
    {
        $request = service('request');
        $getMethod = $request->getMethod();
        $getVar_page = $request->getVar('page');
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        $id = (isset($processRequest['id'])) ? ('/' . $processRequest['id']) : ('/' . $parameter);
        #
        $loadView1 = array(
            $this->app_message_card,
            $this->app_loading,
            $this->app_json,
        );
        #
        $loadView2 = $this->viewValidacao->camposValidacao();
        $loadView3 = $this->viewPadroes->camposPadroes();
        $loadView4 = $this->viewFormatacao->camposFormatacao();
        #
        $loadView5 = array(
            'fia/ptpa/AppLoadingPage',
            'fia/ptpa/adolescentes/AppTermosUso',
            'fia/ptpa/adolescentes/AppForm2',
            'fia/ptpa/adolescentes/AppCadastrar2',
        );
        $loadView = array_merge($loadView1, $loadView2, $loadView3, $loadView4, $loadView5);
        // myprint($loadView, 'src\app\Controllers\AdolescenteEndpointController.php');
        #
        try {
            $this->tokenCsrf->token_csrf();
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
                    'page_title' => 'CADASTRAR ADOLESCENTE',
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
                    'page_title' => 'ERRO - CADASTRAR ADOLESCENTE',
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
    # route GET /www/index.php/fia/ptpa/adolescente/endpoint/cadastrar/(:any)
    # route POST /www/index.php/fia/ptpa/adolescente/endpoint/cadastrar/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function dbCreate($parameter = NULL)
    {
        $request = service('request');
        $getMethod = $request->getMethod();
        $getVar_page = $request->getVar('page');
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        $id = (isset($processRequest['id'])) ? ('/' . $processRequest['id']) : ('/' . $parameter);
        // $processRequest = eagarScagaire($processRequest);
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
            'fia/ptpa/AppLoadingPage',
            'fia/ptpa/adolescentes/AppTermosUso',
            'fia/ptpa/adolescentes/AppForm',
            'fia/ptpa/adolescentes/AppCadastrar',
            $this->app_footer,
        );
        $loadView = array_merge($loadView1, $loadView2, $loadView3, $loadView4, $loadView5);
        // myPrint('$loadView :: ', $loadView);
        #
        try {
            $this->tokenCsrf->token_csrf();
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
                    'page_title' => 'CADASTRAR ADOLESCENTE',
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
                    'page_title' => 'ERRO - CADASTRAR ADOLESCENTE',
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
    # route GET /www/index.php/fia/ptpa/adolescente/endpoint/atualizar/(:any)
    # route POST /www/index.php/fia/ptpa/adolescente/endpoint/atualizar/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function dbUpdate($parameter = NULL)
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
            'fia/ptpa/AppLoadingPage',
            'fia/ptpa/adolescentes/AppForm',
            'fia/ptpa/adolescentes/AppAtualizar',
            $this->app_footer,
        );
        $loadView = array_merge($loadView1, $loadView2, $loadView3, $loadView4, $loadView5);
        #
        try {
            $this->tokenCsrf->token_csrf();
            $processRequest = array(
                'atualizar_id' => $id,
            );
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
                    'page_title' => 'ATUALIZAR ADOLESCENTE',
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
                    'page_title' => 'ERRO - ATUALIZAR ADOLESCENTE',
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
    # route GET /www/index.php/fia/ptpa/adolescente/endpoint/deletar/(:any)
    # route POST /www/index.php/fia/ptpa/adolescente/endpoint/deletar/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function dbDelete($parameter = NULL)
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
            'fia/ptpa/adolescentes/excluir',
            $this->app_footer,
        );
        $loadView = array_merge($loadView1, $loadView2, $loadView3, $loadView4, $loadView5);
        #
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
                'result' => $processRequest,
                'loadView' => $loadView,
                'metadata' => [
                    'page_title' => 'EXCLUIR ADOLESCENTE',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                    'getVar_page' => $getVar_page,
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
                    'page_title' => 'ERRO - EXCLUIR ADOLESCENTE',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                    'getVar_page' => $getVar_page,
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
    # route GET /www/index.php/fia/ptpa/adolescente/endpoint/limpar/(:any)
    # route POST /www/index.php/fia/ptpa/adolescente/endpoint/limpar/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function dbCleanner($parameter = NULL)
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
            'fia/ptpa/adolescentes/lixeira',
            $this->app_footer,
        );
        $loadView = array_merge($loadView1, $loadView2, $loadView3, $loadView4, $loadView5);
        #
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
                'result' => $processRequest,
                'loadView' => $loadView,
                'metadata' => [
                    'page_title' => 'LIMPAR ADOLESCENTE',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                    'getVar_page' => $getVar_page,
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
                    'page_title' => 'ERRO - LIMPAR ADOLESCENTE',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                    'getVar_page' => $getVar_page,
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
