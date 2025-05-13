<?php

namespace App\Controllers;

// use App\Models\UploadModel;
use App\Controllers\SystemMessageController;
use App\Models\PeriodoModels;
use App\Models\VperiodosUnidadesMunicipiosModels;
use Exception;

class PeriodoDbController extends BaseController
{
    // private $ModelUpload;
    private $ModelDbController;
    private $ModelsVperiodosUnidadesMunicipios;
    private $pagination;
    private $message;
    private $uri;

    // $this->pagination = new SystemBaseController();
    // $linksArray = $this->pagination->extractPaginationLinks($paginationLinks);

    public function __construct()
    {
        // $this->ModelUpload = new UploadModel();
        $this->ModelDbController = new PeriodoModels();
        $this->ModelsVperiodosUnidadesMunicipios = new VperiodosUnidadesMunicipiosModels();
        $this->pagination = new SystemBaseController();
        $this->message = new SystemMessageController();
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
        $this->message = new SystemMessageController();
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
        $autoColumn = $this->ModelDbController->getColumnsFromTable();
        // myPrint('$autoColumn', $autoColumn, true);

        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $value_autoColumn) {
                // Atribui o valor apenas se existir no array de request fields
                $dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn] ?? null;
            }
        }

        // Atribuições específicas com alias
        if (isset($processRequestFields['periodo_capacidade_vagas'])) {
            $dbCreate['capacidade_vagas'] = $processRequestFields['periodo_capacidade_vagas'] ?? null;
        }
        $dbCreate['dt_inicio'] = $processRequestFields['periodo_data_inicio'] ?? null;
        $dbCreate['dt_termino'] = $processRequestFields['periodo_data_termino'] ?? null;
        $dbCreate['periodo'] = $processRequestFields['periodo_numero'] ?? null;
        $dbCreate['dbModelo'] = $processRequestFields['Modelo'] ?? null;

        $arrayLimpo = array_filter($dbCreate);
        // myPrint('$arrayLimpo', $arrayLimpo);
        return $arrayLimpo;
    }

    # use App\Controllers\SystemUploadDbController;
    # private $DbController;
    # $this->DbController = new SystemUploadDbController();
    # $this->DbController->dbFields($fileds = array();
    public function dbFieldsFilter($processRequestFields = array())
    {
        // myPrint($processRequestFields, '', true);
        $dbCreate = array();
        $autoColumn = $this->ModelsVperiodosUnidadesMunicipios->getColumnsFromTable();
        // myPrint($autoColumn, '', true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        $dbCreate['periodo_ano'] = $processRequestFields['ano'] ?? null;
        // myPrint($dbCreate, 'src\app\Controllers\PeriodoDbController.php');
        return ($dbCreate);
    }

    # use App\Controllers\TokenCsrfController;
    # $this->DbController = new PeriodoDbController();
    # $this->DbController->dbCreate($parameter);
    public function dbCreate($parameter = NULL)
    {
        try {
            $this->ModelDbController->dbCreate($this->dbFields($parameter));
            $affectedRows = $this->ModelDbController->affectedRows();
            if ($affectedRows > 0) {
                $dbCreate['insertID'] = $this->ModelDbController->insertID();
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
                myPrint($e->getMessage(), 'src\app\Controllers\PeriodoDbController.php');
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
        $limit = 10;
        try {
            if ($parameter !== NULL) {
                $dbResponse = $this
                    ->ModelsVperiodosUnidadesMunicipios
                    ->where('id', $parameter)
                    ->where('deleted_at', NULL)
                    ->orderBy('id', 'DESC')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelsVperiodosUnidadesMunicipios
                    ->where('deleted_at', NULL)
                    ->orderBy('id', 'DESC')
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
        } catch (\Exception $e) {
            if (DEBUG_MY_PRINT) {
                myPrint($e->getMessage(), 'src\app\Controllers\PeriodoDbController.php');
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
        $parameter = $this->dbFieldsFilter($parameter);
        $parameter = array_filter($parameter);

        //
        try {
            # decide se a consulta será por UnidadeId ou por período
            if (
                count($parameter) === 1 &&
                array_key_exists('UnidadeId', $parameter)
            ) {
                $query = $this
                    ->ModelsVperiodosUnidadesMunicipios
                    ->where('UnidadeId', $parameter['UnidadeId']);
            }
            # 
            $query = $this
                ->ModelsVperiodosUnidadesMunicipios
                ->where('deleted_at', NULL);

            foreach ($parameter as $key => $value) {
                if (!in_array($key, ['periodo_data_inicio', 'periodo_data_termino'])) {
                    $query = $query->like($key, $value);
                }
            }

            // Adiciona tratamento específico para as datas
            if (isset($parameter['periodo_data_inicio']) && isset($parameter['periodo_data_termino'])) {
                $query = $query->groupStart()
                    ->where('periodo_data_inicio >=', $parameter['periodo_data_inicio'])
                    ->where('periodo_data_termino <=', $parameter['periodo_data_termino'])
                    ->groupEnd();
            }

            $dbResponse = $query
                ->orderBy('id', 'DESC')
                ->paginate($limit, 'paginator', $page);

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
                myPrint($e->getMessage(), 'src\app\Controllers\PeriodoDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', $parameter, 5);
            $response = array();
        }
        // myPrint($response, 'src\app\Controllers\PeriodoDbController.php');
        return $response;
    }

    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbUpdate($key, $parameter = NULL)
    {
        // myPrint($key, $parameter, true);
        try {
            if (
                !isset($parameter['deleted_at'])
                && empty($parameter['deleted_at'])
                && count($parameter) == 1
            ) {
                $this->ModelDbController->dbUpdate($key, $parameter);
            } else {
                $this->ModelDbController->dbUpdate($key, $this->dbFields($parameter));
            }
            $affectedRows = $this->ModelDbController->affectedRows();
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
                myPrint($e->getMessage(), 'src\app\Controllers\PeriodoDbController.php');
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
            $this->ModelDbController->dbDelete('id', $parameter);
            $affectedRows = $this->ModelDbController->affectedRows();
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
                myPrint($e->getMessage(), 'src\app\Controllers\PeriodoDbController.php');
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
            // exit('src\app\Controllers\AdolescenteDbController.php');
            if ($parameter !== NULL) {
                $dbResponse = $this
                    ->ModelDbController
                    ->where('id', $parameter)
                    ->where('deleted_at !=', NULL)
                    ->orderBy('id', 'DESC')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelDbController
                    ->where('deleted_at !=', NULL)
                    ->orderBy('id', 'DESC')
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
                myPrint($e->getMessage(), 'src\app\Controllers\PeriodoDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', $parameter, 5);
            $response = array();
        }
        return $response;
    }
}
