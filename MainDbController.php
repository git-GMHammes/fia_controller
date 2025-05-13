<?php

namespace App\Controllers;

// use App\Models\UploadModel;
use App\Controllers\SystemBaseController;
use App\Controllers\SystemMessageController;
use App\Models\VQtdCadastroAdolescentesModels;
use App\Models\VQtdCadastroProfissionaisModels;
use App\Models\VQtdUnidadesModels;
use App\Models\VQtdVagastotalModels;
use Exception;

class MainDbController extends BaseController
{
    // private $ModelUpload;
    private $ModelsVQtdCadastroAdolescentes;
    private $ModelsVQtdCadastroProfissionais;
    private $ModelsVQtdUnidades;
    private $ModelsVQtdVagasTotal;
    private $pagination;
    private $message;
    private $uri;

    public function __construct()
    {
        // $this->ModelUpload = new UploadModel();
        $this->ModelsVQtdCadastroAdolescentes = new VQtdCadastroAdolescentesModels();
        $this->ModelsVQtdCadastroProfissionais = new VQtdCadastroProfissionaisModels();
        $this->ModelsVQtdVagasTotal = new VQtdVagastotalModels();
        $this->ModelsVQtdUnidades = new VQtdUnidadesModels();
        $this->pagination = new SystemBaseController();
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
    public function dbFieldsFilter($processRequestFields = array())
    {
        // myPrint($processRequestFields, 'src\app\Controllers\SystemUploadDbController.php', true);
        $dbCreate = array();
        $autoColumn = $this->ModelsVQtdUnidades->getColumnsFromTable();
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                // myPrint($value_autoColumn, '', true);
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        if ($dbCreate == array()) {
            (isset($processRequestFields['Modelo'])) ? ($dbCreate['Modelobanco'] = $processRequestFields['Modelo']) : (NULL);
        }
        // myPrint($dbCreate, 'src\app\Controllers\ExempleDbController.php');
        return ($dbCreate);
    }

    # use App\Controllers\SystemUploadDbController;
    # private $DbController;
    # $this->DbController = new SystemUploadDbController();
    # $this->DbController->dbFields($fileds = array();
    public function dbFields($processRequestFields = array())
    {
        // myPrint($processRequestFields, 'src\app\Controllers\SystemUploadDbController.php', true);
        $dbCreate = array();
        $autoColumn = $this->ModelsVQtdUnidades->getColumnsFromTable();
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                // myPrint($value_autoColumn, '', true);
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        if ($dbCreate == array()) {
            (isset($processRequestFields['Modelo'])) ? ($dbCreate['Modelobanco'] = $processRequestFields['Modelo']) : (NULL);
        }
        // myPrint($dbCreate, 'src\app\Controllers\ExempleDbController.php');
        return ($dbCreate);
    }

    # use App\Controllers\TokenCsrfController;
    # $this->DbController = new ExempleDbController();
    # $this->DbController->dbCreate($parameter);
    public function dbCreate($parameter = NULL)
    {
        try {
            $this->ModelsVQtdUnidades->dbCreate($this->dbFields($parameter));
            $affectedRows = $this->ModelsVQtdUnidades->affectedRows();
            if ($affectedRows > 0) {
                $dbCreate['insertID'] = $this->ModelsVQtdUnidades->insertID();
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
    public function dbRead()
    {
        try {
            $dbResponse['qtd_adolescente'] = $this
            ->ModelsVQtdCadastroAdolescentes
            ->dBread()
            ->findAll();
            //
            $dbResponse['qtd_profissionais'] = $this
            ->ModelsVQtdCadastroProfissionais
            ->dBread()
            ->findAll();
            //
            $dbResponse['qtd_unidades'] = $this
            ->ModelsVQtdUnidades
            ->dBread()
            ->findAll();
            //
            $dbResponse['qtd_vagas'] = $this
            ->ModelsVQtdVagasTotal
            ->dBread()
            ->findAll();
            $response = $dbResponse;
        } catch (\Exception $e) {
            if (DEBUG_MY_PRINT) {
                myPrint($e->getMessage(), 'src\app\Controllers\ExempleDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', array(), 5);
            $response = array();
        }
        // myPrint($response, 'src\app\Controllers\MainDbController.php');
        return $response;
    }

    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [JSON]
    public function dbUpdate($key, $parameter = NULL)
    {
        try {
            $this->ModelsVQtdUnidades->dbUpdate($key, $this->dbFields($parameter));
            $affectedRows = $this->ModelsVQtdUnidades->affectedRows();
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
        $dbUpdate = array(
            'id' => $parameter,
            'deleted_at' => date('Y-m-d H:i:m')
        );
        try {
            $this->ModelsVQtdUnidades->dbUpdate($parameter, $dbUpdate);
            $affectedRows = $this->ModelsVQtdUnidades->affectedRows();
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
            $this->ModelsVQtdUnidades->dBdelete('id', $parameter);
            $affectedRows = $this->ModelsVQtdUnidades->affectedRows();
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
