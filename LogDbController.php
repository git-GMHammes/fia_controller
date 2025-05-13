<?php

namespace App\Controllers;

# use App\Models\UploadModel;
use App\Controllers\SystemBaseController;
use App\Controllers\SystemMessageController;
use App\Models\LogModels;
// use App\Models\VLogModelsModels;
use Exception;

class LogDbController extends BaseController
{
    // private $ModelUpload;
    private $ModelLog;
    // private $ModelVLogl;
    private $message;
    private $uri;
    private $pagination;

    public function __construct()
    {
        // $this->ModelUpload = new UploadModel();
        // $this->ModelVLogl = new VLogModelsModels();
        $this->ModelLog = new LogModels();
        $this->pagination = new SystemBaseController();
        $this->message = new SystemMessageController();
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
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
        $autoColumn = $this->ModelLog->getColumnsFromTable();
        // myPrint($autoColumn, 'src\app\Controllers\SystemUploadDbController.php', true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        (isset($processRequestFields['url_carga'])) ? ($dbCreate['url_carga'] = $processRequestFields['url_carga']) : (NULL);
        (isset($processRequestFields['carga'])) ? ($dbCreate['carga'] = $processRequestFields['carga']) : (NULL);
        (isset($processRequestFields['link'])) ? ($dbCreate['url_link'] = $processRequestFields['link']) : (NULL);
        (isset($processRequestFields['session'])) ? ($dbCreate['user_session'] = $processRequestFields['session']) : (NULL);
        (isset($processRequestFields['server'])) ? ($dbCreate['serverAddr'] = $processRequestFields['server']) : (NULL);
        (isset($processRequestFields['remote'])) ? ($dbCreate['remoteAddr'] = $processRequestFields['remote']) : (NULL);
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
        $autoColumn = $this->ModelLog->getColumnsFromTable();
        // myPrint($autoColumn, '', true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        (isset($processRequestFields['carga'])) ? ($dbCreate['url_carga'] = $processRequestFields['carga']) : (NULL);
        (isset($processRequestFields['link'])) ? ($dbCreate['url_link'] = $processRequestFields['link']) : (NULL);
        (isset($processRequestFields['session'])) ? ($dbCreate['user_session'] = $processRequestFields['session']) : (NULL);
        (isset($processRequestFields['server'])) ? ($dbCreate['serverAddr'] = $processRequestFields['server']) : (NULL);
        (isset($processRequestFields['remote'])) ? ($dbCreate['remoteAddr'] = $processRequestFields['remote']) : (NULL);

        // myPrint($dbCreate, 'src\app\Controllers\ExempleDbController.php');
        return ($dbCreate);
    }

    # use App\Controllers\TokenCsrfController;
    # $this->DbController = new ExempleDbController();
    # $this->DbController->dbCreate($parameter);
    public function dbCreate($parameter = NULL)
    {

        try {
            $this->ModelLog->dbCreate($this->dbFields($parameter));
            $affectedRows = $this->ModelLog->affectedRows();
            if ($affectedRows > 0) {
                $dbCreate['insertID'] = $this->ModelLog->insertID();
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
                // myPrint($e->getMessage(), 'src\app\Controllers\ExempleDbController.php');
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
                    ->ModelLog
                    ->where('id', $parameter)
                    ->where('deleted_at', NULL)
                    ->orderBy('updated_at', 'desc')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelLog
                    ->where('deleted_at', NULL)
                    ->orderBy('updated_at', 'desc')
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
                // myPrint($e->getMessage(), 'src\app\Controllers\ExempleDbController.php');
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

        $getURI = $this->uri->getSegments();
        try {
            if (in_array('id', array_keys($parameter))) {
                $limit = 1;
                $query = $this
                    ->ModelLog
                    ->where('deleted_at', NULL);
            } elseif (in_array('filtrar', $getURI)) {
                $query = $this
                    ->ModelLog
                    ->where('deleted_at', NULL);
            } elseif (in_array('filtrarlixo', $getURI)) {
                $query = $this
                    ->ModelLog
                    ->where('deleted_at !=', NULL);
            } else {
                $query = $this
                    ->ModelLog
                    ->where('deleted_at', NULL);
            }
            //
            foreach ($parameter as $key => $value) {
                if ($key == 'id') {
                    $query = $query->where($key, $value);
                } else {
                    $query = $query->like($key, $value);
                }
            }

            $dbResponse = $query
                ->orderBy('id', 'desc')
                ->paginate($limit, 'paginator', $page);

            // Paginação
            $pager = \Config\Services::pager();
            $paginationLinks = $pager->makeLinks($page, $limit, $pager->getTotal('paginator'), 'default_full');
            $linksArray = $this->pagination->extractPaginationLinks($paginationLinks);
            //
            // myPrint($dbResponse, 'C:\Users\Habilidade.Com\AppData\Roaming\Code\User\snippets\php.json', true);
            $response = array(
                'dbResponse' => $dbResponse,
                'linksArray' => $linksArray
            );
            #
        } catch (\Exception $e) {
            if (DEBUG_MY_PRINT) {
                myPrint($e->getMessage(), 'src\app\Controllers\ExempleDbController.php');
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
    public function dbUpdate($key, $parameter = NULL)
    {
        if (
            !isset($parameter['deleted_at'])
            && empty($parameter['deleted_at'])
            && count($parameter) == 1
        ) {
            $this->ModelLog->dbUpdate($key, $parameter);
        } else {
            $this->ModelLog->dbUpdate($key, $this->dbFields($parameter));
        }
        try {
            $affectedRows = $this->ModelLog->affectedRows();
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
                // myPrint($e->getMessage(), 'src\app\Controllers\ExempleDbController.php');
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
            $this->ModelLog->dbDelete('id', $parameter);
            $affectedRows = $this->ModelLog->affectedRows();
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
                // myPrint($e->getMessage(), 'src\app\Controllers\ExempleDbController.php');
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
                    ->ModelLog
                    ->where('id', $parameter)
                    ->where('deleted_at !=', NULL)
                    ->orderBy('id', 'DESC')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelLog
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
                // myPrint($e->getMessage(), 'src\app\Controllers\ExempleDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', $parameter, 5);
            $response = array();
        }
        return $response;
    }
}

?>