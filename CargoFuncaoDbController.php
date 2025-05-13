<?php

namespace App\Controllers;

# use App\Models\UploadModel;
use App\Controllers\SystemMessageController;
use App\Models\CargoFuncaoModels;
use App\Controllers\SystemBaseController;
use Exception;

class CargoFuncaoDbController extends BaseController
{
    # private $ModelUpload;
    private $ModelCargoFuncao;
    private $message;
    private $pagination;
    private $uri;

    public function __construct()
    {
        // $this->ModelUpload = new UploadModel();
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
        $this->pagination = new SystemBaseController();
        $this->message = new SystemMessageController();
        $this->ModelCargoFuncao = new CargoFuncaoModels();
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
        // myPrint($processRequestFields, 'src\app\Controllers\SystemUploadDbController.php', true);
        $dbCreate = array();
        $autoColumn = $this->ModelCargoFuncao->getColumnsFromTable();
        // myPrint($autoColumn, '', true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        (isset($processRequestFields['submit_cargo_funcao'])) ? ($dbCreate['cargo_funcao'] = $processRequestFields['submit_cargo_funcao']) : (NULL);
        // myPrint($dbCreate, 'src\app\Controllers\CargoFuncaoDbController.php');
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
        $autoColumn = $this->ModelCargoFuncao->getColumnsFromTable();
        // myPrint('$autoColumn', $autoColumn, true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        // myPrint('$dbCreate', $dbCreate);
        return ($dbCreate);
    }

    # use App\Controllers\TokenCsrfController;
    # $this->DbController = new CargoFuncaoDbController();
    # $this->DbController->dbCreate($parameter);
    public function dbCreate($parameter = NULL)
    {
        $parameter = $this->dbFields($parameter);
        try {
            $this->ModelCargoFuncao->dbCreate($parameter);
            $affectedRows = $this->ModelCargoFuncao->affectedRows();
            if ($affectedRows > 0) {
                $dbCreate['insertID'] = $this->ModelCargoFuncao->insertID();
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
                myPrint($e->getMessage(), 'src\app\Controllers\CargoFuncaoDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', $parameter, 5);
            $response = array();
        }
        // myPrint('$response :: ', $response);
        return $response;
    }

    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbRead($parameter = NULL, $page = 1)
    {
        // Parâmentros para receber um POST
        $request = service('request');
        $processRequest = (array) $request->getVar();
        $limit = 10;
        $getURI = $this->uri->getSegments();
        if (in_array('select', $getURI)) {
            $limit = 200;
        }
        // $processRequest = eagarScagaire($processRequest);
        //
        try {
            if ($parameter !== NULL) {
                $dbResponse = $this
                    ->ModelCargoFuncao
                    ->where('id', $parameter)
                    ->where('form_on', 'Y')
                    ->where('deleted_at', NULL)
                    ->orderBy('cargo_funcao', 'asc')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelCargoFuncao
                    ->where('deleted_at', NULL)
                    ->where('form_on', 'Y')
                    ->orderBy('cargo_funcao', 'asc')
                    ->dBread()
                    ->paginate($limit, 'paginator', $page);
                //
            }
            // myPrint($dbResponse, 'src\app\Controllers\CargoFuncaoDbController.php');
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
                myPrint($e->getMessage(), 'src\app\Controllers\CargoFuncaoDbController.php');
            }
            // exit();
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
        $getURI = $this->uri->getSegments();
        if (in_array('filtrar', $getURI)) {
            $limit = 200;
        }
        //
        try {
            if (in_array('id', array_keys($parameter))) {
                $limit = 1;
                $query = $this
                    ->ModelCargoFuncao
                    ->where('id', $parameter)
                    ->where('form_on', 'Y')
                    ->where('deleted_at', NULL);
            } else {
                $query = $this
                    ->ModelCargoFuncao
                    ->where('form_on', 'Y')
                    ->where('deleted_at', NULL);
            }
            foreach ($parameter as $key => $value) {
                // myPrint($key, $value);
                $query = $query->like($key, $value);
            }
            $dbResponse = $query
                ->orderBy('cargo_funcao', 'asc')
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
            $this->ModelCargoFuncao->dbUpdate($key, $parameter);
        } else {
            $this->ModelCargoFuncao->dbUpdate($key, $this->dbFields($parameter));
        }
        try {
            $affectedRows = $this->ModelCargoFuncao->affectedRows();
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
                myPrint($e->getMessage(), 'src\app\Controllers\CargoFuncaoDbController.php');
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
        $dbUpdate = array(
            'id' => $parameter,
            'deleted_at' => date('Y-m-d H:i:m')
        );
        try {
            $this->ModelCargoFuncao->dbUpdate($parameter, $dbUpdate);
            $affectedRows = $this->ModelCargoFuncao->affectedRows();
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
                myPrint($e->getMessage(), 'src\app\Controllers\CargoFuncaoDbController.php');
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
            $this->ModelCargoFuncao->dBdelete('id', $parameter);
            $affectedRows = $this->ModelCargoFuncao->affectedRows();
            if ($affectedRows > 0) {
                $dBdelete['deleteID'] = $parameter;
                $dBdelete['affectedRows'] = $affectedRows;
            } else {
                $dBdelete['deleteID'] = $parameter;
                $dBdelete['affectedRows'] = $affectedRows;
            }
            $response = $dBdelete;
            if ($parameter !== NULL) {
                $dbResponse = $this
                    ->ModelCargoFuncao
                    ->where('id', $parameter)
                    ->where('deleted_at !=', NULL)
                    ->orderBy('updated_at', 'DESC')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelCargoFuncao
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
                myPrint($e->getMessage(), 'src\app\Controllers\CargoFuncaoDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', $parameter, 5);
            $response = array();
        }
        return $response;
    }
}
