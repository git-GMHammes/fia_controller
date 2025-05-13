<?php

namespace App\Controllers;

# use App\Models\UploadModel;
use App\Controllers\SystemBaseController;
use App\Controllers\SystemMessageController;
use App\Models\SegurancaModels;
use App\Models\VsegurancaPerfilCargoFuncaoModels;
# use App\Models\VTabelaPrincipalModelsModels;
use Exception;

class SegurancaDbController extends BaseController
{
    // private $ModelUpload;
    private $ModelSeguranca;
    private $ModelVsegurancaPerfilCargoFuncao;
    private $message;
    private $uri;
    private $pagination;

    public function __construct()
    {
        $this->ModelSeguranca = new SegurancaModels();
        $this->ModelVsegurancaPerfilCargoFuncao = new VsegurancaPerfilCargoFuncaoModels();
        // $this->ModelUpload = new UploadModel();
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
        $this->pagination = new SystemBaseController();
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
        // myPrint('$processRequestFields :: ', $processRequestFields, true);
        $dbCreate = array();
        $autoColumn = $this->ModelSeguranca->getColumnsFromTable();
        // myPrint($autoColumn, '', true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        (isset($processRequestFields['modelo'])) ? ($dbCreate['modelo'] = $processRequestFields['modelo']) : (NULL);
        // myPrint($dbCreate, 'src\app\Controllers\ExempleDbController.php');
        return ($dbCreate);
    }

    # use App\Controllers\SystemUploadDbController;
    # private $DbController;
    # $this->DbController = new SystemUploadDbController();
    # $this->DbController->dbFields($fileds = array();
    public function dbFieldsFilter($processRequestFields = array())
    {
        // myPrint($processRequestFields, 'src\app\Controllers\SystemUploadDbController.php', true);
        $dbCreate = array();
        $autoColumn = $this->ModelVsegurancaPerfilCargoFuncao->getColumnsFromTable();
        // myPrint($autoColumn, '', true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }

        // myPrint($dbCreate, 'src\app\Controllers\ExempleDbController.php', true);
        return ($dbCreate);
    }

