<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\TokenCsrfController;
use App\Controllers\SystemMessageController;
use App\Controllers\CadastroDbController;
// use App\Controllers\SystemUploadDbController;

use Exception;

class CadastroApiController extends ResourceController
{
    use ResponseTrait;
    private $ModelResponse;
    private $dbFields;
    private $uri;
    private $tokenCsrf;
    private $DbController;
    private $message;

    public function __construct()
    {
        $this->DbController = new CadastroDbController();
        $this->tokenCsrf = new TokenCsrfController();
        $this->message = new SystemMessageController();
        // $this->DbController = new SystemUploadDbController();
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
    }
    #
    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function index()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }
    
    # route POST /www/exemple/group/api/criar/(:any)
    # route GET /www/exemple/group/api/criar/(:any)
    # route POST /exemple/group/api/atualizar/(:any)
    # route GET /exemple/group/api/atualizar/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function create_update($parameter = NULL)
    {
        # Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $getVar_page = $request->getVar('page');
        $processRequest = (array)$request->getVar();
        // $uploadedFiles = $request->getFiles();
        $token_csrf = (isset($processRequest['token_csrf']) ? $processRequest['token_csrf'] : NULL);
        // myPrint($processRequest, 'src\app\Controllers\CadastroApiController.php');
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        $choice_update = (isset($processRequest['id']) && !empty($processRequest['id'])) ? (true) : (false);
        #
        // myPrint($processRequest, 'src\app\Controllers\CadastroApiController.php', true);
        #
        try {
            if ($choice_update === true) {
                if ($this->tokenCsrf->valid_token_csrf($token_csrf)) {
                    $id = (isset($processRequest['id'])) ? ($processRequest['id']) : (array());
                    $dbResponse = $this->DbController->dbUpdate($id, $processRequest);
                    // myPrint($dbResponse, 'src\app\Controllers\CadastroApiController.php');
                    if (isset($dbResponse["affectedRows"]) && $dbResponse["affectedRows"] > 0) {
                        $processRequestSuccess = true;
                    }
                }
            } elseif ($choice_update === false) {
                if ($this->tokenCsrf->valid_token_csrf($token_csrf)) {
                    $dbResponse = $this->DbController->dbCreate($processRequest);
                    if (isset($dbResponse["affectedRows"]) && $dbResponse["affectedRows"] > 0) {
                        $processRequestSuccess = true;
                    }
                }
            } else {
                $this->message->message(['ERRO: Dados enviados inválidos'], 'danger');
            };
            // myPrint($dbResponse, 'src\app\Controllers\CadastroApiController.php');
            $status = (!isset($processRequestSuccess) || $processRequestSuccess !== true) ? ('trouble') : ('success');
            $message = (!isset($processRequestSuccess) || $processRequestSuccess !== true) ? ('Erro - requisição que foi bem-formada mas não pôde ser seguida devido a erros semânticos.') : ('API loading data (dados para carregamento da API)');
            $cod_http = (!isset($processRequestSuccess) || $processRequestSuccess !== true) ? (422) : (201);
            // myPrint($status, $message, true);
            // myPrint($cod_http, 'Teste');
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
            // return redirect()->to('project/endpoint/parameter/parameter/' . $parameter);
        } else {
            return redirect()->to('fia/ptpa/profissional/endpoint/exibir');
            // return redirect()->back();
        }
    }
    
    # route POST /www/exemple/group/api/teste/(:any)
    # route GET /www/exemple/group/api/teste/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbRead($parameter1 = NULL)
    {
        # Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $pageGet = $this->request->getGet('page');
        $page = (isset($pageGet) && !empty($pageGet)) ? ($pageGet) : (1);
        $processRequest = (array)$request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        // $processRequest = eagarScagaire($processRequest);
        #
        try {
            #
            $id = isset($processRequest['id']) ? ($processRequest['id']) : ($parameter1);
            $requestDb = $this->DbController->dbRead($id, $page);
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

    # route POST /# www/index.php/fia/ptpa/profissional/api/filtrar/(:any)
    # route GET /# www/index.php/fia/ptpa/profissional/api/filtrar/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbFilter($parameter = NULL)
    {
        # Parâmentros para receber um POST
        $request = service('request');
        // myPrint($request, 'src\app\Controllers\CadastroApiController.php');
        $getMethod = $request->getMethod();
        $pageGet = $this->request->getGet('page');
        $page = (isset($pageGet) && !empty($pageGet)) ? ($pageGet) : (1);
        $processRequest = (array)$request->getVar();
        // $processRequest = array_filter($processRequest);
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        #
        try {
            #
            // return $this->response->setJSON($processRequest, 200);
            // myPrint($processRequest, 'src\app\Controllers\CadastroApiController.php');
            $requestDb = $this->DbController->dbFilter($processRequest, $page);
            // myPrint($requestDb, 'src\app\Controllers\CadastroApiController.php', true);
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

    private function checkMedicalRecords($parameter1 = 'cadastros', $parameter2 = NULL)
    {
        $requestDb = array();
        if ($parameter1 == 'cadastros') {
            $requestDb = $this->dbCleaner($parameter2);
            $requestDb = $requestDb->getBody();
            $requestDb = json_decode($requestDb, true);
            // myPrint($requestDb['result']['dbResponse'], '');
        }
        if (
            isset($requestDb['result']['dbResponse'][0]['UnidadeId'])
            && $requestDb['result']['dbResponse'][0]['UnidadeId'] !== null
            || isset($requestDb['result']['dbResponse'][0]['ProntuarioId'])
            && $requestDb['result']['dbResponse'][0]['ProntuarioId'] !== null
        ) {
            return false;
        } else {
            return true;
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
        $processRequest = (array)$request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        // myPrint($parameter1, $parameter2);
        try {
            $this->checkMedicalRecords('cadastros', $parameter1);
            if (
                $parameter1 !== NULL
                && $parameter2 == 'eliminar'
                && $this->checkMedicalRecords('cadastros', $parameter1)
            ) {
                // myPrint('Linha 294', 'src\app\Controllers\CadastroApiController.php');
                $dbResponse = $this->DbController->dbDelete($parameter1);
                $this->message->message(['Cadastro Eliminado com sucesso.'], 'success', $dbUpdate = array(), 5);
            } elseif (
                $parameter1 !== NULL
                && $parameter2 == 'restaurar'
            ) {
                // myPrint('Linha 301', 'src\app\Controllers\CadastroApiController.php');
                $dbUpdate = array(
                    'deleted_at' => null
                );
                $dbResponse = $this->DbController->dbUpdate($parameter1, $dbUpdate);
                $this->message->message(['Cadastro Restaurado com sucesso.'], 'success', $dbUpdate = array(), 5);
            } elseif (
                $parameter1 !== NULL
                && $this->checkMedicalRecords('cadastros', $parameter1)
            ) {
                $dbUpdate = array(
                    'deleted_at' => date('Y-m-d H:i:s')
                );
                $dbResponse = $this->DbController->dbUpdate($parameter1, $dbUpdate);
                // myPrint($dbUpdate, 'src\app\Controllers\CadastroApiController.php');
                $this->message->message(['Cadastro Excluído com sucesso.'], 'success', $dbUpdate = array(), 5);
            } else {
                $this->message->message(['Erro ao Excluir o Cadastro.'], 'warning', $dbUpdate = array(), 5);
            }
            // myPrint('Linha 321', 'src\app\Controllers\CadastroApiController.php');
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
            // return redirect()->to('fia/ptpa/profissional/endpoint/deletar');
            return redirect()->back();
            
        }
    }

    # route POST /www/exemple/group/api/limpar/(:any)
    # route GET /www/exemple/group/api/limpar/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbCleaner($parameter = NULL)
    {
        $request = service('request');
        $getMethod = $request->getMethod();
        $pageGet = $this->request->getGet('page');
        $page = (isset($pageGet) && !empty($pageGet)) ? ($pageGet) : (1);
        $processRequest = (array)$request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        try {
            $requestDb = $this->DbController->dbCleaner($parameter);
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
            $this->message->message($message = array(), 'danger', $parameter, 5);
            $response = $this->response->setJSON($apiRespond, 500);
        }
        if ($json == 1) {
            return $response;
            // return redirect()->back();
            // return redirect()->to('project/endpoint/parameter/parameter/' . $parameter);
        } else {
            return $response;
            // return redirect()->to('fia/ptpa/profissional/endpoint/limpar');
        }
    }
}
