<?php

namespace App\Controllers;

// use App\Models\UploadModel;
use App\Controllers\SystemBaseController;
use App\Controllers\SystemMessageController;
use App\Models\VCadGeralModels;
use App\Models\CadastrosModels;
use Exception;

class ResponsavelDbController extends BaseController
{
    // private $ModelUpload;
    private $ModelVCadGeral;
    private $ModelCadastros;
    private $pagination;
    private $message;
    private $uri;

    public function __construct()
    {
        // $this->ModelUpload = new UploadModel();
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
        $this->ModelVCadGeral = new VCadGeralModels();
        $this->ModelCadastros = new CadastrosModels();
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
        // myPrint($processRequestFields, 'src\app\Controllers\SystemUploadDbController.php, linha 46', true);
        $dbCreate = array();
        $autoColumn = $this->ModelCadastros->getColumnsFromTable();
        // myPrint($autoColumn, '', true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        (isset($processRequestFields['acesso_id'])) ? ($dbCreate['acesso_id'] = $processRequestFields['acesso_id']) : (NULL);
        (isset($processRequestFields['perfil_id'])) ? ($dbCreate['perfil_id'] = $processRequestFields['perfil_id']) : (NULL);
        (isset($processRequestFields['Responsavel_Nome'])) ? ($dbCreate['Nome'] = $processRequestFields['Responsavel_Nome']) : (NULL);
        (isset($processRequestFields['Responsavel_CPF'])) ? ($dbCreate['CPF'] = $processRequestFields['Responsavel_CPF']) : (NULL);
        (isset($processRequestFields['Responsavel_Email'])) ? ($dbCreate['Email'] = $processRequestFields['Responsavel_Email']) : (NULL);
        (isset($processRequestFields['Responsavel_TelefoneMovel'])) ? ($dbCreate['TelefoneMovel'] = $processRequestFields['Responsavel_TelefoneMovel']) : (NULL);
        (isset($processRequestFields['Responsavel_TelefoneFixo'])) ? ($dbCreate['TelefoneFixo'] = $processRequestFields['Responsavel_TelefoneFixo']) : (NULL);
        // myPrint($dbCreate, 'src\app\Controllers\ResponsavelDbController.php');
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
        $autoColumn = $this->ModelVCadGeral->getColumnsFromTable();

        // myPrint('$autoColumn :: ', $autoColumn, true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        (isset($processRequestFields['filterResponsavel'])) ? ($dbCreate['Nome'] = $processRequestFields['filterResponsavel']) : (NULL);
        if (isset($processRequestFields['ResponsavelID'])) {
            (isset($processRequestFields['ResponsavelID'])) ? ($dbCreate['id'] = $processRequestFields['ResponsavelID']) : (NULL);
            unset($dbCreate['ResponsavelID']);
        }
        // myPrint('$dbCreate :: ', $dbCreate);
        return ($dbCreate);
    }

    # use App\Controllers\TokenCsrfController;
    # $this->DbController = new ExempleDbController();
    # $this->DbController->dbCreate($parameter);
    public function dbCreate($parameter = NULL)
    {
        // myPrint($parameter, 'src\app\Controllers\ResponsavelDbController.php');
        $id_adolescente = (isset($parameter['id_adolescente'])) ? ($parameter['id_adolescente']) : (false);
        #
        try {
            $this->ModelCadastros->dbCreate($this->dbFields($parameter));
            $affectedRows = $this->ModelCadastros->affectedRows();
            if ($affectedRows > 0) {
                $dbCreate['insertID'] = $this->ModelCadastros->insertID();
                $dbCreate['affectedRows'] = $affectedRows;
                $dbCreate['dbCreate'] = $parameter;
            } else {
                $dbCreate['insertID'] = NULL;
                $dbCreate['affectedRows'] = $affectedRows;
                $dbCreate['dbCreate'] = $parameter;
            }
            $response = $dbCreate;
            if ($id_adolescente && $dbCreate['insertID'] !== null) {
                $dbUpdateAdolescente = array(
                    'cadastro_id' => $dbCreate['insertID'],
                );
                $this->ModelCadastros->dbUpdate($id_adolescente, $dbUpdateAdolescente);
            }
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
    public function dbRead($parameter = NULL, $page = 1)
    {
        $limit = 10;
        try {if ($parameter !== NULL) {
                $dbResponse = $this
                    ->ModelVCadGeral
                    ->where('id', $parameter)
                    ->where('CadastroId', NULL)
                    ->where('perfil_id', 6)
                    ->where('deleted_at', NULL)
                    ->orderBy('updated_at', 'asc')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelVCadGeral
                    ->where('perfil_id', 6)
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
    public function dbFilter($parameter = NULL, $page = 1, $limit = 10)
    {
        $parameter = $this->dbFieldsFilter($parameter);
        $getURI = $this->uri->getSegments();
        try {
            if (in_array('filtrar', $getURI)) {
                $limit = 200;
                $query = $this
                    ->ModelVCadGeral
                    // ->where('perfil_id', 3)
                    // ->where('acesso_Id', 2)
                    ->where('deleted_at', NULL);
            } elseif (in_array('filtrarlixo', $getURI)) {
                $limit = 200;
                $query = $this
                    ->ModelVCadGeral
                    // ->where('perfil_id', 3)
                    // ->where('acesso_Id', 2)
                    ->where('deleted_at !=', NULL);
            } else {
                $query = $this
                    ->ModelVCadGeral
                    // ->where('perfil_id', 3)
                    // ->where('acesso_Id', 2)
                    ->where('deleted_at', NULL);
            }
            // myPrint(in_array('id', array_keys($parameter)), 'src\app\Controllers\ResponsavelDbController.php');
            if (in_array('id', array_keys($parameter))) {
                foreach ($parameter as $key => $value) {
                    // myPrint($key, $value);
                    $query = $query->where($key, $value);
                }
            } else {
                // myPrint($parameter, 'src\app\Controllers\ResponsavelDbController.php');
                foreach ($parameter as $key => $value) {
                    // myPrint($key, $value);
                    $query = $query->like($key, $value);
                }
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
        // myPrint('$dbResponse :: ', $dbResponse);
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
            $this->ModelCadastros->dbUpdate($key, $parameter);
        } else {
            $this->ModelCadastros->dbUpdate($key, $this->dbFields($parameter));
        }
        try {
            $affectedRows = $this->ModelCadastros->affectedRows();
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
            // myPrint($response, 'src\app\Controllers\ResponsavelDbController.php');
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
        $dbUpdate = array(
            'id' => $parameter,
            'deleted_at' => date('Y-m-d H:i:m')
        );
        try {
            $this->ModelVCadGeral->dbUpdate($parameter, $dbUpdate);
            $affectedRows = $this->ModelVCadGeral->affectedRows();
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
            $this->ModelVCadGeral->dBdelete($parameter);
            $affectedRows = $this->ModelVCadGeral->affectedRows();
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
