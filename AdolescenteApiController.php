<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\TokenCsrfController;
use App\Controllers\SystemMessageController;
use App\Controllers\AdolescenteDbController;
use App\Controllers\ResponsavelDbController;
// use App\Controllers\SystemUploadDbController;

use Exception;

class AdolescenteApiController extends ResourceController
{
    use ResponseTrait;
    private $ModelResponse;
    private $dbFields;
    private $uri;
    private $tokenCsrf;
    private $DbResponsavel;
    private $DbController;
    private $message;

    public function __construct()
    {
        $this->DbResponsavel = new ResponsavelDbController();
        $this->DbController = new AdolescenteDbController();
        $this->message = new SystemMessageController();
        $this->tokenCsrf = new TokenCsrfController();
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

    private function verificaResponsavel($parameter = array())
    {
        $dbData['id'] = isset($parameter['ResponsavelID']) ? ($parameter['ResponsavelID']) : (null);
        $dbData['acesso_id'] = '2';
        $dbData['perfil_id'] = '2';
        $dbData['Nome'] = isset($parameter['Responsavel_Nome']) ? ($parameter['Responsavel_Nome']) : (null);
        $dbData['Email'] = isset($parameter['Responsavel_Email']) ? ($parameter['Responsavel_Email']) : (null);
        $dbData['TelefoneFixo'] = isset($parameter['Responsavel_TelefoneFixo']) ? ($parameter['Responsavel_TelefoneFixo']) : (null);
        $dbData['TelefoneMovel'] = isset($parameter['Responsavel_TelefoneMovel']) ? ($parameter['Responsavel_TelefoneMovel']) : (null);
        $dbData['TelefoneRecado'] = isset($parameter['Responsavel_TelefoneRecado']) ? ($parameter['Responsavel_TelefoneRecado']) : (null);
        $dbData['Endereco'] = isset($parameter['Responsavel_Endereco']) ? ($parameter['Responsavel_Endereco']) : (null);
        $dbData['CPF'] = isset($parameter['Responsavel_CPF']) ? ($this->formatarCPF($parameter['Responsavel_CPF'])) : (null);
        # 
        $dbParameter = array(
            'CPF' => $dbData['CPF'],
        );
        // myPrint('$dbParameter :: ', $dbParameter, true);

        $dbValidation = $this->DbResponsavel->dbFilter($dbParameter);
        // myPrint("dbValidation :: ", $dbValidation);
        if (isset($dbValidation['dbResponse'][0])) {
            // myPrint($dbValidation['dbResponse'][0], 'src\app\Controllers\AdolescenteApiController.php');
            $responsavel_id = $dbValidation['dbResponse'][0]['id'];
            $this->DbResponsavel->dbUpdate($responsavel_id, $dbData);
        } else {
            $dbCreate = $this->DbResponsavel->dbCreate($dbData);
            $responsavel_id = isset($dbCreate['insertID']) ? ($dbCreate['insertID']) : (null);
            // myPrint($dbCreate, 'src\app\Controllers\AdolescenteApiController.php');
        }
        return ($responsavel_id);
    }

    private function formatarCPF($cpf)
    {
        // Remove todos os caracteres que não sejam números
        $cpfNumerico = preg_replace('/[^0-9]/', '', $cpf);

        // Verifica se o CPF já possui formatação (pontos ou hífen)
        if (strpos($cpf, '.') !== false || strpos($cpf, '-') !== false) {
            // Se já possuir formatação, retorna o CPF original
            return $cpf;
        }

        // Verifica se o CPF possui 11 dígitos
        if (strlen($cpfNumerico) == 11) {
            // Aplica a máscara XXX.XXX.XXX-XX
            return substr($cpfNumerico, 0, 3) . '.' .
                substr($cpfNumerico, 3, 3) . '.' .
                substr($cpfNumerico, 6, 3) . '-' .
                substr($cpfNumerico, 9, 2);
        }

        // Se não tiver 11 dígitos, retorna o valor original
        return $cpf;
    }

    # route POST /www/index.php/fia/ptpa/adolescente/api/cadastrar/(:any)
    # route GET /www/index.php/fia/ptpa/adolescente/api/cadastrar/(:any)
    # route POST /www/index.php/fia/ptpa/adolescente/api/atualizar/(:any)
    # route GET /www/index.php/fia/ptpa/adolescente/api/atualizar/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function create_update($parameter = NULL)
    {
        # Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $getVar_page = $request->getVar('page');
        $processRequest = (array) $request->getVar();
        $processRequest['CPF'] = $this->formatarCPF($processRequest['CPF']);
        $processRequest['Responsavel_CPF'] = $this->formatarCPF($processRequest['Responsavel_CPF']);
        #
        $assina['CPF'] = isset($processRequest['CPF']) ? ($processRequest['CPF']) : ('');
        $assina['Certidao'] = isset($processRequest['Certidao']) ? ($processRequest['Certidao']) : ('');
        $processRequest['assinatura'] = $this->assinatura($assina);
        #
        // myPrint('$processRequest :: ', $processRequest);
        #
        $id_responsavel = $this->verificaResponsavel($processRequest);
        #
        $processRequest['ResponsavelID'] = $id_responsavel;
        #
        $token_csrf = (isset($processRequest['token_csrf']) ? $processRequest['token_csrf'] : NULL);
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        $choice_update = (isset($processRequest['id']) && !empty($processRequest['id'])) ? (true) : (false);
        #
        if ($choice_update === false) { // Novo registro
            if ($this->tokenCsrf->valid_token_csrf($token_csrf)) {
                $processRequest['created_at'] = date('Y-m-d H:i:s'); // Timestamp para criação
                $processRequest['updated_at'] = date('Y-m-d H:i:s'); // Inicializa também o updated_at
                $processRequest['DataCadastramento'] = date('Y-m-d');

                $dbResponse = $this->DbController->dbCreate($processRequest);
                //myPrint($dbResponse, 'src\app\Controllers\AdolescenteApiController.php', true);
                if (isset($dbResponse["affectedRows"]) && $dbResponse["affectedRows"] > 0) {
                    $processRequestSuccess = true;
                }
            }
        } elseif ($choice_update === true) { // Atualização
            if ($this->tokenCsrf->valid_token_csrf($token_csrf)) {
                $processRequest['updated_at'] = date('Y-m-d H:i:s'); // Atualiza o timestamp
                $id = (isset($processRequest['id'])) ? ($processRequest['id']) : (array());
                $dbResponse = $this->DbController->dbUpdate($id, $processRequest);
                //myPrint($dbResponse, 'src\app\Controllers\AdolescenteApiController.php', true);
                if (isset($dbResponse["affectedRows"]) && $dbResponse["affectedRows"] > 0) {
                    $processRequestSuccess = true;
                }
            }
        } else {
            $this->message->message(['ERRO: Dados enviados inválidos'], 'danger');
        }

        if (session()->get('message')) {
            $apiSession = session()->get('message');
            //myPrint($apiSession, '');
        }

        $status = (!isset($processRequestSuccess) || $processRequestSuccess !== true) ? ('trouble') : ('success');
        $message = (!isset($processRequestSuccess) || $processRequestSuccess !== true) ? ('Erro - requisição que foi bem-formada mas não pôde ser seguida devido a erros semânticos.') : ('API loading data (dados para carregamento da API)');
        $cod_http = (!isset($processRequestSuccess) || $processRequestSuccess !== true) ? (422) : (201);
        // myPrint($status, '', true);
        // myPrint($message, '', true);
        // myPrint($cod_http, '');

        try {
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
            // return redirect()->back();
            return redirect()->to('index.php/fia/ptpa/adolescente/endpoint/exibir');
        }
    }

    # route POST /www/index.php/fia/ptpa/adolescente/api/exibir/(:any))
    # route GET /www/index.php/fia/ptpa/adolescente/api/exibir/(:any))
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbRead($parameter = NULL)
    {
        # Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $pageGet = $this->request->getGet('page');
        $page = (isset($pageGet) && !empty($pageGet)) ? ($pageGet) : (1);
        $limitGet = $this->request->getGet('limit');
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        #
        // myPrint($getMethod, 'src\app\Controllers\AdolescenteApiController.php');
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
        $limitGet = $this->request->getGet('limit');
        $limit = (isset($limitGet) && !empty($limitGet)) ? ($limitGet) : (10);
        $processRequest = (array) $request->getVar();
        $processRequest = array_filter($processRequest);
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        // myPrint($processRequest, 'src\app\Controllers\AdolescenteApiController.php');
        #
        try {
            #
            // return $this->response->setJSON($processRequest, 200);
            // myPrint($processRequest, 'src\app\Controllers\AdolescenteApiController.php');
            $requestDb = $this->DbController->dbFilter($processRequest, $page, $limit);
            // myPrint($requestDb['dbResponse'], 'src\app\Controllers\AdolescenteApiController.php');
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
            // myPrint($requestDb, '');
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

    # route POST /www/index.php/fia/ptpa/adolescente/api/deletar/(:any)
    # route GET /www/index.php/fia/ptpa/adolescente/api/deletar/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbDelete($parameter1 = NULL, $parameter2 = NULL)
    {
        $request = service('request');
        $getMethod = $request->getMethod();
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;

        try {
            $this->checkMedicalRecords('cadastros', $parameter1);
            if (
                $parameter1 !== NULL
                && $parameter2 == 'eliminar'
                && $this->checkMedicalRecords('cadastros', $parameter1)
            ) {
                // myPrint($parameter1, 'src\app\Controllers\AdolescenteApiController.php, 302', true);
                #
                $dbResponse = $this->DbController->dbDelete($parameter1);
                #
                $this->message->message(['Adolescente Eliminado com sucesso com Sucesso.'], 'success', $dbUpdate = array(), 5);
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
                $this->message->message(['Adolescente Restaurado com sucesso com Sucesso.'], 'success', $dbUpdate = array(), 5);
            } elseif (
                $parameter1 !== NULL
                && $this->checkMedicalRecords('cadastros', $parameter1)
            ) {
                $dbUpdate = array(
                    'deleted_at' => date('Y-m-d H:i:s')
                );
                #
                $dbResponse = $this->DbController->dbUpdate($parameter1, $dbUpdate);
                #
                $this->message->message(['Adolescente Excluído com Sucesso.'], 'success', $dbUpdate = array(), 5);
            } else {
                #
                $this->message->message(['Erro ao Excluir o Adolescente.'], 'warning', $dbUpdate = array(), 5);
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
        }
    }

    # route POST /www/index.php/fia/ptpa/adolescente/api/limpar/(:any)
    # route GET /www/index.php/fia/ptpa/adolescente/api/limpar/(:any)
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbCleaner($parameter = NULL)
    {
        # Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $pageGet = $this->request->getGet('page');
        $page = (isset($pageGet) && !empty($pageGet)) ? ($pageGet) : (1);
        $processRequest = (array) $request->getVar();
        $json = isset($processRequest['json']) && $processRequest['json'] == 1 ? 1 : 0;
        #
        // myPrint($getMethod, 'src\app\Controllers\AdolescenteApiController.php');
        try {
            #
            $id = isset($processRequest['id']) ? ($processRequest['id']) : ($parameter);
            $requestDb = $this->DbController->dbCleaner($id, $page);
            // myPrint($requestDb, '');
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

    protected function stringToArray($email)
    {
        if (is_null($email)) {
            return [];
        }

        if (!is_array($email)) {
            $email = (string) $email; // Garantir que $email seja uma string
            return (strpos($email, ',') !== false) ? preg_split('/[\s,]/', $email, -1, PREG_SPLIT_NO_EMPTY) : (array) trim($email);
        }

        return $email;
    }

    public function sendConfirm()
    {

        # Parâmentros para receber um POST
        $request = service('request');
        $getMethod = $request->getMethod();
        $getVar_page = $request->getVar('page');
        $processRequest = (array) $request->getVar();

        $setFrom = isset($processRequest['setFrom']) ? $processRequest['setFrom'] : 'gfs@proderj.rj.gov.br';
        $setMail = array(
            isset($processRequest['setMail']) ? $processRequest['setMail'] : 'caiomarinho@proderj.rj.gov.br'
        );
        $setCC = array(
            isset($processRequest['setCC']) ? $processRequest['setCC'] : 'arthuroliveira@proderj.rj.gov.br'
        );
        $setBCC = array(
            isset($processRequest['setBCC']) ? $processRequest['setBCC'] : 'gustavo.hammes@extreme.digital'
        );
        $setSubject = isset($processRequest['setSubject']) ? $processRequest['setSubject'] : '';
        $messageMail = isset($processRequest['messageMail']) ? $processRequest['messageMail'] : '';
        #
        $config['protocol'] = 'smtp';
        $config['SMTPHost'] = 'relay.proderj.rj.gov.br';
        $config['SMTPCrypto'] = false;
        $config['SMTPPort'] = 25;
        $config['mailType'] = 'html';
        $config['SMTPTimeout'] = 256;
        $config['mailPath'] = '/usr/sbin/sendmail';
        #
        //$config['SMTPUser'] = 'testefia@habilidade.com'; 
        //$config['SMTPPass'] = 'Teste@123';
        //$config['protocol'] = 'smtp';
        //$config['SMTPHost'] = 'smtp.kinghost.net';
        //$config['SMTPCrypto'] = 'ssl';
        //$config['SMTPPort'] = 465;
        //$config['mailType'] = 'html';
        //$config['SMTPTimeout'] = 256;
        //$config['mailPath'] = '/usr/sbin/sendmail';
        # 
        $config['charset'] = 'utf-8';
        $config['wordWrap'] = true;
        $email = \Config\Services::email();
        # -- Config Settings
        $email->setHeader('Content-Type', 'text/html; charset=UTF-8');
        $email->setHeader('Content-Transfer-Encoding', 'quoted-printable');
        $email->initialize($config);
        $email->setFrom($setFrom, 'Mr. Gerente');
        $email->setTo($this->stringToArray($setMail));
        $email->setCC($this->stringToArray($setCC));
        $email->setBCC($this->stringToArray($setBCC));
        $email->setSubject($setSubject);
        $email->setMessage($messageMail);
        # -- Anexar a imagem
        //$email->attach(FCPATH . 'assets/img/agenersa/LogoAgenersa_Centro.png', 'inline', 'image1');
        #
        // Envia o e-mail e trata o retorno
        if ($email->send()) {
            return $this->response->setJSON(['result' => 'success']);
        } else {
            // Obtém o debug da mensagem em caso de erro
            $debug = $email->printDebugger(['headers']);
            return $this->response->setJSON(['result' => 'erro', 'debug' => $debug]);
        }
    }

    private function assinatura(array $parameter)
    {
        if (
            isset($parameter['CPF']) &&
            isset($parameter['Certidao'])
        ) {
            $parameter01 = [
                $parameter['CPF'],
                $parameter['Certidao']
            ];
            $parameter02 = implode($parameter01);
            $parameter03 = myChar($parameter02);
            $parameter04 = strtoupper(md5($parameter03));
            return $parameter04;
        }
    }
}
