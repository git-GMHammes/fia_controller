<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\TokenCsrfController;
use App\Controllers\SystemMessageController;
use App\Controllers\GeneroIdentidadeDbController;
// use App\Controllers\SystemUploadDbController;

use Exception;

class GeneroIdentidadeApiController extends ResourceController
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
        $this->DbController = new GeneroIdentidadeDbController();
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

    function verificarPalavraInadequada($entrada)
    {
        // Se a entrada for um número, retorna false imediatamente
        if (is_numeric($entrada)) {
            return false;
        }
        // Converter para minúsculas para comparação não sensível a caso
        $entrada = mb_strtolower(trim($entrada), 'UTF-8');

        // Lista de palavras inadequadas individuais
        $palavrasInadequadas = [
            "vai",
            "se",
            "ovoooo",
            "ovooo",
            "ovoo",
            "ovoo",
            "ovvo",
            "ovvvo",
            "ovo",
            "oovo",
            "ooovo",
            "oooovo",
            "oooo0vo",
            "cú",
            "cu",
            "piroca",
            "puta",
            "pqp",
            "putaquiupariu",
            "pariu",
            "buceta",
            "tomar",
            "tomar",
            "comando",
            "vermelho",
            "governador",
            "senador",
            "juiz",
            "juíz",
            "juís",
            "juis",
            "deputado",
            "vereador",
            "prime",
            "lula",
            "bolsonaro",
            "teta",
            "ladrão",
            "ladrao",
            "golpe",
            "fuder",
            "foder",
            "foda",
            "fora",
            "caio",
            "cassio",
            "arthur",
            "artur",
            "artu",
            "anna",
            "ana",
            "skol",
            "brama",
            "carnaval",
            "palhaçada",
            "alexandre",
            "moraes",
            "barroso",
            "carmem",
            "carmen",
            "dino",
            "palhaço",
            "palhasso",
            "palhaçada",
            "palhasada",
            "palhassada",
            "pokemon",
            "paçoca",
            "brahma",
            "joao",
            "joão",
            "zeca",
            "jose",
            "josé",
            "jozé",
            "joze",
            "walace",
            "walaci",
            "wallace",
            "gostavo",
            "gustavo",
            "gust@vo",
            "gust@vo",
            "fod@",
            "fod4",
            "fuder",
            "trepar",
            "transar",
            "burro",
            "ignorante",
            "chupar",
            "chupa",
            "cadeia",
            "federal",
            "militar",
            "escroto",
            "vagina",
            "anus",
            "bosta",
            "merda",
            "claudio",
            "castro",
            "fia",
            'mdb',
            'pl',
            'pt',
            'psd',
            'pp',
            'republicanos',
            'união',
            'pdt',
            'psb',
            'psdb',
            'podemos',
            'psol',
            'pcdob',
            'cidadania',
            'avante',
            'pv',
            'rede',
            'pmb',
            'dc',
            'pcb',
            'pco',
            'prtb',
            'pmn',
            'agir',
            'novo',
            'up',
            'prd'
            // Adicione mais palavras inadequadas individuais
        ];

        // Lista de expressões inadequadas completas
        $expressoesInadequadas = [
            "vai tomar no cu",
            "vai tomar",
            "vai se",
            "vai se fuder",
            "lula livre",
            // Adicione mais expressões inadequadas
        ];

        // Verificar expressões completas primeiro
        foreach ($expressoesInadequadas as $expressao) {
            if (strpos($entrada, $expressao) !== false) {
                // myPrint('$expressao :: ', $expressao, true);
                return false; // Expressão inadequada encontrada
            }
        }

        // Dividir a entrada por vários separadores possíveis
        $separadores = [' ', '-', ',', '|', '/', '.', '_', ':', ';', '\\', '+', '=', '*'];
        $palavras = [];

        // Substituir todos os separadores por um espaço
        $textoNormalizado = str_replace($separadores, ' ', $entrada);

        // Dividir por espaços e remover elementos vazios
        $palavrasSeparadas = array_filter(explode(' ', $textoNormalizado), 'strlen');

        // Verificar cada palavra individual contra a lista de palavras inadequadas
        foreach ($palavrasSeparadas as $palavra) {
            $palavra = trim($palavra);
            if ($palavra === '')
                continue;

            foreach ($palavrasInadequadas as $palavraInadequada) {
                if ($palavra === $palavraInadequada) {
                    // myPrint('$palavra :: ', $palavra, true);
                    return false; // Palavra inadequada encontrada
                }
            }
        }

        return true;
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
        $pageGet = $this->request->getGet('page');
        $limitGet = $this->request->getGet('limit');
        $limit = (isset($limitGet) && !empty($limitGet)) ? ($limitGet) : (10);
        $page = (isset($pageGet) && !empty($pageGet)) ? ($pageGet) : (1);
        $processRequest = (array) $request->getVar();
        $processRequest['genero'] = isset($processRequest['genero']) ? ucfirst(strtolower($processRequest['genero'])) : ('erro');
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        $id = isset($processRequest['id']) ? ($processRequest['id']) : ($parameter);
        #
        $choice_update = isset($processRequest['id']) ? true : false;
        $token_csrf = isset($processRequest['token_csrf']) ? ($processRequest['token_csrf']) : ('erro');
        #
        $filter = array(
            'genero' => isset($processRequest['genero']) ? ($processRequest['genero']) : ('erro')
        );
        $this->verificarPalavraInadequada($processRequest['genero']);
        $dbRead = $this->DbController->dbFilter($filter, $page, $limit);
        // myPrint('$dbRead :: ', $dbRead);
        try {
            #
            if (
                count($dbRead['dbResponse']) === 0 ||
                $this->verificarPalavraInadequada($processRequest['genero'])
            ) {
                $requestDb = $this->saveRequest($choice_update, $token_csrf, $processRequest);
            } else {
                $requestDb = array(
                    'status' => 'trouble',
                    'message' => 'Erro - requisição que foi bem-formada mas não pôde ser seguida devido a erros semânticos.',
                    'cod_http' => 422,
                    'dbResponse' => $dbRead['dbResponse'],
                );
            }
            // myPrint('$requestDb :: ', $requestDb);
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

    # route POST /www/index.php/fia/ptpa/generoidentidade/api/exibir/(:any)
    # route GET /www/index.php/fia/ptpa/generoidentidade/api/exibir/(:any)
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
        // $processRequest = eagarScagaire($processRequest);
        #
        try {
            #
            $id = isset($processRequest['id']) ? ($processRequest['id']) : ($parameter);
            $requestDb = $this->DbController->dbRead($id);
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

    # route POST /# www/index.php/fia/ptpa/adolescente/api/filtrar/(:any)
    # route GET /# www/index.php/fia/ptpa/adolescente/api/filtrar/(:any)
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
        try {
            #
            // return $this->response->setJSON($processRequest, 200);
            // myPrint($processRequest, 'src\app\Controllers\AdolescenteApiController.php');
            $requestDb = $this->DbController->dbFilter($processRequest, $page);
            // myPrint($requestDb, 'src\app\Controllers\AdolescenteApiController.php', true);
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
        try {
            if (
                $parameter1 !== NULL
                && $parameter2 == 'eliminar'
            ) {
                // myPrint($parameter1, 'src\app\Controllers\GeneroIdentidadeApiController.php, 271', true);
                $dbResponse = $this->DbController->dbDelete($parameter1);
                $this->message->message(['Genero Eliminada com sucesso.'], 'success', $dbUpdate = array(), 5);
            } elseif (
                $parameter1 !== NULL
                && $parameter2 == 'restaurar'
            ) {
                $dbUpdate = array(
                    'deleted_at' => null
                );
                // myPrint($parameter1, 'src\app\Controllers\GeneroIdentidadeApiController.php');
                $dbResponse = $this->DbController->dbUpdate($parameter1, $dbUpdate);
                $this->message->message(['Genero Restaurada com sucesso.'], 'success', $dbUpdate = array(), 5);
            } elseif (
                $parameter1 !== NULL
            ) {
                // myPrint($parameter1, 'src\app\Controllers\GeneroIdentidadeApiController.php, 287');
                $dbUpdate = array(
                    'deleted_at' => date('Y-m-d H:i:s')
                );
                // myPrint($dbUpdate, 'dbDelete/Genero');
                $dbResponse = $this->DbController->dbUpdate($parameter1, $dbUpdate);
                $this->message->message(['Genero Excluída com sucesso.'], 'success', $dbUpdate = array(), 5);
            } else {
                $this->message->message(['Erro ao Excluir a Genero.'], 'warning', $dbUpdate = array(), 5);
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
            // return redirect()->to('project/endpoint/parameter/parameter/' . $parameter);
        } else {
            // return $response;
            return redirect()->back();
            // return redirect()->to('fia/ptpa/genero/endpoint/exibir');
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
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        try {
            #
            $id = isset($processRequest['id']) ? ($processRequest['id']) : ($parameter);
            $requestDb = $this->DbController->dbCleaner($id, $page);
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
        }
    }
}
