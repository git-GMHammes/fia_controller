<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\TokenCsrfController;
// use App\Models\NomeModel;
use Exception;

class ProntuarioPSEndpointController extends ResourceController
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
    private $tokenCsrf;
    private $uri;
    private $token;
    #
    public function __construct()
    {
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
        $this->tokenCsrf = new TokenCsrfController();
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
            // 'fia/ptpa/prontuario/AppForm',
            'fia/ptpa/prontuario/AppForm2',
            'fia/ptpa/prontuario/AppConsultar',
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
                    'page_title' => 'CONSULTAR PRONTUÁRIO PSICOSSOCIAL',
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
                    'page_title' => 'ERRO - PRONTUÁRIO PSICOSSOCIAL',
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
    # route GET www/index.php/fia/ptpa/prontuariopsicosocial/endpoint/exibir/(:any)
    # route POST www/index.php/fia/ptpa/prontuariopsicosocial/endpoint/exibir/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function dbRead($parameter = NULL)
    {
        $this->tokenCsrf->token_csrf();
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
            'fia/ptpa/prontuario/AppForm2',
            'fia/ptpa/prontuario/AppListar_conteudo',
            'fia/ptpa/prontuario/AppListar',
            $this->app_footer,
        );
        $loadView = array_merge($loadView1, $loadView2, $loadView3, $loadView4, $loadView5);
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
                    'page_title' => 'LISTAR PRONTUÁRIO PSICOSSOCIAL',
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
                    'page_title' => 'ERRO - LISTAR PRONTUÁRIO PSICOSSOCIAL',
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
    # route GET www/index.php/fia/ptpa/prontuariopsicosocial/endpoint/criar/(:any)
    # route POST www/index.php/fia/ptpa/prontuariopsicosocial/endpoint/criar/(:any)
    # Informação sobre o controller
    # retorno do controller [VIEW]
    public function dbCreate($parameter = NULL)
    {
        $request = service('request');
        $getMethod = $request->getMethod();
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
            // 'fia/ptpa/prontuario/AppForm',
            'fia/ptpa/prontuario/AppForm2',
            'fia/ptpa/prontuario/AppCadastrar',
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
                'result' => $requestJSONform,
                'loadView' => $loadView,
                'metadata' => [
                    'page_title' => 'CADASTRAR PRONTUÁRIO PSICOSSOCIAL',
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
                    'page_title' => 'ERRO - CADASTRAR PRONTUÁRIO PSICOSSOCIAL',
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
    # route GET www/index.php/fia/ptpa/prontuariopsicosocial/endpoint/atualizar/(:any)
    # route POST www/index.php/fia/ptpa/prontuariopsicosocial/endpoint/atualizar/(:any)
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
            // 'fia/ptpa/prontuario/AppForm',
            'fia/ptpa/prontuario/AppForm2',
            'fia/ptpa/prontuario/AppAtualizar',
            $this->app_footer,
        );
        $loadView = array_merge($loadView1, $loadView2, $loadView3, $loadView4, $loadView5);
        #
        $this->tokenCsrf->token_csrf();
        try {
            # URI da API                                                                                                          
            // $endPoint['objeto'] = myEndPoint('index.php/projeto/endereco/api/verbo', '123');
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
                    'page_title' => 'ATUALIZAR PRONTUÁRIO PSICOSSOCIAL',
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
                    'page_title' => 'ERRO - ATUALIZAR PRONTUÁRIO PSICOSSOCIAL',
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
    # route GET www/index.php/fia/ptpa/prontuariopsicosocial/endpoint/atualizar/(:any)
    # route POST www/index.php/fia/ptpa/prontuariopsicosocial/endpoint/atualizar/(:any)
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
            'fia/ptpa/camposFormatacao/AppDataPtBr',
            'fia/ptpa/prontuario/excluir',
            $this->app_footer,
        );
        $loadView = array_merge($loadView1, $loadView2, $loadView3, $loadView4, $loadView5);
        #
        try {
            # URI da API                                                                                                          
            // $endPoint['objeto'] = myEndPoint('index.php/projeto/endereco/api/verbo', '123');
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
                    'page_title' => 'EXCLUIR PRONTUÁRIO PSICOSSOCIAL',
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
                    'page_title' => 'ERRO - EXCLUIR PRONTUÁRIO PSICOSSOCIAL',
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
    # route GET www/index.php/fia/ptpa/prontuariopsicosocial/endpoint/limpar/(:any)
    # route POST www/index.php/fia/ptpa/prontuariopsicosocial/endpoint/limpar/(:any)
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
            'fia/ptpa/prontuario/lixeira',
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
                    'page_title' => 'LIMPAR PRONTUÁRIO PSICOSSOCIAL',
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
                    'page_title' => 'ERRO - LIMPAR PRONTUÁRIO PSICOSSOCIAL',
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