    # use App\Controllers\TokenCsrfController;
    # $this->DbController = new ExempleDbController();
    # $this->DbController->dbCreate($parameter);
    public function dbCreate($parameter = NULL)
    {

        try {
            $this->ModelSeguranca->dbCreate($this->dbFields($parameter));
            $affectedRows = $this->ModelSeguranca->affectedRows();
            if ($affectedRows > 0) {
                $dbCreate['insertID'] = $this->ModelSeguranca->insertID();
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
    public function dbRead($parameter = NULL, $page = 1, $limit = 10)
    {
        try {
            $baseQuery = $this->ModelVsegurancaPerfilCargoFuncao
                ->where('seg_metodo_acao !=', 'filtrarlixo')
                ->where('seg_metodo_acao !=', 'limpar')
                ->where('deleted_at', NULL)
                ->orderBy('seg_projeto', 'asc')
                ->orderBy('seg_sub_projeto', 'asc')
                ->orderBy('pf_perfil', 'asc')
                ->orderBy('cf_cargo_funcao', 'asc')
                ->orderBy('seg_modulo', 'asc')
                ->orderBy('seg_metodo_acao', 'asc');

            // Então usar condicionalmente
            if ($parameter !== NULL) {
                $baseQuery->where('id', $parameter);
                $baseQuery->where('seg_permitido', 'Y');
                $limit = 1;
            }
            $dbResponse = $baseQuery->dBread()
                ->paginate($limit, 'paginator', $page);
            // if ($parameter !== NULL) {
            //     $dbResponse = $this
            //         ->ModelVsegurancaPerfilCargoFuncao
            //         ->where('id', $parameter)
            //         ->where('seg_permitido', 'Y')
            //         ->where('metodo_acao !=', 'filtrarlixo')
            //         ->where('metodo_acao !=', 'limpar')
            //         ->where('deleted_at', NULL)
            //         ->orderBy('seg_projeto', 'asc')
            //         ->orderBy('seg_sub_projeto', 'asc')
            //         ->orderBy('pf_perfil', 'asc')
            //         ->orderBy('cf_cargo_funcao', 'asc')
            //         ->orderBy('seg_modulo', 'asc')
            //         ->orderBy('seg_metodo_acao', 'asc')
            //         ->dBread()
            //         ->paginate(1, 'paginator', $page);
            //     //
            // } else {
            //     $dbResponse = $this
            //         ->ModelVsegurancaPerfilCargoFuncao
            //         ->where('metodo_acao !=', 'filtrarlixo')
            //         ->where('metodo_acao !=', 'limpar')
            //         ->where('deleted_at', NULL)
            //         ->orderBy('seg_projeto', 'asc')
            //         ->orderBy('seg_sub_projeto', 'asc')
            //         ->orderBy('pf_perfil', 'asc')
            //         ->orderBy('cf_cargo_funcao', 'asc')
            //         ->orderBy('seg_modulo', 'asc')
            //         ->orderBy('seg_metodo_acao', 'asc')
            //         ->dBread()
            //         ->paginate($limit, 'paginator', $page);
            //     //
            // }
            // Paginação
            $pager = \Config\Services::pager();
            $paginationLinks = $pager->makeLinks($page, $limit, $pager->getTotal('paginator'), 'default_full');
            $linksArray = $this->pagination->extractPaginationLinks($paginationLinks);
            //
            $response = array(
                'dbResponse' => $dbResponse,
                'linksArray' => $linksArray
            );
            // myPrint($response, 'src\app\Controllers\UsuarioDbController.php');
        } catch (\Exception $e) {
            if (DEBUG_MY_PRINT) {
                myPrint($e->getMessage(), 'src\app\Controllers\UsuarioDbController.php');
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
        // 
        try {
            $query = $this
                ->ModelVsegurancaPerfilCargoFuncao
                ->where('seg_metodo_acao !=', 'filtrarlixo')
                ->where('seg_metodo_acao !=', 'limpar')
                ->where('deleted_at', NULL);
            foreach ($parameter as $key => $value) {
                $query = $query->like($key, $value);
                // myPrint($key, $value, true);
            }

            $dbResponse = $query
                ->orderBy('seg_projeto', 'asc')
                ->orderBy('seg_sub_projeto', 'asc')
                ->orderBy('pf_perfil', 'asc')
                ->orderBy('cf_cargo_funcao', 'asc')
                ->orderBy('seg_modulo', 'asc')
                ->orderBy('seg_metodo_acao', 'asc')
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
        if (
            !isset($parameter['deleted_at'])
            && empty($parameter['deleted_at'])
            && count($parameter) == 1
        ) {
            $this->ModelSeguranca->dbUpdate($key, $parameter);
        } else {
            $this->ModelSeguranca->dbUpdate($key, $this->dbFields($parameter));
        }
        try {
            $affectedRows = $this->ModelSeguranca->affectedRows();
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
    public function dbDelete($parameter = NULL)
    {
        // myPrint('$parameter', $parameter);
        try {
            $this->ModelSeguranca->dbDelete($parameter);
            $affectedRows = $this->ModelSeguranca->affectedRows();
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
    public function dbCleaner($parameter = NULL, $page = 1)
    {
        $limit = 10;
        try {
            // exit('src\app\Controllers\AdolescenteDbController.php');
            if ($parameter !== NULL) {
                $dbResponse = $this
                    ->ModelSeguranca
                    ->where('id', $parameter)
                    ->where('deleted_at !=', NULL)
                    ->orderBy('id', 'DESC')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelSeguranca
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
                myPrint($e->getMessage(), 'src\app\Controllers\ExempleDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', $parameter, 5);
            $response = array();
        }
        return $response;
    }
}

?>