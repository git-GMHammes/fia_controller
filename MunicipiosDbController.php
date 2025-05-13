<?php

namespace App\Controllers;

// use App\Models\UploadModel;
use App\Controllers\SystemMessageController;
use App\Controllers\SystemBaseController;
use App\Models\MunicipiosModels;

use Exception;

class MunicipiosDbController extends BaseController
{
    // private $ModelUpload;
    private $ModelMunicipios;
    private $pagination;
    private $message;
    private $uri;

    public function __construct()
    {
        // $this->ModelUpload = new UploadModel();
        $this->ModelMunicipios = new MunicipiosModels();
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
        $this->message = new SystemMessageController();
        $this->pagination = new SystemBaseController();
    }

    // route POST /www/sigla/rota
    // route GET /www/sigla/rota
    // Informação sobre o controller
    // retorno do controller [JSON]
    public function index()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }

    // use App\Controllers\SystemUploadDbController;
    // private $DbController;
    // $this->DbController = new SystemUploadDbController();
    // $this->DbController->dbFields($fileds = array();
    public function dbFields($processRequestFields = array())
    {
        $dbCreate = array();
        $autoColumn = $this->ModelMunicipios->getColumnsFromTable();
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                // myPrint($value_autoColumn, '', true);
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        if ($dbCreate == array()) {
            (isset($processRequestFields['id'])) ? ($dbCreate['id'] = $processRequestFields['id']) : (NULL);
            (isset($processRequestFields['id_municipio'])) ? ($dbCreate['id_municipio'] = $processRequestFields['id_municipio']) : (NULL);
            (isset($processRequestFields['nome_municipio'])) ? ($dbCreate['nome_municipio'] = $processRequestFields['nome_municipio']) : (NULL);
            (isset($processRequestFields['id_regiao'])) ? ($dbCreate['id_regiao'] = $processRequestFields['id_regiao']) : (NULL);
            (isset($processRequestFields['nome_regiao'])) ? ($dbCreate['nome_regiao'] = $processRequestFields['nome_regiao']) : (NULL);
            (isset($processRequestFields['id_mesoregiao'])) ? ($dbCreate['id_mesoregiao'] = $processRequestFields['id_mesoregiao']) : (NULL);
            (isset($processRequestFields['nome_mesoregiao'])) ? ($dbCreate['nome_mesoregiao'] = $processRequestFields['nome_mesoregiao']) : (NULL);
            (isset($processRequestFields['id_uf'])) ? ($dbCreate['id_uf'] = $processRequestFields['id_uf']) : (NULL);
            (isset($processRequestFields['nome_uf'])) ? ($dbCreate['nome_uf'] = $processRequestFields['nome_uf']) : (NULL);
        }
        // myPrint($dbCreate, 'src\app\Controllers\ExempleDbController.php');
        return ($dbCreate);
    }

    // use App\Controllers\SystemUploadDbController;
    // private $DbController;
    // $this->DbController = new SystemUploadDbController();
    // $this->DbController->dbFields($fileds = array();
    public function dbFieldsFilter($processRequestFields = array())
    {
        // myPrint($processRequestFields, 'src\app\Controllers\SystemUploadDbController.php', true);
        $dbCreate = array();
        $autoColumn = $this->ModelMunicipios->getColumnsFromTable();
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

    // use App\Controllers\TokenCsrfController;
    // $this->DbController = new ExempleDbController();
    // $this->DbController->dbCreate($parameter);
    public function dbCreate($parameter = NULL)
    {
        try {
            $this->ModelMunicipios->dbCreate($this->dbFields($parameter));
            $affectedRows = $this->ModelMunicipios->affectedRows();
            if ($affectedRows > 0) {
                $dbCreate['insertID'] = $this->ModelMunicipios->insertID();
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

    // route POST /www/sigla/rota
    // route GET /www/sigla/rota
    // Informação sobre o controller
    // retorno do controller [JSON]
    public function dbRead($parameter = NULL, $page = 1, $limit = 10)
    {
        // Parâmentros para receber um POST
        $request = service('request');
        $processRequest = (array) $request->getVar();
        $getURI = $this->uri->getSegments();
        try {
            if (isset($processRequest['id'])) {
                $dbResponse = $this
                    ->ModelMunicipios
                    ->where('id', $processRequest['id'])
                    ->where('deleted_at', NULL)
                    ->orderBy('nome_municipio', 'asc')
                    ->dBread()
                    ->paginate($limit, 'paginator', $page);
                //
            } elseif ($parameter !== NULL) {
                $dbResponse = $this
                    ->ModelMunicipios
                    ->where('id', $parameter)
                    ->where('deleted_at', NULL)
                    ->orderBy('nome_municipio', 'asc')
                    ->dBread()
                    ->paginate($limit, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelMunicipios
                    ->where('deleted_at', NULL)
                    ->orderBy('nome_municipio', 'asc')
                    ->dBread()
                    ->paginate($limit, 'paginator', $page);
                //
            };
            //
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
                myPrint($e->getMessage(), 'src\app\Controllers\ExempleDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', $parameter, 5);
            $response = array();
        }
        return $response;
    }

    // route POST /www/sigla/rota
    // route GET /www/sigla/rota
    // Informação sobre o controller
    // retorno do controller [JSON]
    public function dbFilter($parameter = NULL, $page = 1, $limit = 10)
    {
        $parameter = $this->dbFieldsFilter($parameter);
        $getURI = $this->uri->getSegments();
        if (in_array('filtrar', $getURI)) {
            $limit = 200;
        }
        try {
            $query = $this
                ->ModelMunicipios
                ->where('deleted_at', NULL);
            foreach ($parameter as $key => $value) {
                $query = $query->like($key, $value);
            }

            $dbResponse = $query
                ->orderBy('id', 'nome_municipio')
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

    // route POST /www/sigla/rota
    // route GET /www/sigla/rota
    // Informação sobre o controller
    // retorno do controller [JSON]
    public function dbUpdate($key, $parameter = NULL)
    {
        if (
            !isset($parameter['deleted_at'])
            && empty($parameter['deleted_at'])
            && count($parameter) == 1
        ) {
            $this->ModelMunicipios->dbUpdate($key, $parameter);
        } else {
            $this->ModelMunicipios->dbUpdate($key, $this->dbFields($parameter));
        }
        try {
            $affectedRows = $this->ModelMunicipios->affectedRows();
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

    // route POST /www/sigla/rota
    // route GET /www/sigla/rota
    // Informação sobre o controller
    // retorno do controller [JSON]
    public function dbDelete($parameter = NULL)
    {
        $dbUpdate = array(
            'id' => $parameter,
            'deleted_at' => date('Y-m-d H:i:m')
        );
        try {
            $this->ModelMunicipios->dbUpdate($parameter, $dbUpdate);
            $affectedRows = $this->ModelMunicipios->affectedRows();
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

    // route POST /www/sigla/rota
    // route GET /www/sigla/rota
    // Informação sobre o controller
    // retorno do controller [JSON]
    public function dbCleaner($parameter = NULL, $page = 1)
    {
        $limit = 10;
        try {
            $this->ModelMunicipios->dBdelete('id', $parameter);
            $affectedRows = $this->ModelMunicipios->affectedRows();
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
                myPrint($e->getMessage(), 'src\app\Controllers\ExempleDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', $parameter, 5);
            $response = array();
        }
        return $response;
    }
}
