<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\TokenCsrfController;
use App\Controllers\SystemMessageController;
use App\Controllers\EscolaridadeDbController;
// use App\Controllers\SystemUploadDbController;

use Exception;

class EscolaridadeApiController extends ResourceController
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
        $this->DbController = new EscolaridadeDbController();
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

    # route POST www/index.php/fia/ptpa/unidade/api/criar/(:any)
    # route GET www/index.php/fia/ptpa/unidade/api/criar/(:any)
    # route POST www/index.php/fia/ptpa/unidade/api/atualizar/(:any)
    # route GET www/index.php/fia/ptpa/unidade/api/atualizar/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function create_update($parameter = NULL)
    {
        $dbResponse = array();
        # Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $getVar_page = $request->getVar('page');
        $processRequest = (array) $request->getVar();
        // $uploadedFiles = $request->getFiles();
        $processRequest['assinatura'] = $this->assinatura($processRequest);
        // 
        // myPrint('$processRequest :: ', $processRequest, true);
        // 
        $token_csrf = (isset($processRequest['token_csrf']) ? $processRequest['token_csrf'] : NULL);
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        $choice_update = (isset($processRequest['id']) && !empty($processRequest['id'])) ? (true) : (false);
        // myPrint($choice_update, 'Linha 182');
        #
        try {
            if ($token_csrf == NULL) {
                return $this->response->setJSON(['status' => 'error', 'result' => 'Token CSRF inválido.'], 422);
            }
            if ($choice_update === false) { // Novo registro
                if ($this->tokenCsrf->valid_token_csrf($token_csrf)) {
                    $processRequest['created_at'] = date('Y-m-d H:i:s'); // Timestamp para criação
                    $processRequest['updated_at'] = date('Y-m-d H:i:s'); // Inicializa também o updated_at
                    $processRequest['unidades_data_cadastramento'] = date('Y-m-d');

                    $dbResponse = $this->DbController->dbCreate($processRequest);
                    // myPrint($dbResponse, 'src\app\Controllers\AdolescenteApiController.php', true);
                    if (isset($dbResponse["affectedRows"]) && $dbResponse["affectedRows"] > 0) {
                        $processRequestSuccess = true;
                    }
                }
            } elseif ($choice_update === true) { // Atualização
                if ($this->tokenCsrf->valid_token_csrf($token_csrf)) {
                    $processRequest['updated_at'] = date('Y-m-d H:i:s'); // Atualiza o timestamp
                    $id = (isset($processRequest['id'])) ? ($processRequest['id']) : (array());
                    $dbResponse = $this->DbController->dbUpdate($id, $processRequest);
                    // myPrint($dbResponse, 'src\app\Controllers\AdolescenteApiController.php', true);
                    if (isset($dbResponse["affectedRows"]) && $dbResponse["affectedRows"] > 0) {
                        $processRequestSuccess = true;
                    }
                }
            } else {
                $this->message->message(['ERRO: Dados enviados inválidos'], 'danger');
            }
            // exit('src\app\Controllers\UnidadeApiController.php');
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
            // return $response;
            return redirect()->to('index.php/fia/ptpa/unidade/endpoint/exibir');
        }
    }

    # route POST /www/exemple/group/api/teste/(:any)
    # route GET /www/exemple/group/api/teste/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbRead($parameter = NULL)
    {
        # Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $pageGet = $this->request->getGet('page');
        $page = (isset($pageGet) && !empty($pageGet)) ? ($pageGet) : (1);
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        #
        try {
            #
            $id = isset($processRequest['id']) ? ($processRequest['id']) : ($parameter);
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

    # route POST /www/index.php/fia/ptpa/unidade/api/filtrarassinatura/(:any)
    # route GET /www/index.php/fia/ptpa/unidade/api/filtrarassinatura/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbFilterSignature(array $parameter = [])
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
        // myPrint($processRequest, 'src\app\Controllers\UnidadeApiController.php');
        if (
            isset($processRequest['unidades_nome']) &&
            isset($processRequest['unidades_endereco']) &&
            isset($processRequest['municipio_id'])
        ) {
            $signatureRequest['assinatura'] = $this->assinatura($processRequest);
            // $response = $this->response->setStatusCode(201)->setJSON($signatureRequest);
            // return $response;
            // myPrint($signatureRequest, 'src\app\Controllers\UnidadeApiController.php');
            $id = isset($processRequest['id']) ? ($processRequest['id']) : ($parameter);
            $requestDb = $this->DbController->dbFilter($signatureRequest);
            // myPrint('$requestDb', $requestDb);
            #
        }
        try {
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
                'result' => isset($requestDb) ? ($requestDb) : array('dbResponse' => [], 'linksArray' => []),
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
        if ($json == 1) {
            return $response;
            // return redirect()->back();
            // return redirect()->to('project/endpoint/parameter/parameter/' . $parameter);
        } else {
            return $response;
        }
    }

    # route POST /# www/index.php/fia/ptpa/unidade/api/filtrar/(:any)
    # route GET /# www/index.php/fia/ptpa/unidade/api/filtrar/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbFilter($parameter = NULL)
    {
        # Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $pageGet = $this->request->getGet('page');
        $limitGet = $this->request->getGet('limit');
        $page = (isset($pageGet) && !empty($pageGet)) ? ($pageGet) : (1);
        $limit = (isset($limitGet) && !empty($limitGet)) ? ($limitGet) : (10);
        $processRequest = (array) $request->getVar();
        $processRequest = array_filter($processRequest);
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        #
        try {
            #
            // return $this->response->setJSON($processRequest, 200);
            $requestDb = $this->DbController->dbFilter($processRequest, $page, $limit);
            // myPrint($requestDb, 'src\app\Controllers\UnidadeApiController.php');
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
                $dbResponse = $this->DbController->dbDelete($parameter1);
                $this->message->message(['Unidade Eliminada com sucesso.'], 'success', $dbUpdate = array(), 5);
            } elseif (
                $parameter1 !== NULL
                && $parameter2 == 'restaurar'
            ) {
                $dbUpdate = array(
                    'deleted_at' => null
                );
                // myPrint($dbUpdate, 'src\app\Controllers\UnidadeApiController.php', true);
                $dbResponse = $this->DbController->dbUpdate($parameter1, $dbUpdate);
                $this->message->message(['Unidade Restaurada com sucesso.'], 'success', $dbUpdate = array(), 5);
            } elseif (
                $parameter1 !== NULL
            ) {
                // myPrint($parameter1, 'src\app\Controllers\UnidadeApiController.php, 287');
                $dbUpdate = array(
                    'deleted_at' => date('Y-m-d H:i:s')
                );
                // myPrint($dbUpdate, 'dbDelete/Unidade');
                $dbResponse = $this->DbController->dbUpdate($parameter1, $dbUpdate);
                $this->message->message([''], 'success', $dbUpdate = array(), 5);
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
            // return redirect()->back();
            return redirect()->to('index.php/fia/ptpa/unidade/endpoint/exibir');
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
        $processRequest = (array) $request->getVar();
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

    private function assinatura(array $parameter)
    {
        if (
            isset($parameter['unidades_nome']) &&
            isset($parameter['unidades_endereco']) &&
            isset($parameter['municipio_id'])
        ) {
            $parameter01 = [
                $parameter['unidades_nome'],
                $parameter['unidades_endereco'],
                $parameter['municipio_id'],
            ];
            $parameter02 = implode($parameter01);
            $parameter03 = myChar($parameter02);
            $parameter04 = strtoupper(md5($parameter03));
            return $parameter04;
        }
    }
}
