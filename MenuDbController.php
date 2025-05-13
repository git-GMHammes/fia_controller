<?php

namespace App\Controllers;

# use App\Models\UploadModel;
use App\Controllers\SystemMessageController;
use App\Models\MenuModels;
use App\Models\VMenuPerfilCargoModels;
use App\Controllers\SystemBaseController;
use Exception;

class MenuDbController extends BaseController
{
    # private $ModelUpload;
    private $ModelsMenu;
    private $ModelVMenuPerfilCargo;
    private $message;
    private $pagination;
    private $uri;

    public function __construct()
    {
        // $this->ModelUpload = new UploadModel();
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
        $this->pagination = new SystemBaseController();
        $this->message = new SystemMessageController();
        $this->ModelsMenu = new MenuModels();
        $this->ModelVMenuPerfilCargo = new VMenuPerfilCargoModels();
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
        $autoColumn = $this->ModelsMenu->getColumnsFromTable();
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                // myPrint($value_autoColumn, '', true);
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        if ($dbCreate == array()) {
            (isset($processRequestFields['modelo'])) ? ($dbCreate['modelo_db'] = $processRequestFields['modelo']) : (NULL);
        }
        // myPrint($dbCreate, 'src\app\Controllers\MenuDbController.php');
        return ($dbCreate);
    }

    # use App\Controllers\SystemUploadDbController;
    # private $DbController;
    # $this->DbController = new SystemUploadDbController();
    # $this->DbController->dbFields($fileds = array();
    public function dbFieldsFilter($processRequestFields = array())
    {
        // myPrint($processRequestFields, 'src\app\Controllers\MenuDbController.php', true);
        $dbCreate = array();
        $autoColumn = $this->ModelVMenuPerfilCargo->getColumnsFromTable();
        # 
        // myPrint($autoColumn, '', true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        // myPrint($dbCreate, 'src\app\Controllers\MenuDbController.php', true);
        # 
        return ($dbCreate);
    }

    # use App\Controllers\TokenCsrfController;
    # $this->DbController = new MenuDbController();
    # $this->DbController->dbCreate($parameter);
    public function dbCreate($parameter = NULL)
    {
        try {
            $this->ModelsMenu->dbCreate($this->dbFields($parameter));
            $affectedRows = $this->ModelsMenu->affectedRows();
            if ($affectedRows > 0) {
                $dbCreate['insertID'] = $this->ModelsMenu->insertID();
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
                myPrint($e->getMessage(), 'src\app\Controllers\MenuDbController.php');
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
                    ->ModelVMenuPerfilCargo
                    ->where('id', $parameter)
                    ->where('deleted_at', NULL)
                    ->orderBy('id', 'asc')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelVMenuPerfilCargo
                    ->where('deleted_at', NULL)
                    ->orderBy('id', 'asc')
                    ->dBread()
                    ->paginate($limit, 'paginator', $page);
                //
            }
            // myPrint($dbResponse, 'src\app\Controllers\MenuDbController.php');
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
                myPrint($e->getMessage(), 'src\app\Controllers\MenuDbController.php');
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
        } else {
            $limit = 20000;
        }
        //
        if (in_array('id', array_keys($parameter))) {
            $limit = 1;
            $query = $this
                ->ModelVMenuPerfilCargo
                ->where('id', $parameter)
                ->where('deleted_at', NULL);
        } else {
            $query = $this
                ->ModelVMenuPerfilCargo
                ->where('deleted_at', NULL);
        }
        foreach ($parameter as $key => $value) {
            // myPrint($key, $value);
            $query = $query->like($key, $value);
        }
        $dbResponse = $query
            ->orderBy('id', 'asc')
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
        try {
            //
        } catch (\Exception $e) {
            if (DEBUG_MY_PRINT) {
                myPrint($e->getMessage(), 'src\app\Controllers\MenuDbController.php');
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
        $parameter = $this->dbFields($parameter);
        // myPrint('$parameter :: ', $parameter);
        if (
            !isset($parameter['deleted_at'])
            && empty($parameter['deleted_at'])
            && count($parameter) == 1
        ) {
            $this->ModelsMenu->dbUpdate($key, $parameter);
        } else {
            $this->ModelsMenu->dbUpdate($key, $this->dbFields($parameter));
        }
        try {
            $affectedRows = $this->ModelsMenu->affectedRows();
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
                myPrint($e->getMessage(), 'src\app\Controllers\MenuDbController.php');
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
    public function dbDelete($parameter = NULL)
    {
        $dbUpdate = array(
            'id' => $parameter,
            'deleted_at' => date('Y-m-d H:i:m')
        );
        try {
            $this->ModelsMenu->dbUpdate($parameter, $dbUpdate);
            $affectedRows = $this->ModelsMenu->affectedRows();
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
                myPrint($e->getMessage(), 'src\app\Controllers\MenuDbController.php');
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
            $this->ModelsMenu->dBdelete('id', $parameter);
            $affectedRows = $this->ModelsMenu->affectedRows();
            if ($affectedRows > 0) {
                $dBdelete['deleteID'] = $parameter;
                $dBdelete['affectedRows'] = $affectedRows;
            } else {
                $dBdelete['deleteID'] = $parameter;
                $dBdelete['affectedRows'] = $affectedRows;
            }
            $response = $dBdelete;
        } catch (\Exception $e) {
            if (DEBUG_MY_PRINT) {
                myPrint($e->getMessage(), 'src\app\Controllers\MenuDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', $parameter, 5);
            $response = array();
        }
        return $response;
    }
}
