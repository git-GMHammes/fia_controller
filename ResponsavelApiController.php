<?php

namespace App\Controllers;

use App\Controllers\AdolescenteDbController;
use App\Controllers\ResponsavelDbController;
use App\Controllers\SystemMessageController;
use App\Controllers\TokenCsrfController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
// use App\Controllers\SystemUploadDbController;
use Exception;

class ResponsavelApiController extends ResourceController
{
    use ResponseTrait;

    private $ModelResponse;
    private $dbFields;
    private $uri;
    private $tokenCsrf;
    private $DbResponsavel;
    private $DbAdolescente;
    private $message;

    public function __construct()
    {
        $this->DbResponsavel = new ResponsavelDbController();
        $this->DbAdolescente = new AdolescenteDbController();
        $this->tokenCsrf = new TokenCsrfController();
        $this->message = new SystemMessageController();
        // $this->DbResponsavel = new SystemUploadDbController();
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
    }

    //
    // route POST /www/sigla/rota
    // route GET /www/sigla/rota
    // Informação sobre o controller
    // retorno do controller [JSON]
    public function index()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }


    // route POST /www/exemple/group/api/criar/(:any)
    // route GET /www/exemple/group/api/criar/(:any)
    // route POST /exemple/group/api/atualizar/(:any)
    // route GET /exemple/group/api/atualizar/(:any)
    // Informação sobre o controller
    // retorno do controller [JSON]
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
        #
        if (
            isset($processRequest['id_adolescente']) &&
            isset($processRequest['perfil_id']) &&
            isset($processRequest['Responsavel_Nome']) &&
            isset($processRequest['Responsavel_CPF']) &&
            isset($processRequest['Responsavel_Email']) &&
            isset($processRequest['Responsavel_TelefoneMovel'])
        ) {
            $processRequest['perfil_id'] = $processRequest['perfil_id'];
            $processRequest['Nome'] = $processRequest['Responsavel_Nome'];
            $processRequest['CPF'] = $processRequest['Responsavel_CPF'];
            $processRequest['Email'] = $processRequest['Responsavel_Email'];
            $processRequest['TelefoneMovel'] = $processRequest['Responsavel_TelefoneMovel'];
        }
        #
        if ($choice_update === true) {
            if ($this->tokenCsrf->valid_token_csrf($token_csrf)) {
                $id = (isset($processRequest['id'])) ? ($processRequest['id']) : (array());
                $dbResponse = $this->DbResponsavel->dbUpdate($id, $processRequest);
                // myPrint($processRequest, 'src\app\Controllers\ResponsavelApiController.php');
                if (isset($dbResponse['affectedRows']) && $dbResponse['affectedRows'] > 0) {
                    $processRequestSuccess = true;
                }
            }
        } elseif ($choice_update === false) {
            if ($this->tokenCsrf->valid_token_csrf($token_csrf)) {
                $dbResponse = $this->DbResponsavel->dbCreate($processRequest);
                if (isset($dbResponse['affectedRows']) && $dbResponse['affectedRows'] > 0) {
                    $processRequestSuccess = true;
                }
            }
            #

        } else {
            $this->message->message(['ERRO: Dados enviados inválidos'], 'danger');
        }
        ;
        $status = (!isset($processRequestSuccess) || $processRequestSuccess !== true) ? ('trouble') : ('success');
        $message = (!isset($processRequestSuccess) || $processRequestSuccess !== true) ? ('Erro - requisição que foi bem-formada mas não pôde ser seguida devido a erros semânticos.') : ('API loading data (dados para carregamento da API)');
        $cod_http = (!isset($processRequestSuccess) || $processRequestSuccess !== true) ? (422) : (201);
        // myprint($status, 'src\app\Controllers\ResponsavelApiController.php');
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
        if ($json) {
            return $response;
        } elseif ($parameter !== null) {
            return redirect()->to('fia/ptpa/adolescente/endpoint/atualizar/' . $parameter);
        } elseif (isset($processRequest['id_adolescente'])) {
            $page = isset($getVar_page) && !empty($getVar_page) ? ('?page=' . $getVar_page) : ('?page=1');
            return redirect()->to('fia/ptpa/adolescente/endpoint/exibir/' . $processRequest['id_adolescente'] . $page);
        } elseif ($status == 'success') {
            $page = isset($getVar_page) && !empty($getVar_page) ? ('?page=' . $getVar_page) : ('?page=1');
            return redirect()->to('fia/ptpa/responsavel/endpoint/exibir/' . $parameter . $page);
        } else {
            return $response;
        }
    }

    // route POST /www/index.php/fia/ptpa/responsavel/api/exibir/(:any)
    // route GET /www/index.php/fia/ptpa/responsavel/api/exibir/(:any)
    // Informação sobre o controller
    // retorno do controller [JSON]
    public function dbRead($parameter1 = NULL)
    {
        // Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $pageGet = $this->request->getGet('page');
        $page = (isset($pageGet) && !empty($pageGet)) ? ($pageGet) : (1);
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        // $processRequest = eagarScagaire($processRequest);
        //
        try {
            //
            $id = isset($processRequest['id']) ? ($processRequest['id']) : ($parameter1);
            $requestDb = $this->DbResponsavel->dbRead($id, $page);
            // myPrint($requestDb, 'src\app\Controllers\ResponsavelApiController.php');
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
            $this->message->message($message = array(), 'danger', $parameter1, 5);
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

    // route POST /# www/index.php/fia/ptpa/responsavel/api/filtrar/(:any)
    // route GET /# www/index.php/fia/ptpa/responsavel/api/filtrar/(:any)
    // Informação sobre o controller
    // retorno do controller [JSON]
    public function dbFilter($parameter = NULL)
    {
        // Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $pageGet = $this->request->getGet('page');
        $page = (isset($pageGet) && !empty($pageGet)) ? ($pageGet) : (1);
        $processRequest = (array) $request->getVar();
        $processRequest = array_filter($processRequest);
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        //
        try {
            //
            // return $this->response->setJSON($processRequest, 200);
            // myPrint($processRequest, 'src\app\Controllers\responsavelApiController.php');
            $requestDb = $this->DbResponsavel->dbFilter($processRequest, $page);
            // myPrint($requestDb, 'src\app\Controllers\responsavelApiController.php', true);
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

    # route POST /www/exemple/group/api/excluir/(:any)
    # route GET /www/exemple/group/api/excluir/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbDelete($parameter1 = NULL, $parameter2 = NULL)
    {
        $request = service('request');
        $getMethod = $request->getMethod();
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        // myPrint($parameter1, $parameter2);
        try {
            if (
                $parameter1 !== NULL
                && $parameter2 == 'eliminar'
            ) {
                // myPrint($parameter1, 'src\app\Controllers\UnidadeApiController.php, 271');
                $dbResponse = $this->DbResponsavel->dbDelete($parameter1);
                $this->message->message(['Unidade Eliminada com sucesso.'], 'success', $dbUpdate = array(), 5);
            } elseif (
                $parameter1 !== NULL
                && $parameter2 == 'restaurar'
            ) {
                $dbUpdate = array(
                    'deleted_at' => null
                );
                // myPrint($dbUpdate, 'src\app\Controllers\UnidadeApiController.php', true);
                $dbResponse = $this->DbResponsavel->dbUpdate($parameter1, $dbUpdate);
                $this->message->message(['Unidade Restaurada com sucesso.'], 'success', $dbUpdate = array(), 5);
            } elseif (
                $parameter1 !== NULL
            ) {
                // myPrint($parameter1, 'src\app\Controllers\UnidadeApiController.php, 287');
                $dbUpdate = array(
                    'deleted_at' => date('Y-m-d H:i:s')
                );
                // myPrint($dbUpdate, 'dbDelete/Unidade');
                $dbResponse = $this->DbResponsavel->dbUpdate($parameter1, $dbUpdate);
                $this->message->message(['Unidade Excluída com sucesso.'], 'success', $dbUpdate = array(), 5);
            } else {
                $this->message->message(['Erro ao Excluir a Unidade.'], 'warning', $dbUpdate = array(), 5);
            }
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
            // return redirect()->to('project/endpoint/parameter1/parameter/' . $parameter);
        } else {
            // return $response;
            return redirect()->back();
            // return redirect()->to('fia/ptpa/unidade/endpoint/exibir');
        }
    }

    // route POST /www/exemple/group/api/limpar/(:any)
    // route GET /www/exemple/group/api/limpar/(:any)
    // Informação sobre o controller
    // retorno do controller [JSON]
    public function dbCleaner($parameter = NULL)
    {
        $request = service('request');
        $getMethod = $request->getMethod();
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        try {
            $requestDb = $this->DbResponsavel->dbCleaner($parameter);
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
}
