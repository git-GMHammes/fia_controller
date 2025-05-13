<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\TokenCsrfController;
use App\Controllers\SystemBaseController;
// use App\Models\NomeModel;
use Exception;

class AlocarFuncionarioEndpointController extends ResourceController
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
        $this->viewFormatacao = new SystemBaseController();
        $this->viewValidacao = new SystemBaseController();
        $this->viewPadroes = new SystemBaseController();
        $this->tokenCsrf = new TokenCsrfController();
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
    # route GET /www/index.php/fia/ptpa/profissional/endpoint/alocar/(:any)
    # route POST /www/index.php/fia/ptpa/profissional/endpoint/alocar/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function dbAllocate($parameter = NULL)
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
            'fia/ptpa/profissional/AppForm',
            'fia/ptpa/profissional/AppAtualizar',
            $this->app_footer,
        );
        $loadView = array_merge($loadView1, $loadView2, $loadView3, $loadView4, $loadView5);
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
                    'page_title' => 'VALIDAR FUNCIONÁRIO',
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
                    'page_title' => 'ERRO - VALIDAR FUNCIONÁRIO',
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
    # route GET /www/index.php/fia/ptpa/alocarfuncionario/endpoint/consultarfunc/(:any)
    # route POST /www/index.php/fia/ptpa/alocarfuncionario/endpoint/consultarfunc/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function dbConsultFunc($parameter = NULL)
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
            'fia/ptpa/profissional/AppForm',
            'fia/ptpa/profissional/AppConsultar',
            'fia/ptpa/alocarFuncionario/AppListarHistorico',
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
                    'page_title' => 'CONSULTAR FUNCIONÁRIO ALOCADO',
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
                    'page_title' => 'ERRO - CONSULTAR FUNCIONÁRIO ALOCADO',
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
    # route GET /www/index.php/fia/ptpa/unidade/endpoint/atualizar/(:any)
    # route POST /www/index.php/fia/ptpa/unidade/endpoint/atualizar/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function dbConsult($parameter = NULL)
    {
        // myPrint($parameter, 'src\app\Controllers\AlocarFuncionarioEndpointController.php');
        // $this->tokenCsrf->token_csrf();
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
            'fia/ptpa/unidades/AppForm',
            'fia/ptpa/profissional/AppListarConteudo',
            'fia/ptpa/alocarFuncionario/AppConsultarUnidade',
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
                    'page_title' => 'CONSULTAR ALOCAÇÃO DE FUNCIONÁRIOS NA UNIDADE',
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
                    'page_title' => 'ERRO - CONSULTAR ALOCAÇÃO DE FUNCIONÁRIOS NA UNIDADE',
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
    # route GET /www/index.php/fia/ptpa/alocarFuncionario/endpoint/exibir/(:any)
    # route POST /www/index.php/fia/ptpa/alocarFuncionario/endpoint/exibir/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function dbRead($parameter = NULL)
    {
        // $this->token_Csrf();
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
            'fia/ptpa/camposFormatacao/AppDataPtBr',
            'fia/ptpa/alocarFuncionario/AppListarUnidade',
            $this->app_footer,
        );
        $loadView = array_merge($loadView1, $loadView2, $loadView3, $loadView4, $loadView5);
        #
        try {
            $this->tokenCsrf->token_csrf();
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
                    'page_title' => 'ALOCAR FUNCIONÁRIOS',
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
                    'page_title' => 'ERRO - ALOCAR FUNCIONÁRIOS',
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
    # route GET /www/index.php/fia/ptpa/alocarFuncionario/endpoint/cadastrar/(:any)
    # route POST /www/index.php/fia/ptpa/alocarFuncionario/endpoint/cadastrar/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function dbCreate($parameter = NULL)
    {
        $this->tokenCsrf->token_csrf();
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
            'fia/ptpa/profissional/AppForm',
            'fia/ptpa/profissional/AppCadastrar',
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
                    'page_title' => 'CADASTRAR/ALOCAR FUNCIONÁRIO',
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
                    'page_title' => 'ERRO - CADASTRAR/ALOCAR FUNCIONÁRIO',
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
    # route GET /www/index.php/fia/ptpa/alocarFuncionario/endpoint/atualizar/(:any)
    # route POST /www/index.php/fia/ptpa/alocarFuncionario/endpoint/atualizar/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function dbUpdate($parameter = NULL)
    {
        // $this->tokenCsrf->token_csrf();
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
            'fia/ptpa/profissional/AppForm',
            'fia/ptpa/profissional/AppAtualizar',
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
                    'page_title' => 'ATUALIZAR FUNCIONÁRIO ALOCADO',
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
                    'page_title' => 'ERRO - ATUALIZAR FUNCIONÁRIO ALOCADO',
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
    # route GET /www/index.php/fia/ptpa/unidade/endpoint/deletar/(:any)
    # route POST /www/index.php/fia/ptpa/unidade/endpoint/deletar/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function dbDelete($parameter = NULL)
    {
        //$this->tokenCsrf->token_csrf();
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
            'fia/ptpa/camposValidacao/AppUnidadeEndereco',
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
                    'page_title' => 'EXCLUIR UNIDADE',
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
                    'page_title' => 'ERRO - EXCLUIR UNIDADE',
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
    # route GET /www/index.php/fia/ptpa/unidade/endpoint/limpar/(:any)
    # route POST /www/index.php/fia/ptpa/unidade/endpoint/limpar/(:any)
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
            'fia/ptpa/camposValidacao/AppUnidadeEndereco',
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
                    'page_title' => 'LIMPAR UNIDADE',
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
                    'page_title' => 'ERRO - LIMPAR UNIDADE',
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
