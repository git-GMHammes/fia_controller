<?php

namespace App\Controllers;

# use App\Models\UploadModel;
use App\Controllers\SystemBaseController;
use App\Controllers\SystemMessageController;
use App\Models\UnidadesModels;
use App\Models\VUnidadesMunicipiosModels;
use App\Models\VCadastroProfissionalModels;

use Exception;

class AlocarFuncionarioDbController extends BaseController
{
    // private $ModelUpload;
    private $ModelUnidades;
    private $ModelUnidadesMunicipios;
    private $ModelVCadastroProfissional;
    private $pagination;
    private $message;
    private $uri;

    public function __construct()
    {
        // $this->ModelUpload = new UploadModel();
        $this->ModelUnidades = new UnidadesModels();
        $this->ModelVCadastroProfissional = new VCadastroProfissionalModels();
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
        $this->message = new SystemMessageController();
        $this->pagination = new SystemBaseController();
    }

    # route POST /www/sigla/rota
    // route GET /www/sigla/rota
    // Informação sobre o controller
    // retorno do controller [JSON]
    public function index()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }

    # use App\Controllers\SystemUploadDbController;
    # private $DbController;
    # $this->DbController = new SystemUploadDbController();
    # $this->DbController->dbFields($fileds = array();
    public function dbFields($processRequestFields = array())
    {
        // myPrint('$processRequestFields ::', $processRequestFields, true);
        $dbCreate = array();
        $autoColumn = $this->ModelUnidades->getColumnsFromTable();
        // myPrint('$autoColumn', $autoColumn, true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        (isset($processRequestFields['id'])) ? ($dbCreate['id'] = $processRequestFields['id']) : (NULL);
        (isset($processRequestFields['idUnidades'])) ? ($dbCreate['idUnidades'] = $processRequestFields['idUnidades']) : (NULL);
        (isset($processRequestFields['unidade_nome'])) ? ($dbCreate['Nome'] = $processRequestFields['unidade_nome']) : (NULL);
        (isset($processRequestFields['unidades_endereco'])) ? ($dbCreate['Endereco'] = $processRequestFields['unidades_endereco']) : (NULL);
        (isset($processRequestFields['unidades_Logradouro'])) ? ($dbCreate['Logradouro'] = $processRequestFields['unidades_Logradouro']) : (NULL);
        (isset($processRequestFields['unidades_Numero'])) ? ($dbCreate['Numero'] = $processRequestFields['unidades_Numero']) : (NULL);
        (isset($processRequestFields['unidades_Bairro'])) ? ($dbCreate['Bairro'] = $processRequestFields['unidades_Bairro']) : (NULL);
        (isset($processRequestFields['unidades_Complemento'])) ? ($dbCreate['Complemento'] = $processRequestFields['unidades_Complemento']) : (NULL);
        (isset($processRequestFields['unidades_cap_atendimento'])) ? ($dbCreate['CapAtendimento'] = $processRequestFields['unidades_cap_atendimento']) : (NULL);
        (isset($processRequestFields['unidades_data_cadastramento'])) ? ($dbCreate['DataCadastramento'] = $processRequestFields['unidades_data_cadastramento']) : (NULL);
        // myPrint('$dbCreate', $dbCreate);
        return ($dbCreate);
    }

    # use App\Controllers\SystemUploadDbController;
    # private $DbController;
    # $this->DbController = new SystemUploadDbController();
    # $this->DbController->dbFields($fileds = array();
    public function dbFieldsFilter($processRequestFields = array())
    {
        // myPrint('$processRequestFields :: ', $processRequestFields);
        $dbCreate = array();
        $autoColumn = $this->ModelUnidadesMunicipios->getColumnsFromTable();
        // myPrint($autoColumn, '', true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                // myPrint($value_autoColumn, '', true);
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        (isset($processRequestFields['Nome'])) ? ($dbCreate['unidade_nome'] = $processRequestFields['Nome']) : (NULL);
        (isset($processRequestFields['Endereco'])) ? ($dbCreate['unidades_endereco'] = $processRequestFields['Endereco']) : (NULL);
        (isset($processRequestFields['Logradouro'])) ? ($dbCreate['unidades_Logradouro'] = $processRequestFields['Logradouro']) : (NULL);
        (isset($processRequestFields['Numero'])) ? ($dbCreate['unidades_Numero'] = $processRequestFields['Numero']) : (NULL);
        (isset($processRequestFields['Bairro'])) ? ($dbCreate['unidades_Bairro'] = $processRequestFields['Bairro']) : (NULL);
        (isset($processRequestFields['Complemento'])) ? ($dbCreate['unidades_Complemento'] = $processRequestFields['Complemento']) : (NULL);
        (isset($processRequestFields['CapAtendimento'])) ? ($dbCreate['unidades_cap_atendimento'] = $processRequestFields['CapAtendimento']) : (NULL);
        (isset($processRequestFields['DataCadastramento'])) ? ($dbCreate['unidades_data_cadastramento'] = $processRequestFields['DataCadastramento']) : (NULL);
        // myPrint($dbCreate, 'src\app\Controllers\ExempleDbController.php');
        return ($dbCreate);
    }

    # use App\Controllers\TokenCsrfController;
    # $this->DbController = new UnidadeDbController();
    # $this->DbController->dbCreate($parameter);
    public function dbCreate($parameter = NULL)
    {
        try {
            $this->ModelUnidades->dbCreate($this->dbFields($parameter));
            $affectedRows = $this->ModelUnidades->affectedRows();
            if ($affectedRows > 0) {
                $dbCreate['insertID'] = $this->ModelUnidades->insertID();
                $dbCreate['affectedRows'] = $affectedRows;
                $dbCreate['dbCreate'] = $parameter;
            } else {
                $dbCreate['insertID'] = NULL;
                $dbCreate['affectedRows'] = $affectedRows;
                $dbCreate['dbCreate'] = $parameter;
            }
            $response = $dbCreate;
        } catch (\Exception $e) {
            if (DEBUG_MY_PRINT) {
                myPrint($e->getMessage(), 'src\app\Controllers\UnidadeDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', $parameter, 5);
            $response = array();
        }
        return $response;
    }

    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbConsult($parameter = NULL, $page = 1, $limit = 10)
    {
        // myPrint('$parameter :: ', $parameter);
        try {
            if ($parameter !== NULL) {
                $dbResponse = $this
                    ->ModelVCadastroProfissional
                    ->where('UnidadeId', $parameter)
                    ->where('deleted_at', NULL)
                    ->orderBy('updated_at', 'DESC')
                    ->dBread()
                    ->paginate($limit, 'paginator', $page);

            } else {
                $dbResponse = $this
                    ->ModelVCadastroProfissional
                    ->where('deleted_at', NULL)
                    ->orderBy('updated_at', 'DESC')
                    ->dBread()
                    ->paginate($limit, 'paginator', $page);

            }
            // exit('src\app\Controllers\AlocarFuncionarioDbController.php');
            // Paginação
            $pager = \Config\Services::pager();
            $paginationLinks = $pager->makeLinks($page, $limit, $pager->getTotal('paginator'), 'default_full');
            $linksArray = $this->pagination->extractPaginationLinks($paginationLinks);
            //
            $response = array(
                'dbResponse' => $dbResponse,
                'linksArray' => $linksArray
            );
            //
        } catch (\Exception $e) {
            if (DEBUG_MY_PRINT) {
                myPrint($e->getMessage(), 'src\app\Controllers\UnidadeDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', $parameter, 5);
            $response = array();
        }
        return $response;
    }

    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbRead($parameter = NULL, $page = 1)
    {
        // myPrint($page, 'src\app\Controllers\UnidadeDbController.php');
        $limit = 10;
        $getURI = $this->uri->getSegments();
        if (in_array('select', $getURI)) {
            $limit = 200;
        }
        //
        try {
            if ($parameter !== NULL) {
                $dbResponse = $this
                    ->ModelUnidadesMunicipios
                    ->where('id', $parameter)
                    ->where('deleted_at', NULL)
                    ->orderBy('updated_at', 'DESC')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelUnidadesMunicipios
                    ->where('deleted_at', NULL)
                    ->orderBy('updated_at', 'DESC')
                    ->dBread()
                    ->paginate($limit, 'paginator', $page);
                //
            }
            // myPrint($dbResponse, 'src\app\Controllers\UnidadeDbController.php');
            // Paginação
            $pager = \Config\Services::pager();
            $paginationLinks = $pager->makeLinks($page, $limit, $pager->getTotal('paginator'), 'default_full');
            $linksArray = $this->pagination->extractPaginationLinks($paginationLinks);
            //
            $response = array(
                'dbResponse' => $dbResponse,
                'linksArray' => $linksArray
            );
            //
        } catch (\Exception $e) {
            if (DEBUG_MY_PRINT) {
                myPrint($e->getMessage(), 'src\app\Controllers\UnidadeDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', $parameter, 5);
            $response = array();
        }
        return $response;
    }

    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbFilter($parameter = NULL, $page = 1, $limit = 10)
    {
        $dt_inicio = isset($parameter['unidades_data_cadastramento_inicio']) ? $parameter['unidades_data_cadastramento_inicio'] : null;
        $dt_fim = isset($parameter['unidades_data_cadastramento_fim']) ? $parameter['unidades_data_cadastramento_fim'] : null;
        #
        if ($dt_inicio !== null && $dt_fim == null) {
            $parameter['unidades_data_cadastramento'] = $parameter['unidades_data_cadastramento_inicio'];
        }
        #
        // myPrint($parameter, 'src\app\Controllers\UnidadeDbController.php');
        $parameter = $this->dbFieldsFilter($parameter);
        // myPrint($parameter, 'src\app\Controllers\UnidadeDbController.php');

        $getURI = $this->uri->getSegments();
        #
        try {
            if (isset($parameter['unidades_cap_atendimento'])) {
                $unidades_cap_atendimento = $parameter['unidades_cap_atendimento'];
                unset($parameter['unidades_cap_atendimento']);
                $limit = 200;
                $query = $this
                    ->ModelUnidadesMunicipios
                    ->where('unidades_cap_atendimento', $unidades_cap_atendimento);
            } elseif ($dt_inicio !== null && $dt_fim !== null) {
                $limit = 200;
                $query = $this
                    ->ModelUnidadesMunicipios
                    ->where('deleted_at', NULL)
                    ->where('unidades_data_cadastramento >=', $dt_inicio)
                    ->where('unidades_data_cadastramento <=', $dt_fim);
            } elseif (in_array('filtrar', $getURI)) {
                $limit = 200;
                $query = $this
                    ->ModelUnidadesMunicipios
                    ->where('deleted_at', NULL);
            } elseif (in_array('filtrarlixo', $getURI)) {
                $limit = 200;
                $query = $this
                    ->ModelUnidadesMunicipios
                    ->where('deleted_at !=', NULL);
            } else {
                $query = $this
                    ->ModelUnidadesMunicipios
                    ->where('deleted_at', NULL);
            }

            foreach ($parameter as $key => $value) {
                if ($key === 'unidades_Logradouro') {
                    $query = $query->groupStart()
                        ->like('Logradouro', $value)
                        ->orLike('Numero', $value)
                        ->orLike('Complemento', $value)
                        ->orLike('Bairro', $value)
                        ->groupEnd();
                } else {
                    $query = $query->like($key, $value);
                }
                // myPrint($key, $value, true);
            }

            $dbResponse = $query
                ->orderBy('updated_at', 'DESC')
                ->paginate($limit, 'paginator', $page);
            // exit('178');

            // Paginação
            $pager = \Config\Services::pager();
            $paginationLinks = $pager->makeLinks($page, $limit, $pager->getTotal('paginator'), 'default_full');
            $linksArray = $this->pagination->extractPaginationLinks($paginationLinks);
            //
            $response = array(
                'dbResponse' => $dbResponse,
                'linksArray' => $linksArray
            );
            //
        } catch (\Exception $e) {
            if (DEBUG_MY_PRINT) {
                myPrint($e->getMessage(), 'src\app\Controllers\ExempleDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', $parameter, 5);
            $response = array();
        }
        return $response;
    }

    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbUpdate($key, $parameter = NULL)
    {
        // myPrint(!isset($parameter['deleted_at']), '234', true);
        // myPrint(empty($parameter['deleted_at']), '236', true);
        // myPrint(count($parameter) == 1, '237');
        try {
            if (
                !isset($parameter['deleted_at'])
                && empty($parameter['deleted_at'])
                && count($parameter) == 1
            ) {
                $this->ModelUnidades->dbUpdate($key, $parameter);
            } else {
                // exit('Não quero aqui');
                $this->ModelUnidades->dbUpdate($key, $this->dbFields($parameter));
            }
            #
            $affectedRows = $this->ModelUnidades->affectedRows();
            #
            if ($affectedRows > 0) {
                $dbUpdate['updateID'] = $key;
                $dbUpdate['affectedRows'] = $affectedRows;
                $dbUpdate['dbUpdate'] = $parameter;
            } else {
                $dbUpdate['updateID'] = $key;
                $dbUpdate['affectedRows'] = $affectedRows;
                $dbUpdate['dbUpdate'] = $parameter;
            }
            $response = $dbUpdate;
        } catch (\Exception $e) {
            if (DEBUG_MY_PRINT) {
                myPrint($e->getMessage(), 'src\app\Controllers\UnidadeDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', $parameter, 5);
            $response = array();
        }
        return $response;
    }

    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbDelete($parameter = NULL)
    {
        try {
            $this->ModelUnidades->dbDelete('id', $parameter);
            $affectedRows = $this->ModelUnidades->affectedRows();
            if ($affectedRows > 0) {
                $dbUpdate['updateID'] = $parameter;
                $dbUpdate['affectedRows'] = $affectedRows;
            } else {
                $dbUpdate['updateID'] = $parameter;
                $dbUpdate['affectedRows'] = $affectedRows;
            }
            $response = $dbUpdate;
        } catch (\Exception $e) {
            if (DEBUG_MY_PRINT) {
                myPrint($e->getMessage(), 'src\app\Controllers\UnidadeDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', $parameter, 5);
            $response = array();
        }
        return $response;
    }

    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbCleaner($parameter = NULL, $page = 1)
    {
        $limit = 10;
        try {
            // exit('src\app\Controllers\UnidadeDbController.php');
            if (isset($processRequest['id'])) {
                $dbResponse = $this
                    ->ModelUnidadesMunicipios
                    ->where('id', $processRequest['id'])
                    ->where('deleted_at !=', NULL)
                    ->orderBy('updated_at', 'DESC')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } elseif ($parameter !== NULL) {
                $dbResponse = $this
                    ->ModelUnidadesMunicipios
                    ->where('id', $parameter)
                    ->where('deleted_at !=', NULL)
                    ->orderBy('updated_at', 'DESC')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelUnidadesMunicipios
                    ->where('deleted_at !=', NULL)
                    ->orderBy('updated_at', 'DESC')
                    ->dBread()
                    ->paginate($limit, 'paginator', $page);
                //
            }
            ;
            // Paginação
            $pager = \Config\Services::pager();
            $paginationLinks = $pager->makeLinks($page, $limit, $pager->getTotal('paginator'), 'default_full');
            $linksArray = $this->pagination->extractPaginationLinks($paginationLinks);
            //
            $response = array(
                'dbResponse' => $dbResponse,
                'linksArray' => $linksArray
            );
            //
        } catch (\Exception $e) {
            if (DEBUG_MY_PRINT) {
                myPrint($e->getMessage(), 'src\app\Controllers\UnidadeDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', $parameter, 5);
            $response = array();
        }
        return $response;
    }
}
