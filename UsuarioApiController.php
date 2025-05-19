<?php

namespace App\Controllers;

// Envolve, Usuário/Profissional/Segurança
use App\Controllers\UsuarioDbController;
use App\Controllers\CadastroDbController;
use App\Controllers\ProfissionalDbController;
use App\Controllers\MenuDbController;
use App\Controllers\SegurancaDbController;
use App\Controllers\SegurancaObjetoDbController;

use App\Controllers\SystemMessageController;
use App\Controllers\TokenCsrfController;
use App\Controllers\UnidadeDbController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
// use App\Controllers\SystemUploadDbController;
use Exception;

class UsuarioApiController extends ResourceController
{
    use ResponseTrait;
    private $dbFields;
    private $uri;
    private $tokenCsrf;
    private $DbController;
    private $DbCadastro;
    private $DbProfissional;
    private $DbSeguranca;
    private $DbMenu;
    private $DbSegurancaObjeto;
    private $DbUnidades;
    private $message;

    public function __construct()
    {
        // $this->DbController = new SystemUploadDbController();
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
        $this->DbController = new UsuarioDbController();
        $this->DbCadastro = new CadastroDbController();
        $this->DbProfissional = new ProfissionalDbController();
        $this->DbMenu = new MenuDbController();
        $this->DbSeguranca = new SegurancaDbController();
        $this->DbSegurancaObjeto = new SegurancaObjetoDbController();
        $this->message = new SystemMessageController();
        $this->DbUnidades = new UnidadeDbController();
        $this->tokenCsrf = new TokenCsrfController();
    }

    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function index()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }


    private function setApiRespond(string $status = 'success', string $getMethod = 'GET', array $requestDb = array(), $message = 'API loading data (dados para carregamento da API)')
    {
        # $message = 'API loading data (dados para carregamento da API)',
        $apiRespond = [
            'status' => $status,
            'message' => $message,
            'date' => date('Y-m-d'),
            'api' => [
                'version' => '1.0',
                'method' => $getMethod,
                'description' => 'API Description',
                'content_type' => 'application/x-www-form-urlencoded'
            ],
            'result' => $requestDb,
            'metadata' => [
                'page_title' => 'Application title',
                'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                // Você pode adicionar campos comentados anteriormente se forem relevantes
                // 'method' => '__METHOD__',
                // 'function' => '__FUNCTION__',
            ]
        ];

        return $apiRespond;
    }

    private function saveRequest(bool $choice_update = false, string $token_csrf = 'erro', array $processRequest = array())
    {
        $processRequestSuccess = false;
        $server = $_SERVER['SERVER_NAME'];
        if ($server !== '127.0.0.1') {
            $passToken = $this->tokenCsrf->valid_token_csrf($token_csrf);
        } else {
            $passToken = true;
        }
        if ($choice_update === true) {
            if ($passToken) {
                $id = (isset($processRequest['id'])) ? ($processRequest['id']) : (array());
                $dbResponse = $this->DbController->dbUpdate($id, $processRequest);
                if (isset($dbResponse["affectedRows"]) && $dbResponse["affectedRows"] > 0) {
                    $processRequestSuccess = true;
                }
            }
        } elseif ($choice_update === false) {
            if ($passToken) {
                // return $this->response->setJSON($processRequest, 200);
                $dbResponse = $this->DbController->dbCreate($processRequest);
                if (isset($dbResponse["affectedRows"]) && $dbResponse["affectedRows"] > 0) {
                    $processRequestSuccess = true;
                }
            }
        } else {
            $this->message->message(['ERRO: Dados enviados inválidos'], 'danger');
            $dbResponse = array();
            $processRequestSuccess = false;
        }
        #
        $dbSave = [
            'processRequestSuccess' => $processRequestSuccess,
            'dbResponse' => $dbResponse,
            'status' => !isset($processRequestSuccess) || $processRequestSuccess !== true ? 'trouble' : 'success',
            'message' => !isset($processRequestSuccess) || $processRequestSuccess !== true ? 'Erro - requisição que foi bem-formada mas não pôde ser seguida devido a erros semânticos.' : 'API loading data (dados para carregamento da API)',
            'cod_http' => !isset($processRequestSuccess) || $processRequestSuccess !== true ? 422 : 201,
        ];
        #
        return $dbSave;
    }

    # route POST /www/index.php/fia/ptpa/usuario/api/access/(:any)
    # route GET /www/index.php/fia/ptpa/usuario/api/access/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function getCPF($parameter = NULL)
    {
        // Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $processRequest = (array) $request->getVar();
        $json = 1;

        # Limpando o CPF
        if ($parameter !== NULL) {
            $cpf = '/' . myChar($parameter);
            $cpf2 = myChar($parameter);
        } elseif (isset($processRequest['cpf'])) {
            $cpf = '/' . myChar($processRequest['cpf']);
            $cpf2 = myChar($processRequest['cpf']);
        } else {
            $cpf = NULL;
            $cpf2 = NULL;
        }
        // myPrint($cpf, $cpf2);

        #
        try {
            $endPoint = myEndPoint('https://cpf-consulta.rj.gov.br/buscar-por-cpf' . $cpf, '125f8d3b3f9c0bf81ef56936d4d932eb');
            $requestJSONform['SIGRH'] = (isset($endPoint['result'])) ? ($endPoint['result']) : (array('error' => 'HTTP request failed with code 403'));

            #
            $dbfilter = array(
                'CPF' => formatarCPF($cpf2)
            );
            $dbRequest = $this->DbProfissional->dbFilter($dbfilter);
            $requestJSONform['FIA'] = isset($dbRequest['dbResponse'][0]) ? ($dbRequest['dbResponse'][0]) : (array('error' => 'HTTP request failed with code 403'));
            // myPrint('$requestJSONform :: ', $requestJSONform);

            #
            $apiRespond = array(
                'name_session' => 'user_session',
                'time_in_seconds' => 36000,
                'dados' => array(
                    'SIGRH' => $requestJSONform['SIGRH'],
                    'FIA' => array(
                        'profissional_id' => isset($requestJSONform['FIA']['id']) ? ($requestJSONform['FIA']['id']) : (''),
                        'Nome' => isset($requestJSONform['FIA']['Nome']) ? ($requestJSONform['FIA']['Nome']) : (''),
                        'PerfilId' => isset($requestJSONform['FIA']['PerfilId']) ? ($requestJSONform['FIA']['PerfilId']) : (''),
                        'PerfilDescricao' => isset($requestJSONform['FIA']['PerfilDescricao']) ? ($requestJSONform['FIA']['PerfilDescricao']) : (''),
                        'CargoFuncaoId' => isset($requestJSONform['FIA']['CargoFuncaoId']) ? ($requestJSONform['FIA']['CargoFuncaoId']) : (''),
                        'CargoFuncao' => isset($requestJSONform['FIA']['CargoFuncao']) ? ($requestJSONform['FIA']['CargoFuncao']) : (''),
                        'UnidadeId' => isset($requestJSONform['FIA']['UnidadeId']) ? ($requestJSONform['FIA']['UnidadeId']) : (''),
                        'NomeUnidade' => isset($requestJSONform['FIA']['NomeUnidade']) ? ($requestJSONform['FIA']['NomeUnidade']) : (''),
                        'MunicipioUnidade' => isset($requestJSONform['FIA']['MunicipioUnidade']) ? ($requestJSONform['FIA']['MunicipioUnidade']) : (''),
                        'EnderecoUnidade' => isset($requestJSONform['FIA']['EnderecoUnidade']) ? ($requestJSONform['FIA']['EnderecoUnidade']) : (''),
                    ),
                )
            );
            #
            session()->set($apiRespond['name_session'], $apiRespond['dados']);
            session()->markAsTempdata($apiRespond['name_session'], $apiRespond['time_in_seconds']);
            #
            $varSession = (session()->get('session_name')) ? (session()->get('session_name')) : (array());
            // myPrint($apiRespond, 'src\app\Controllers\UsuarioApiController.php Linha 102', true);
            // myPrint($requestJSONform, 'src\app\Controllers\UsuarioApiController.php Linha 103');
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
                'metadata' => [
                    'page_title' => 'Application title',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                    // Você pode adicionar campos comentados anteriormente se forem relevantes
                    // 'method' => '__METHOD__',
                    // 'function' => '__FUNCTION__',
                ]
            ];
            $response = $this->response->setJSON($apiRespond, 201);
        } catch (\Exception $e) {
            $apiRespond = array(
                'message' => array('danger' => $e->getMessage()),
                'page_title' => 'Application title',
                'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
            );
            $this->message->message($message = array(), 'danger', $parameter, 5);
            $response = $this->response->setJSON($apiRespond, 500);
        }
        if ($json == 1) {
            // return $response;
            // return redirect()->back();
            return redirect()->to('fia/ptpa/principal/endpoint/indicadores');
        } else {
            return $response;
        }
    }

    #
    # route POST /www/index.php/fia/ptpa/usuario/api/sair/(:any)
    # route GET /www/index.php/fia/ptpa/usuario/api/sair/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function exit($parameter = NULL)
    {
        // Carrega o serviço de sessão
        $session = \Config\Services::session();

        // Remove todas as sessões
        $session->destroy();
        #
        try {
            #
            $apiRespond = [
                'status' => 'success',
                'message' => 'API loading data (dados para carregamento da API)',
                'date' => date('Y-m-d'),
                'api' => [
                    'version' => '1.0',
                    'method' => 'Sair',
                    'description' => 'API Description',
                    'content_type' => 'application/x-www-form-urlencoded'
                ],
                // 'method' => '__METHOD__',
                // 'function' => '__FUNCTION__',
                'result' => 'sair',
                'metadata' => [
                    'page_title' => 'Application title',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                    // Você pode adicionar campos comentados anteriormente se forem relevantes
                    // 'method' => '__METHOD__',
                    // 'function' => '__FUNCTION__',
                ]
            ];
            $response = $this->response->setStatusCode(201)->setJSON($apiRespond);
        } catch (\Exception $e) {
            $apiRespond = array(
                'message' => array('danger' => $e->getMessage()),
                'page_title' => 'Application title',
                'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
            );
            $this->message->message($message = array(), 'danger', $parameter, 5);
            $response = $this->response->setStatusCode(500)->setJSON($apiRespond);
        }
        return redirect()->back();
    }
    #

    # route POST /www/Usuario/group/api/teste/(:any)
    # route GET /www/Usuario/group/api/teste/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function onRest($parameter = NULL)
    {
        // Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        // $processRequest = eagarScagaire($processRequest);
        //
        try {
            //
            $id = isset($processRequest['id']) ? ($processRequest['id']) : ($parameter);
            $requestDb = $this->DbController->dbRead($id);
            //
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
                'result' => $requestDb,
                'metadata' => [
                    'page_title' => 'Application title',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                    // Você pode adicionar campos comentados anteriormente se forem relevantes
                    // 'method' => '__METHOD__',
                    // 'function' => '__FUNCTION__',
                ]
            ];
            $response = $this->response->setJSON($apiRespond, 201);
        } catch (\Exception $e) {
            $apiRespond = array(
                'message' => array('danger' => $e->getMessage()),
                'page_title' => 'Application title',
                'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
            );
            $this->message->message($message = array(), 'danger', $parameter, 5);
            $response = $this->response->setJSON($apiRespond, 500);
        }
        if ($json == 1) {
            return $response;
            // return redirect()->back();
            // return redirect()->to('project/endpoint/parameter/parameter/' . $parameter);
        } else {
            return $response;
        }
    }

    # route POST /www/Usuario/group/api/criar/(:any)
    # route GET /www/Usuario/group/api/criar/(:any)
    # route POST /Usuario/group/api/atualizar/(:any)
    # route GET /Usuario/group/api/atualizar/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function create_update($parameter = NULL)
    {
        // Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $getVar_page = $request->getVar('page');
        $processRequest = (array) $request->getVar();
        // $uploadedFiles = $request->getFiles();
        $token_csrf = (isset($processRequest['token_csrf']) ? $processRequest['token_csrf'] : NULL);
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        $choice_update = (isset($processRequest['id']) && !empty($processRequest['id'])) ? (true) : (false);
        //
        if ($choice_update === true) {
            if ($this->tokenCsrf->valid_token_csrf($token_csrf)) {
                $id = (isset($processRequest['id'])) ? ($processRequest['id']) : (array());
                $dbResponse = $this->DbController->dbUpdate($id, $processRequest);
                if (isset($dbResponse['affectedRows']) && $dbResponse['affectedRows'] > 0) {
                    $processRequestSuccess = true;
                }
            }
        } elseif ($choice_update === false) {
            if ($this->tokenCsrf->valid_token_csrf($token_csrf)) {
                $dbResponse = $this->DbController->dbCreate($processRequest);
                if (isset($dbResponse['affectedRows']) && $dbResponse['affectedRows'] > 0) {
                    $processRequestSuccess = true;
                }
            }
        } else {
            $this->message->message(['ERRO: Dados enviados inválidos'], 'danger');
        }
        ;
        $status = (!isset($processRequestSuccess) || $processRequestSuccess !== true) ? ('trouble') : ('success');
        $message = (!isset($processRequestSuccess) || $processRequestSuccess !== true) ? ('Erro - requisição que foi bem-formada mas não pôde ser seguida devido a erros semânticos.') : ('API loading data (dados para carregamento da API)');
        $cod_http = (!isset($processRequestSuccess) || $processRequestSuccess !== true) ? (422) : (201);
        $apiRespond = [
            'status' => $status,
            'message' => $message,
            'date' => date('Y-m-d'),
            'api' => [
                'version' => '1.0',
                'method' => $getMethod,
                'description' => 'API Description',
                'content_type' => 'application/x-www-form-urlencoded'
            ],
            // 'method' => '__METHOD__',
            // 'function' => '__FUNCTION__',
            'result' => $dbResponse,
            'metadata' => [
                'page_title' => 'Application title',
                'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                // Você pode adicionar campos comentados anteriormente se forem relevantes
                // 'method' => '__METHOD__',
                // 'function' => '__FUNCTION__',
            ]
        ];
        try {
            $response = $this->response->setJSON($apiRespond, $cod_http);
        } catch (\Exception $e) {
            $apiRespond = [
                'status' => 'error',
                'message' => $e->getMessage(),
                'date' => date('Y-m-d'),
                'api' => [
                    'version' => '1.0',
                    'method' => isset($getMethod) ? $getMethod : 'unknown',
                    'description' => 'API Criar Method',
                    'content_type' => 'application/x-www-form-urlencoded'
                ],
                'metadata' => [
                    'page_title' => 'ERRO - API Method',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                ]
            ];
            $response = $this->response->setJSON($apiRespond, 500);
        }
        if (!$json) {
            return $response;
            // return redirect()->to('project/endpoint/parameter/parameter/' . $parameter);
        } else {
            return $response;
            // return redirect()->back();
        }
    }

    # route POST /# www/index.php/fia/ptpa/usuario/api/filtrar/(:any)
    # route GET /# www/index.php/fia/ptpa/usuario/api/filtrar/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbFilter($parameter = NULL)
    {

        # Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $pageGet = $this->request->getGet('page');
        $page = (isset($pageGet) && !empty($pageGet)) ? ($pageGet) : (1);
        $processRequest = (array) $request->getVar();
        $processRequest = array_filter($processRequest);
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        #
        // myPrint($processRequest, 'src\app\Controllers\UsuarioApiController.php');
        try {
            #
            // return $this->response->setJSON($processRequest, 200);
            $requestDb = $this->DbController->dbFilter($processRequest);
            // myPrint($requestDb, 'src\app\Controllers\UsuarioApiController.php');
            #
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
                'result' => $requestDb,
                'metadata' => [
                    'page_title' => 'Application title',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                    // Você pode adicionar campos comentados anteriormente se forem relevantes
                    // 'method' => '__METHOD__',
                    // 'function' => '__FUNCTION__',
                ]
            ];
            $response = $this->response->setJSON($apiRespond, 201);
        } catch (\Exception $e) {
            $apiRespond = array(
                'message' => array('danger' => $e->getMessage()),
                'page_title' => 'Application title',
                'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
            );
            $this->message->message($message = array(), 'danger', $parameter, 5);
            $response = $this->response->setJSON($apiRespond, 500);
        }
        if ($json == 1) {
            return $response;
            // return redirect()->back();
            // return redirect()->to('project/endpoint/parameter/parameter/' . $parameter);
        } else {
            return $response;
        }
    }

    # route POST /www/Usuario/group/api/excluir/(:any)
    # route GET /www/Usuario/group/api/excluir/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbDelete($parameter = NULL)
    {
        $request = service('request');
        $getMethod = $request->getMethod();
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        try {
            $requestDb = $this->DbController->dbDelete($parameter);
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
                'result' => $requestDb,
                'metadata' => [
                    'page_title' => 'Application title',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                    // Você pode adicionar campos comentados anteriormente se forem relevantes
                    // 'method' => '__METHOD__',
                    // 'function' => '__FUNCTION__',
                ]
            ];
            $response = $this->response->setJSON($apiRespond, 201);
        } catch (\Exception $e) {
            $apiRespond = array(
                'message' => array('danger' => $e->getMessage()),
                'page_title' => 'Application title',
                'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
            );
            // $this->returnFunction(array($e->getMessage()), 'danger',);
            $response = $this->response->setJSON($apiRespond, 500);
        }
        if ($json == 1) {
            return $response;
            // return redirect()->back();
            // return redirect()->to('project/endpoint/parameter/parameter/' . $parameter);
        } else {
            return $response;
        }
    }

    # route POST /www/Usuario/group/api/limpar/(:any)
    # route GET /www/Usuario/group/api/limpar/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbCleaner($parameter = NULL)
    {
        $request = service('request');
        $getMethod = $request->getMethod();
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        try {
            $dbResponse = $this->DbController->dbCleaner($parameter);
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
                'result' => $dbResponse,
                'metadata' => [
                    'page_title' => 'Application title',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                    // Você pode adicionar campos comentados anteriormente se forem relevantes
                    // 'method' => '__METHOD__',
                    // 'function' => '__FUNCTION__',
                ]
            ];
            $response = $this->response->setJSON($apiRespond, 201);
        } catch (\Exception $e) {
            $apiRespond = array(
                'message' => array('danger' => $e->getMessage()),
                'page_title' => 'Application title',
                'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
            );
            // $this->returnFunction(array($e->getMessage()), 'danger',);
            $response = $this->response->setJSON($apiRespond, 500);
        }
        if ($json == 1) {
            return $response;
            // return redirect()->back();
            // return redirect()->to('project/endpoint/parameter/parameter/' . $parameter);
        } else {
            return $response;
        }
    }

    #
    # route POST /www/index.php/fia/ptpa/usuario/api/seguranca/(:any)
    # route GET /www/index.php/fia/ptpa/usuario/api/seguranca/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbSeguranca($parameter = NULL)
    {
        # Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $pageGet = $this->request->getGet('page');
        $page = (isset($pageGet) && !empty($pageGet)) ? ($pageGet) : (1);
        $limitGet = $this->request->getGet('limit');
        $limit = (isset($limitGet) && !empty($limitGet)) ? ($limitGet) : (10);
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        #
        // myPrint($getMethod, 'C:\Users\Habilidade.Com\AppData\Roaming\Code\User\snippets\php.json');
        try {
            #
            $id = isset($processRequest['id']) ? ($processRequest['id']) : ($parameter);
            $requestDb = $this->DbSeguranca->dbRead($id, $page, $limit);
            #
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
                'result' => $requestDb,
                'metadata' => [
                    'page_title' => 'Application title',
                    'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
                    // Você pode adicionar campos comentados anteriormente se forem relevantes
                    // 'method' => '__METHOD__',
                    // 'function' => '__FUNCTION__',
                ]
            ];
            $response = $this->response->setJSON($apiRespond, 201);
        } catch (\Exception $e) {
            $apiRespond = array(
                'message' => array('danger' => $e->getMessage()),
                'page_title' => 'Application title',
                'getURI' => $this->uri->getSegments(),
                    'environment' => ENVIRONMENT_CHOICE,
            );
            $this->message->message($message = array(), 'danger', $parameter, 5);
            $response = $this->response->setJSON($apiRespond, 500);
        }
        if ($json == 1) {
            return $response;
            // return redirect()->back();
            // return redirect()->to('project/endpoint/parameter/parameter/' . $parameter);
        } else {
            return $response;
        }
    }

    # route POST /www/index.php/fia/ptpa/security/api/assuredness/(:any)
    # route GET /www/index.php/fia/ptpa/security/api/assuredness/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function gerarSeguranca($parameter = NULL)
    {
        # Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $pageGet = $this->request->getGet('page');
        $limitGet = $this->request->getGet('limit');
        $limit = (isset($limitGet) && !empty($limitGet)) ? ($limitGet) : (10);
        $page = (isset($pageGet) && !empty($pageGet)) ? ($pageGet) : (1);
        $processRequest = (array) $request->getVar();
        // myPrint('$processRequest ::', $processRequest, true);
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        $id = isset($processRequest['id']) ? ($processRequest['id']) : ($parameter);
        #
        $projeto = (String) 'fia';
        $sub_projeto = (String) 'ptpa';
        $perfil_id = (String) isset($processRequest['select_perfil']) ? $processRequest['select_perfil'] : '';
        $cargo_funcao_id = (String) isset($processRequest['select_cargo_funcao']) ? $processRequest['select_cargo_funcao'] : '';
        $permitido = (String) isset($processRequest['permitido']) ? $processRequest['permitido'] : '';
        #
        $parameterDbSeguranca = array(
            'pf_id' => $perfil_id,
            'cf_id' => $cargo_funcao_id,
        );
        $dbReturnDbSeguranca = $this->DbSeguranca->dbFilter($parameterDbSeguranca, $page = 1, $limit = 1);
        $dbReturnDbSeguranca = isset($dbReturnDbSeguranca['dbResponse'][0]) ? false : true;
        #
        $parameterDbMenu = array(
            'id_perfil' => $perfil_id,
            'id_cargo' => $cargo_funcao_id,
        );
        $dbReturnDbMenu = $this->DbMenu->dbFilter($parameterDbMenu, $page = 1, $limit = 1);
        $dbReturnDbMenu = isset($dbReturnDbMenu['dbResponse'][0]) ? false : true;
        #
        $parameterDbSegurancaObjeto = array(
            'pf_id' => $perfil_id,
            'cf_id' => $cargo_funcao_id,
        );
        $dbReturnDbSegurancaObjeto = $this->DbSegurancaObjeto->dbFilter($parameterDbSegurancaObjeto, $page = 1, $limit = 1);
        $dbReturnDbSegurancaObjeto = isset($dbReturnDbSegurancaObjeto['dbResponse'][0]) ? false : true;
        // myPrint('$dbReturnDbMenu ::', $dbReturnDbMenu, true);
        // myPrint('$dbReturnDbSeguranca ::', $dbReturnDbSeguranca, true);
        // myPrint('$dbReturnDbSegurancaObjeto ::', $dbReturnDbSegurancaObjeto);
        #
        $qtd_respostas = 0;
        // myPrint('$projeto ::', $projeto, true);
        // myPrint('$sub_projeto ::', $sub_projeto, true);
        // myPrint('$perfil_id ::', $perfil_id, true);
        // myPrint('$cargo_funcao_id ::', $cargo_funcao_id, true);
        // myPrint('$permitido ::', $permitido, true);
        #

        #
        $menu = array(
            "Funcionários",
            "Alocar Funcionário",
            "Unidades",
            "Períodos",
            "Adolescente",
            "Prontuário",
        );
        #
        $modulo2 = array(
            "adolescente",
            "alocarfuncionario",
            "cargofuncao",
            "genero",
            "perfil",
            "periodo",
            "profissional",
            "programa",
            "prontuario",
            "unidade",
            "usuario"
        );
        #
        $metodo_acao = array(
            "atualizar",
            "cadastrar",
            "deletar",
            "exibir",
            "consultar",
            "filtrar",
            "filtrarlixo",
            "limpar"
        );
        #
        $adolescente_cadastrar_atualizar = array(
            'cmp_data_nascimento',
            'cmp_rg',
            'cmp_orgao_expedidor',
            'cmp_cep',
            'cmp_endereco',
            'cmp_numero',
            'cmp_complemento',
            'cmp_municipio',
            'cmp_unidade',
            'cmp_genero',
            'cmp_etnia',
            'cmp_sexo',
            'cmp_matricula_certidao_nascimento',
            'cmp_numero_registro',
            'cmp_zona',
            'cmp_folha',
            'cmp_livro',
            'cmp_circunscricao',
            'cmp_tipo_escola',
            'cmp_escolaridade',
            'cmp_nome_escola',
            'cmp_turno_escolar',
            'cmp_nome',
            'btn_salvar',
            'cmp_cpf'
        );
        $adolescente_exibir = array(
            'col_nome',
            'col_cpf',
            'col_data_nascimento',
            'col_telefone_responsavel',
            'col_unidade_desejada',
            'col_cpf_responsavel',
            'btn_editar'
        );
        $adolescente_consultar = array(
            'undefined'
        );
        $alocarfuncionario_cadastrar_atualizar = array(
            'cmp_nome_completo',
            'cmp_cpf',
            'cmp_email',
            'cmp_telefone',
            'cmp_programas',
            'cmp_perfil',
            'cmp_cargo',
            'cmp_admissão',
            'cmp_unidade',
            'btn_salvar'
        );
        $alocarfuncionario_exibir = array(
            'cmp_nome',
            'cmp_endereco',
            'cmp_municipio',
            'cmp_capacidade',
            'cmp_data_cadastro',
            'btn_consultar'
        );
        $alocarfuncionario_consultar = array(
            'cmp_nome',
            'cmp_capacidade_atendimento',
            'cmp_endereco',
            'cmp_municipio',
            'col_nome',
            'col_cpf',
            'col_email',
            'col_telefone',
            'col_cargo_funcao',
            'col_programa_fia',
            'col_data_dmissao',
            'col_data_demissao'
        );
        $cargofuncao_cadastrar_atualizar = array(
            'undefined'
        );
        $cargofuncao_exibir = array(
            'undefined'
        );
        $cargofuncao_consultar = array(
            'undefined'
        );
        $genero_cadastrar_atualizar = array(
            'undefined'
        );
        $genero_exibir = array(
            'undefined'
        );
        $genero_consultar = array(
            'undefined'
        );
        $perfil_cadastrar_atualizar = array(
            'undefined'
        );
        $perfil_exibir = array(
            'undefined'
        );
        $perfil_consultar = array(
            'undefined'
        );
        $periodo_cadastrar_atualizar = array(
            'cmp_periodo',
            'cmp_ano',
            'cmp_inicio_periodo',
            'cmp_termino_periodo',
            'btn_salvar'
        );
        $periodo_exibir = array(
            'col_ano',
            'col_periodo',
            'capacidade_periodo',
            'col_municipio',
            'col_unidade',
        );
        $periodo_consultar = array(
            'undefined'
        );
        $profissional_cadastrar_atualizar = array(
            'cmp_nome_completo',
            'cmp_cpf',
            'cmp_email',
            'cmp_telefone',
            'cmp_programas',
            'cmp_perfil',
            'cmp_cargo',
            'cmp_admissão',
            'btn_salvar'
        );
        $profissional_exibir = array(
            'col_nome',
            'col_email',
            'col_telefone',
            'col_cargo_funcao',
            'col_programa_fia',
            'col_perfil',
            'col_admissao',
            'col_demissao',
            'btn_editar',
            'btn_consultar',
            'btn_excluir',
        );
        $profissional_consultar = array(
            'undefined'
        );
        $programa_cadastrar_atualizar = array(
            'undefined'
        );
        $programa_exibir = array(
            'undefined'
        );
        $programa_consultar = array(
            'undefined'
        );
        $prontuario_cadastrar_atualizar = array(
            'undefined'
        );
        $prontuario_exibir = array(
            'undefined'
        );
        $prontuario_consultar = array(
            'undefined'
        );
        $unidade_cadastrar_atualizar = array(
            'cmp_nome',
            'cmp_capacidade_atendimento',
            'cmp_endereco',
            'cmp_municipio',
            'btn_salvar',
            'cmp_nome',
            'cmp_capacidade_atendimento',
            'cmp_endereco',
            'cmp_municipio',
            'btn_salvar'
        );
        $unidade_exibir = array(
            'col_nome',
            'col_endereco',
            'col_municipio',
            'col_capacidade',
            'col_data_cadastro',
            'btn_editar',
            'btn_consultar',
            'btn_excluir'
        );
        $unidade_consultar = array(
            'undefined'
        );
        $usuario_cadastrar_atualizar = array(
            'undefined'
        );
        $usuario_exibir = array(
            'undefined'
        );
        $usuario_consultar = array(
            'undefined'
        );
        #
        $indice = 1;
        if ($dbReturnDbSeguranca) {
            foreach ($modulo2 as $key_modulo => $value_modulo) {
                // myPrint('$value_modulo :: ', $value_modulo, true);
                foreach ($metodo_acao as $key_metodo_acao => $value_metodo_acao) {
                    $dbCreate = array(
                        'perfil_id' => $perfil_id,
                        'cargo_funcao_id' => $cargo_funcao_id,
                        'projeto' => $projeto,
                        'sub_projeto' => $sub_projeto,
                        'modulo' => $value_modulo,
                        'metodo_acao' => $value_metodo_acao,
                        'permitido' => $permitido
                    );
                    $this->DbSeguranca->dbCreate($dbCreate);
                    $qtd_respostas++;
                }
            }
        }
        #
        $indice = 1;
        if ($dbReturnDbSegurancaObjeto) {
            foreach ($modulo2 as $key_modulo => $value_modulo) {
                // myPrint('$value_modulo :: ', $value_modulo, true);
                foreach ($metodo_acao as $key_metodo_acao => $value_metodo_acao) {
                    #
                    if ($value_modulo === "adolescente" && $value_metodo_acao === "atualizar") {
                        foreach ($adolescente_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "adolescente" && $value_metodo_acao === "cadastrar") {
                        foreach ($adolescente_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "adolescente" && $value_metodo_acao === "exibir") {
                        foreach ($adolescente_exibir as $key_exibir => $value_exibir) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_exibir,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "adolescente" && $value_metodo_acao === "consultar") {
                        foreach ($adolescente_consultar as $key_consultar => $value_consultar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_consultar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "alocarfuncionario" && $value_metodo_acao === "atualizar") {
                        foreach ($alocarfuncionario_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "alocarfuncionario" && $value_metodo_acao === "cadastrar") {
                        foreach ($alocarfuncionario_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "alocarfuncionario" && $value_metodo_acao === "exibir") {
                        foreach ($alocarfuncionario_exibir as $key_exibir => $value_exibir) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_exibir,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "alocarfuncionario" && $value_metodo_acao === "consultar") {
                        foreach ($alocarfuncionario_consultar as $key_consultar => $value_consultar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_consultar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "cargofuncao" && $value_metodo_acao === "atualizar") {
                        foreach ($cargofuncao_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "cargofuncao" && $value_metodo_acao === "cadastrar") {
                        foreach ($cargofuncao_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "cargofuncao" && $value_metodo_acao === "exibir") {
                        foreach ($cargofuncao_exibir as $key_exibir => $value_exibir) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_exibir,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "cargofuncao" && $value_metodo_acao === "consultar") {
                        foreach ($cargofuncao_consultar as $key_consultar => $value_consultar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_consultar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "genero" && $value_metodo_acao === "atualizar") {
                        foreach ($genero_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "genero" && $value_metodo_acao === "cadastrar") {
                        foreach ($genero_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "genero" && $value_metodo_acao === "exibir") {
                        foreach ($genero_exibir as $key_exibir => $value_exibir) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_exibir,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "genero" && $value_metodo_acao === "consultar") {
                        foreach ($genero_consultar as $key_consultar => $value_consultar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_consultar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "perfil" && $value_metodo_acao === "atualizar") {
                        foreach ($perfil_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "perfil" && $value_metodo_acao === "cadastrar") {
                        foreach ($perfil_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "perfil" && $value_metodo_acao === "exibir") {
                        foreach ($perfil_exibir as $key_exibir => $value_exibir) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_exibir,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "perfil" && $value_metodo_acao === "consultar") {
                        foreach ($perfil_consultar as $key_consultar => $value_consultar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_consultar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "periodo" && $value_metodo_acao === "atualizar") {
                        foreach ($periodo_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "periodo" && $value_metodo_acao === "cadastrar") {
                        foreach ($periodo_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "periodo" && $value_metodo_acao === "exibir") {
                        foreach ($periodo_exibir as $key_exibir => $value_exibir) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_exibir,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "periodo" && $value_metodo_acao === "consultar") {
                        foreach ($periodo_consultar as $key_consultar => $value_consultar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_consultar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "profissional" && $value_metodo_acao === "atualizar") {
                        foreach ($profissional_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "profissional" && $value_metodo_acao === "cadastrar") {
                        foreach ($profissional_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "profissional" && $value_metodo_acao === "exibir") {
                        foreach ($profissional_exibir as $key_exibir => $value_exibir) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_exibir,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "profissional" && $value_metodo_acao === "consultar") {
                        foreach ($profissional_consultar as $key_consultar => $value_consultar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_consultar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "programa" && $value_metodo_acao === "atualizar") {
                        foreach ($programa_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "programa" && $value_metodo_acao === "cadastrar") {
                        foreach ($programa_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "programa" && $value_metodo_acao === "exibir") {
                        foreach ($programa_exibir as $key_exibir => $value_exibir) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_exibir,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "programa" && $value_metodo_acao === "consultar") {
                        foreach ($programa_consultar as $key_consultar => $value_consultar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_consultar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "prontuario" && $value_metodo_acao === "atualizar") {
                        foreach ($prontuario_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "prontuario" && $value_metodo_acao === "cadastrar") {
                        foreach ($prontuario_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "prontuario" && $value_metodo_acao === "exibir") {
                        foreach ($prontuario_exibir as $key_exibir => $value_exibir) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_exibir,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "prontuario" && $value_metodo_acao === "consultar") {
                        foreach ($prontuario_consultar as $key_consultar => $value_consultar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_consultar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "unidade" && $value_metodo_acao === "atualizar") {
                        foreach ($unidade_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "unidade" && $value_metodo_acao === "cadastrar") {
                        foreach ($unidade_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "unidade" && $value_metodo_acao === "exibir") {
                        foreach ($unidade_exibir as $key_exibir => $value_exibir) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_exibir,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "unidade" && $value_metodo_acao === "consultar") {
                        foreach ($unidade_consultar as $key_consultar => $value_consultar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_consultar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "usuario" && $value_metodo_acao === "atualizar") {
                        foreach ($usuario_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "usuario" && $value_metodo_acao === "cadastrar") {
                        foreach ($usuario_cadastrar_atualizar as $key_cadastrar_atualizar => $value_cadastrar_atualizar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_cadastrar_atualizar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "usuario" && $value_metodo_acao === "exibir") {
                        foreach ($usuario_exibir as $key_exibir => $value_exibir) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_exibir,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                    #
                    if ($value_modulo === "usuario" && $value_metodo_acao === "consultar") {
                        foreach ($usuario_consultar as $key_consultar => $value_consultar) {
                            $dbCreate = array(
                                'perfil_id' => $perfil_id,
                                'cargo_funcao_id' => $cargo_funcao_id,
                                'modulo' => $value_modulo,
                                'metodo_acao' => $value_metodo_acao,
                                'objeto' => $value_consultar,
                                'permitido' => $permitido
                            );
                            $this->DbSegurancaObjeto->dbCreate($dbCreate);
                            $qtd_respostas++;
                        }
                    }
                }
            }
        }
        $indice = 1;
        if($dbReturnDbMenu){ 
            foreach ($menu as $key_menu => $value_menu) {
                $dbCreate = array(
                    'id_perfil' => $perfil_id,
                    'id_cargo' => $cargo_funcao_id,
                    'menu' => $value_menu,
                    'permissao' => $permitido
                );
                $this->DbMenu->dbCreate($dbCreate);
                $qtd_respostas++;
            }
        }
        #
        try {
            #
            $requestDb = [
                'affectedRows' => $qtd_respostas,
            ];
            #
            $apiRespond = $this->setApiRespond('success', $getMethod, $requestDb);
            $response = $this->response->setStatusCode(201)->setJSON($apiRespond);
        } catch (\Exception $e) {
            $apiRespond = $this->setApiRespond('error', $getMethod, $requestDb, $e->getMessage());
            // myPrint('Exception $e :: ', $e->getMessage());
            $response = $this->response->setStatusCode(500)->setJSON($apiRespond);
        }
        if ($json == 1) {
            return $response;
            // return redirect()->back();
            // return redirect()->to('project/endpoint/parameter/parameter/' . $parameter);
        } else {
            return $response;
        }
    }

    # route POST /www/index.php/fia/ptpa/security/api/sheltered/(:any)
    # route GET /www/index.php/fia/ptpa/security/api/sheltered/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function removeSeguranca($parameter = NULL)
    {
        # Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $pageGet = $this->request->getGet('page');
        $limitGet = $this->request->getGet('limit');
        $limit = (isset($limitGet) && !empty($limitGet)) ? ($limitGet) : (10);
        $page = (isset($pageGet) && !empty($pageGet)) ? ($pageGet) : (1);
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        $id = isset($processRequest['id']) ? ($processRequest['id']) : ($parameter);
        #
        // myPrint($processRequest, 'C:\Users\Habilidade.Com\AppData\Roaming\Code\User\snippets\php.json');
        #
        try {
            #
            $dbDelete = array(
                'perfil_id' => isset($processRequest['select_perfil']) ? ($processRequest['select_perfil']) : (null),
                'cargo_funcao_id' => isset($processRequest['select_cargo_funcao']) ? ($processRequest['select_cargo_funcao']) : (null)
            );
            $requestDb = $this->DbSeguranca->dbDelete($dbDelete);
            #
            $apiRespond = $this->setApiRespond('success', $getMethod, $requestDb);
            $response = $this->response->setStatusCode(201)->setJSON($apiRespond);
        } catch (\Exception $e) {
            $apiRespond = $this->setApiRespond('error', $getMethod, $requestDb, $e->getMessage());
            // myPrint('Exception $e :: ', $e->getMessage());
            $response = $this->response->setStatusCode(500)->setJSON($apiRespond);
        }
        if ($json == 1) {
            return $response;
            // return redirect()->back();
            // return redirect()->to('project/endpoint/parameter/parameter/' . $parameter);
        } else {
            return $response;
        }
    }

}
