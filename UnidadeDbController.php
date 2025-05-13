<?php

namespace App\Controllers;

# use App\Models\UploadModel;
use App\Controllers\SystemBaseController;
use App\Controllers\SystemMessageController;
use App\Models\UnidadesModels;
use App\Models\VUnidadesMunicipiosModels;
use Exception;

class UnidadeDbController extends BaseController
{
    // private $ModelUpload;
    private $ModelUnidades;
    private $ModelUnidadesMunicipios;
    private $pagination;
    private $message;
    private $uri;

    public function __construct()
    {
        // $this->ModelUpload = new UploadModel();
        $this->ModelUnidades = new UnidadesModels();
        $this->ModelUnidadesMunicipios = new VUnidadesMunicipiosModels();
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
        $this->message = new SystemMessageController();
        $this->pagination = new SystemBaseController();
    }

    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [JSON]
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
        // myPrint('$processRequestFields', $processRequestFields, true);
        $dbCreate = array();
        $autoColumn = $this->ModelUnidades->getColumnsFromTable();
        // myPrint('$autoColumn', $autoColumn, true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        (isset($processRequestFields['unidades_data_cadastramento'])) ? ($dbCreate['DataCadastramento'] = $processRequestFields['unidades_data_cadastramento']) : (NULL);
        (isset($processRequestFields['unidades_cap_atendimento'])) ? ($dbCreate['CapAtendimento'] = $processRequestFields['unidades_cap_atendimento']) : (NULL);
        (isset($processRequestFields['unidades_Complemento'])) ? ($dbCreate['Complemento'] = $processRequestFields['unidades_Complemento']) : (NULL);
        (isset($processRequestFields['unidades_Logradouro'])) ? ($dbCreate['Logradouro'] = $processRequestFields['unidades_Logradouro']) : (NULL);
        (isset($processRequestFields['unidades_Municipio'])) ? ($dbCreate['Municipio'] = $processRequestFields['unidades_Municipio']) : (NULL);
        (isset($processRequestFields['unidades_Bairro'])) ? ($dbCreate['Bairro'] = $processRequestFields['unidades_Bairro']) : (NULL);
        (isset($processRequestFields['unidades_Numero'])) ? ($dbCreate['Numero'] = $processRequestFields['unidades_Numero']) : (NULL);
        (isset($processRequestFields['idUnidades'])) ? ($dbCreate['idUnidades'] = $processRequestFields['idUnidades']) : (NULL);
        (isset($processRequestFields['unidades_nome'])) ? ($dbCreate['Nome'] = $processRequestFields['unidades_nome']) : (NULL);
        (isset($processRequestFields['unidades_CEP'])) ? ($dbCreate['CEP'] = $processRequestFields['unidades_CEP']) : (NULL);
        (isset($processRequestFields['id'])) ? ($dbCreate['id'] = $processRequestFields['id']) : (NULL);
        // myPrint('$dbCreate', $dbCreate);
        return ($dbCreate);
    }

    # use App\Controllers\SystemUploadDbController;
    # private $DbController;
    # $this->DbController = new SystemUploadDbController();
    # $this->DbController->dbFields($fileds = array();
    public function dbFieldsFilter($processRequestFields = array())
    {
        // myPrint('$processRequestFields', $processRequestFields, true);
        $dbCreate = array();
        $autoColumn = $this->ModelUnidadesMunicipios->getColumnsFromTable();
        // myPrint('$autoColumn', $autoColumn, true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        (isset($processRequestFields['assinatura'])) ? ($dbCreate['unidade_assinatura'] = $processRequestFields['assinatura']) : (NULL);
        (isset($processRequestFields['Nome'])) ? ($dbCreate['unidades_nome'] = $processRequestFields['Nome']) : (NULL);
        (isset($processRequestFields['unidades_Logradouro'])) ? ($dbCreate['unidades_Logradouro'] = $processRequestFields['unidades_Logradouro']) : (NULL);
        (isset($processRequestFields['unidades_bairro'])) ? ($dbCreate['unidades_Bairro'] = $processRequestFields['unidades_bairro']) : (NULL);
        (isset($processRequestFields['CapAtendimento'])) ? ($dbCreate['unidades_cap_atendimento'] = $processRequestFields['CapAtendimento']) : (NULL);
        (isset($processRequestFields['DataCadastramento'])) ? ($dbCreate['unidades_data_cadastramento'] = $processRequestFields['DataCadastramento']) : (NULL);
        // myPrint('$dbCreate', $dbCreate);
        return ($dbCreate);
    }

    # use App\Controllers\TokenCsrfController;
    # $this->DbController = new UnidadeDbController();
    # $this->DbController->dbCreate($parameter);
    public function dbCreate($parameter = NULL)
    {
        try {
            $parameter = $this->dbFields($parameter);
            $this->ModelUnidades->dbCreate($parameter);
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
                // myPrint($e->getMessage(), 'src\app\Controllers\UnidadeDbController.php');
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
    public function dbRead($parameter = NULL, $page = 1, $limit = 10)
    {
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
                $query = $this
                    ->ModelUnidadesMunicipios
                    ->where('unidades_cap_atendimento', $unidades_cap_atendimento);
            } elseif ($dt_inicio !== null && $dt_fim !== null) {
                $query = $this
                    ->ModelUnidadesMunicipios
                    ->where('deleted_at', NULL)
                    ->where('unidades_data_cadastramento >=', $dt_inicio)
                    ->where('unidades_data_cadastramento <=', $dt_fim);
            } elseif (in_array('filtrar', $getURI)) {
                $query = $this
                    ->ModelUnidadesMunicipios
                    ->where('deleted_at', NULL);
            } elseif (in_array('filtrarlixo', $getURI)) {
                $query = $this
                    ->ModelUnidadesMunicipios
                    ->where('deleted_at !=', NULL);
            } else {
                $query = $this
                    ->ModelUnidadesMunicipios
                    ->where('deleted_at', NULL);
            }

            foreach ($parameter as $key => $value) {
                $query = $query->like($key, $value);
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
            if ($parameter !== NULL) {
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
