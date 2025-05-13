<?php
#
namespace App\Controllers\Teste;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
# 
use App\Controllers\Teste\DbController; // Ensure this path is correct and the class exists
use App\Controllers\TokenCsrfController;
use App\Controllers\SystemMessageController;
# 
use Exception;

class ApiController extends ResourceController
{
    use ResponseTrait;
    private $ModelResponse;
    private $uri;
    private $tokenCsrf;
    private $DbController;
    private $message;

    public function __construct()
    {
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
        $this->DbController = new DbController();
        $this->tokenCsrf = new TokenCsrfController();
        $this->message = new SystemMessageController();
        #
    }

    # route POST /www/index.php/index.php/project/method
    # route GET /www/index.php/index.php/project/method
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function index($parameter = NULL)
    {
        $request = service('request');
        $apiRespond['getMethod'] = $request->getMethod();
        $apiRespond['method'] = __METHOD__;
        $apiRespond['function'] = __FUNCTION__;
        $apiRespond['message'] = '403 Forbidden - Directory access is forbidden.';
        return $this->response->setStatusCode(403)->setJSON($apiRespond);
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

    /**
     * Processa e salva o arquivo de upload
     * 
     * @param array $uploadedFiles Array com os arquivos enviados
     * @return array Informações sobre o resultado do upload
     */
    private function processUpload($uploadedFiles, $protocolo_relatorio)
    {
        // Verifica se existe algum arquivo para upload
        if (empty($uploadedFiles)) {
            return [
                'status' => false,
                'message' => 'Nenhum arquivo enviado'
            ];
        }

        // Obtém o arquivo do campo 'upload'
        $arquivo = $uploadedFiles['upload'];

        // Verifica se o arquivo foi enviado corretamente
        if (!$arquivo->isValid()) {
            return [
                'status' => false,
                'message' => 'Arquivo inválido: ' . $arquivo->getErrorString()
            ];
        }

        // Cria o diretório de destino se não existir
        $uploadPath = WRITEPATH . 'uploads/testes/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Obtém a extensão original do arquivo
        $extensao = pathinfo($arquivo->getName(), PATHINFO_EXTENSION);

        // Gera o nome do arquivo no formato: AAAAMMDD_HHMMSS.extensao
        $novoNome = $protocolo_relatorio . '_' . date('Ymd_His') . '.' . $extensao;

        // Move o arquivo para o destino
        if ($arquivo->move($uploadPath, $novoNome)) {
            return [
                'status' => true,
                'message' => 'Arquivo salvo com sucesso',
                'arquivo' => [
                    'nome_original' => $arquivo->getName(),
                    'caminho' => $uploadPath,
                    'nome' => $novoNome,
                    'tamanho' => $arquivo->getSize(),
                    'tipo' => $arquivo->getClientMimeType()
                ]
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Erro ao mover o arquivo: ' . $arquivo->getErrorString()
            ];
        }
    }

    # route POST /www/index.php/www/teste/group/api/salvar/(:any)
    # route GET /www/index.php/www/teste/group/api/salvar/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function create_update($parameter = NULL)
    {
        # Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $processRequest = (array) $request->getVar();
        $uploadedFiles = $request->getFiles();
        $protocolo_relatorio = isset($processRequest['protocolo_relatorio']) ? ($processRequest['protocolo_relatorio']) : ('erro');
        $nome_arquivo = isset($processRequest['nome_arquivo']) ? ($processRequest['nome_arquivo']) : (null);
        $processRequest['path'] = isset($processRequest['path']) ? ($processRequest['path']) : ('testes/');
        // myPrint('$processRequest :: ', $processRequest);
        #
        if ($getMethod == 'GET') {
            $request = service('request');
            $apiRespond['getMethod'] = $request->getMethod();
            $apiRespond['method'] = __METHOD__;
            $apiRespond['function'] = __FUNCTION__;
            $apiRespond['message'] = '403 Forbidden - Directory access is forbidden.';
            return $this->response->setStatusCode(403)->setJSON($apiRespond);
        }
        #
        $token_csrf = (isset($processRequest['token_csrf']) ? $processRequest['token_csrf'] : 'erro');
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        $choice_update = (isset($processRequest['id']) && !empty($processRequest['id'])) ? (true) : (false);

        try {
            if ($nome_arquivo == null) {
                # Processa o upload do arquivo
                $uploadResult = $this->processUpload($uploadedFiles, $protocolo_relatorio);

                # Adiciona as informações do upload ao processRequest
                $processRequest['path'] = isset($uploadResult['arquivo']['caminho']) ? ($uploadResult['arquivo']['caminho']) : (null);
                $processRequest['nome_arquivo'] = isset($uploadResult['arquivo']['nome']) ? ($uploadResult['arquivo']['nome']) : (null);
                $processRequest['tamanho_arquivo'] = isset($uploadResult['arquivo']['tamanho']) ? ($uploadResult['arquivo']['tamanho']) : (null);
                $processRequest['tipo_arquivo'] = isset($uploadResult['arquivo']['tipo']) ? ($uploadResult['arquivo']['tipo']) : (null);
                $processRequest['ip_relatorio'] = isset($_SERVER['SERVER_ADDR ']) ? ($_SERVER['SERVER_ADDR ']) : (null);
                # Se o upload falhou, retorna o erro
                if (!$uploadResult['status']) {
                    $apiRespond = $this->setApiRespond('error', $getMethod, $processRequest, $uploadResult['message']);
                    return $this->response->setStatusCode(400)->setJSON($apiRespond);
                }
            }
            #
            $dbSave = $this->saveRequest($choice_update, $token_csrf, $processRequest);
            $apiRespond = $this->setApiRespond($dbSave['status'], $getMethod, $dbSave['dbResponse']);
            $response = $this->response->setStatusCode(201)->setJSON($apiRespond);
        } catch (\Exception $e) {
            $apiRespond = $this->setApiRespond('error', $getMethod, $processRequest, $e->getMessage());
            $response = $this->response->setStatusCode(500)->setJSON($apiRespond);
        }
        #
        if ($json) {
            return $response;
            // return redirect()->to('project/endpoint/parameter/parameter/' . $parameter);
        } else {
            // return redirect()->back();
            return $response;
        }
    }

    # route POST /www/index.php/teste/group/api/exibir/(:any)
    # route GET /www/index.php/teste/group/api/exibir/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbRead($parameter = NULL)
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
        // myPrint($getMethod, 'C:\Users\Habilidade.Com\AppData\Roaming\Code\User\snippets\php.json');
        try {
            #
            $requestDb = $this->DbController->dbRead($id, $page, $limit);
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


    # route POST /www/index.php/teste/group/api/filtrar/(:any)
    # route GET /www/index.php/teste/group/api/filtrar/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbFilter($parameter = NULL)
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
        // myPrint($getMethod, 'C:\Users\Habilidade.Com\AppData\Roaming\Code\User\snippets\php.json');
        try {
            #
            $requestDb = $this->DbController->dbFilter($processRequest, $page, $limit);
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

    # route POST /www/index.php/teste/group/api/deletar/(:any)
    # route GET /www/index.php/teste/group/api/deletar/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbDelete($parameter1 = NULL, $parameter2 = NULL)
    {
        $request = service('request');
        $getMethod = $request->getMethod();
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;

        try {
            // $this->checkMedicalRecords('cadastros', $parameter1);
            if (
                $parameter1 !== NULL
                && $parameter2 == 'eliminar'
                // && $this->checkMedicalRecords('cadastros', $parameter1)
            ) {
                // myPrint($parameter1, 'C:\Users\Habilidade.Com\AppData\Roaming\Code\User\snippets\php.json', true);
                #
                $dbResponse = $this->DbController->dbDelete($parameter1);
                #
                $this->message->message(['Registro Eliminado com sucesso com Sucesso.'], 'success', $dbUpdate = array(), 5);
            } elseif (
                $parameter1 !== NULL
                && $parameter2 == 'restaurar'
            ) {
                #
                $dbUpdate = array(
                    'deleted_at' => null
                );
                #
                $dbResponse = $this->DbController->dbUpdate($parameter1, $dbUpdate);
                $this->message->message(['Registro Restaurado com sucesso com Sucesso.'], 'success', $dbUpdate = array(), 5);
            } elseif (
                $parameter1 !== NULL
                // && $this->checkMedicalRecords('cadastros', $parameter1)
            ) {
                $dbUpdate = array(
                    'deleted_at' => date('Y-m-d H:i:s')
                );
                #
                $dbResponse = $this->DbController->dbUpdate($parameter1, $dbUpdate);
                #
                $this->message->message(['Registro Excluído com Sucesso.'], 'success', $dbUpdate = array(), 5);
            } else {
                #
                $this->message->message(['Erro ao Excluir o Registro.'], 'warning', $dbUpdate = array(), 5);
                #
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
            $response = $this->response->setStatusCode(201)->setJSON($apiRespond);
        } catch (\Exception $e) {
            $apiRespond = array(
                'message' => array('danger' => $e->getMessage()),
                'page_title' => 'Application title',
                'getURI' => $this->uri->getSegments(),
                'environment' => ENVIRONMENT_CHOICE,
            );
            // $this->returnFunction(array($e->getMessage()), 'danger',);
            $response = $this->response->setStatusCode(500)->setJSON($apiRespond);
        }
        if ($json == 1) {
            return $response;
            // return redirect()->back();
            // return redirect()->to('project/endpoint/parameter1/parameter/' . $parameter);
        } else {
            return $response;
            // return redirect()->back();
        }
    }

    # route POST /www/index.php/index.php/circuito/objeto/api/limpar/(:any)
    # route GET /www/index.php/index.php/circuito/objeto/api/limpar/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbClear($parameter = NULL)
    {
        $request = service('request');
        $apiRespond['getMethod'] = $request->getMethod();
        $apiRespond['method'] = __METHOD__;
        $apiRespond['function'] = __FUNCTION__;
        $apiRespond['message'] = '403 Forbidden - Directory access is forbidden.';
        return $this->response->setStatusCode(403)->setJSON($apiRespond);
    }

    # route POST /# www/index.php/teste/group/api/select/(:any)
    # route GET /# www/index.php/teste/group/api/select/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]

}
#
?>