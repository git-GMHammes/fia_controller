<?php

namespace App\Controllers;

# use App\Models\UploadModel;
use App\Controllers\SystemBaseController;
use App\Controllers\SystemMessageController;
use App\Models\ProgramasModels;
use Exception;

class ProgramaDbController extends BaseController
{
    # private $ModelUpload;
    private $ModelProgramas;
    private $pagination;
    private $message;
    private $uri;

    public function __construct()
    {
        # $this->ModelUpload = new UploadModel();
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
        $this->pagination = new SystemBaseController();
        $this->message = new SystemMessageController();
        $this->ModelProgramas = new ProgramasModels();
    }

    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # Retorno do controller [JSON]
    public function index()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }

    # use App\Controllers\SystemUploadDbController
    # private $DbController
    # $this->DbController = new SystemUploadDbController()
    # $this->DbController->dbFields($fileds = array()
    public function dbFields($processRequestFields = array())
    {
        // myPrint($processRequestFields, 'src\app\Controllers\SystemUploadDbController.php', true);
        $dbCreate = array();
        $autoColumn = $this->ModelProgramas->getColumnsFromTable();
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                // myPrint($value_autoColumn, '', true);
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        // myPrint($dbCreate, 'src\app\Controllers\ExempleDbController.php');
        return ($dbCreate);
    }

    # use App\Controllers\SystemUploadDbController
    # private $DbController
    # $this->DbController = new SystemUploadDbController()
    # $this->DbController->dbFields($fileds = array()
    public function dbFieldsFilter($processRequestFields = array())
    {
        // myPrint($processRequestFields, 'src\app\Controllers\SystemUploadDbController.php', true);
        $dbCreate = array();
        $autoColumn = $this->ModelProgramas->getColumnsFromTable();
        // myPrint($autoColumn, '', true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                // myPrint($value_autoColumn, '', true);
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        // myPrint($dbCreate, 'src\app\Controllers\ExempleDbController.php');
        return ($dbCreate);
    }

    # use App\Controllers\TokenCsrfController
    # $this->DbController = new ExempleDbController()
    # $this->DbController->dbCreate($parameter)
    public function dbCreate($parameter = NULL)
    {
        try {
            $this->ModelProgramas->dbCreate($this->dbFields($parameter));
            $affectedRows = $this->ModelProgramas->affectedRows();
            if ($affectedRows > 0) {
                $dbCreate['insertID'] = $this->ModelProgramas->insertID();
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
                myPrint($e->getMessage(), 'src\app\Controllers\ProgramaDbController.php');
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
    # Retorno do controller [JSON]
    public function dbRead($parameter = NULL, $page = 1)
    {
        $limit = 10;
        $getURI = $this->uri->getSegments();
        if (in_array('select', $getURI)) {
            $limit = 200;
        }
        //
        try {
            if ($parameter !== NULL) {
                $dbResponse = $this
                    ->ModelProgramas
                    ->where('id', $parameter)
                    ->where('form_on', 'Y')
                    ->where('deleted_at', NULL)
                    ->orderBy('updated_at', 'asc')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelProgramas
                    ->where('form_on', 'Y')
                    ->where('deleted_at', NULL)
                    ->orderBy('updated_at', 'asc')
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
            // myPrint($response, 'src\app\Controllers\ExempleDbController.php');
        } catch (\Exception $e) {
            if (DEBUG_MY_PRINT) {
                myPrint($e->getMessage(), 'src\app\Controllers\ProgramaDbController.php');
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
    # Retorno do controller [JSON]
    public function dbFilter($parameter = NULL, $page = 1, $limit = 10)
    {
        $parameter = $this->dbFieldsFilter($parameter);
        $getURI = $this->uri->getSegments();
        if (in_array('filtrar', $getURI)) {
            $limit = 200;
        }
        //
        try {
            if (in_array('filtrarlixo', $getURI)) {
                $limit = 200;
                $query = $this
                    ->ModelProgramas
                    ->where('form_on', 'Y')
                    ->where('deleted_at !=', NULL);
            } else {
                $query = $this
                    ->ModelProgramas
                    ->where('form_on', 'Y')
                    ->where('deleted_at', NULL);
            }
            //
            foreach ($parameter as $key => $value) {
                $query = $query->like($key, $value);
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
    # Retorno do controller [JSON]
    public function dbUpdate($key, $parameter = NULL)
    {
        if (
            !isset($parameter['deleted_at'])
            && empty($parameter['deleted_at'])
            && count($parameter) == 1
        ) {
            $this->ModelProgramas->dbUpdate($key, $parameter);
        } else {
            $this->ModelProgramas->dbUpdate($key, $this->dbFields($parameter));
        }
        try {
            $this->ModelProgramas->dbUpdate($key, $this->dbFields($parameter));
            $affectedRows = $this->ModelProgramas->affectedRows();
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
                myPrint($e->getMessage(), 'src\app\Controllers\ProgramaDbController.php');
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
    # Retorno do controller [JSON]
    public function dbDelete($parameter = NULL)
    {
        $dbUpdate = array(
            'id' => $parameter,
            'deleted_at' => date('Y-m-d H:i:m')
        );
        try {
            $this->ModelProgramas->dbUpdate($parameter, $dbUpdate);
            $affectedRows = $this->ModelProgramas->affectedRows();
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
                myPrint($e->getMessage(), 'src\app\Controllers\ProgramaDbController.php');
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
    # Retorno do controller [JSON]
    public function dbCleaner($parameter = NULL, $page = 1)
    {
        $limit = 10;
        #
        try {
            // exit('src\app\Controllers\ProgramaDbController.php');
            if (isset($processRequest['id'])) {
                $dbResponse = $this
                    ->ModelProgramas
                    ->where('id', $processRequest['id'])
                    ->where('deleted_at !=', NULL)
                    ->orderBy('id', 'asc')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } elseif ($parameter !== NULL) {
                $dbResponse = $this
                    ->ModelProgramas
                    ->where('id', $parameter)
                    ->where('deleted_at !=', NULL)
                    ->orderBy('id', 'asc')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelProgramas
                    ->where('deleted_at !=', NULL)
                    ->orderBy('id', 'asc')
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
                myPrint($e->getMessage(), 'src\app\Controllers\ProgramaDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', $parameter, 5);
            $response = array();
        }
        return $response;
    }
}
