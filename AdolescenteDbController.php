<?php

namespace App\Controllers;

// use App\Models\UploadModel;
use App\Controllers\SystemBaseController;
use App\Controllers\SystemMessageController;
use App\Models\VCadastroAdolescentesModels;
use App\Models\CadastrosModels;
use Exception;

class AdolescenteDbController extends BaseController
{
    // private $ModelUpload;
    private $ModelsVCadastroAdolescentes;
    private $ModelCadastros;
    private $pagination;
    private $message;
    private $uri;

    public function __construct()
    {
        // $this->ModelUpload = new UploadModel();
        $this->ModelsVCadastroAdolescentes = new VCadastroAdolescentesModels();
        $this->ModelCadastros = new CadastrosModels();
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
        // myPrint('$processRequestFields :: ', $processRequestFields, true);
        $dbCreate = array();
        $autoColumn = $this->ModelCadastros->getColumnsFromTable();
        // myPrint('$autoColumn :: ', $autoColumn, true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }

        (isset($processRequestFields['generoIdentidade'])) ? ($dbCreate['genero_identidade'] = $processRequestFields['generoIdentidade']) : (NULL);
        (isset($processRequestFields['AcessoCadastroID'])) ? ($dbCreate['acesso_id'] = $processRequestFields['AcessoCadastroID']) : (NULL);
        (isset($processRequestFields['ResponsavelID'])) ? ($dbCreate['cadastro_id'] = $processRequestFields['ResponsavelID']) : (NULL);
        (isset($processRequestFields['MunicipioId'])) ? ($dbCreate['municipio_id'] = $processRequestFields['MunicipioId']) : (NULL);
        (isset($processRequestFields['SexoId'])) ? ($dbCreate['sexo_biologico_id'] = $processRequestFields['SexoId']) : (NULL);
        (isset($processRequestFields['UnidadeId'])) ? ($dbCreate['unidade_id'] = $processRequestFields['UnidadeId']) : (NULL);
        (isset($processRequestFields['NumRegistro'])) ? ($dbCreate['NumRegistro'] = $processRequestFields['NumRegistro']) : (NULL);
        (isset($processRequestFields['perfil_id'])) ? ($dbCreate['perfil_id'] = $processRequestFields['perfil_id']) : (NULL);
        // myPrint('$dbCreate :: ', $dbCreate);
        return ($dbCreate);
    }

    # use App\Controllers\SystemUploadDbController;
    # private $DbController;
    # $this->DbController = new SystemUploadDbController();
    # $this->DbController->dbFields($fileds = array();
    public function dbFieldsFilter($processRequestFields = array())
    {
        // myPrint('$processRequestFields :: ', $processRequestFields, true);
        $dbCreate = array();
        $autoColumn = $this->ModelsVCadastroAdolescentes->getColumnsFromTable();
        // myPrint('$autoColumn :: ', $autoColumn, true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        if ($dbCreate == array()) {
            // (isset($processRequestFields['filtroSelect'])) ? ($dbCreate['Nome'] = $processRequestFields['filtroSelect']) : (NULL);
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
            $this->ModelCadastros->dbCreate($this->dbFields($parameter));
            // myPrint($parameter, 'src\app\Controllers\AdolescenteDbController.php');
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
        } catch (\Exception $e) {
            if (DEBUG_MY_PRINT) {
                //myPrint($e->getMessage(), 'src\app\Controllers\ExempleDbController.php');
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
        // myPrint($parameter, 'src\app\Controllers\AdolescenteDbController.php', true);
        // myPrint($page, 'src\app\Controllers\AdolescenteDbController.php');
        try {
            if ($parameter !== NULL) {
                $dbResponse = $this
                    ->ModelsVCadastroAdolescentes
                    ->where('id', $parameter)
                    ->where('deleted_at', NULL)
                    ->orderBy('updated_at', 'DESC')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelsVCadastroAdolescentes
                    ->where('deleted_at', NULL)
                    ->orderBy('updated_at', 'DESC')
                    ->dBread()
                    ->paginate($limit, 'paginator', $page);
                //
            }
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
        $dt_inicio = isset($parameter['adolescente_data_cadastramento_inicio']) ? $parameter['adolescente_data_cadastramento_inicio'] : null;
        $dt_fim = isset($parameter['adolescente_data_cadastramento_fim']) ? $parameter['adolescente_data_cadastramento_fim'] : null;
        // myPrint($dt_inicio, $dt_fim, true);
        #
        if ($dt_inicio !== null && $dt_fim == null) {
            $parameter['DataCadastramento'] = $parameter['adolescente_data_cadastramento_inicio'];
        }
        $parameter = $this->dbFieldsFilter($parameter);
        #

        // myPrint($parameter, 'src\app\Controllers\AdolescenteDbController.php', true);

        try {
            if (in_array('id', array_keys($parameter))) {
                $query = $this
                    ->ModelsVCadastroAdolescentes
                    ->where('perfil_id', 1)
                    ->where('acesso_id', 2)
                    ->where('deleted_at', NULL);
            } elseif ($dt_inicio !== null && $dt_fim !== null) {
                $query = $this
                    ->ModelsVCadastroAdolescentes
                    ->where('deleted_at', NULL)
                    ->where('DataCadastramento >=', $dt_inicio)
                    ->where('DataCadastramento <=', $dt_fim);
            } else {
                $query = $this
                    ->ModelsVCadastroAdolescentes
                    ->where('perfil_id', 1)
                    ->where('acesso_id', 2)
                    ->where('deleted_at', NULL);
            }
            //
            foreach ($parameter as $key => $value) {
                $query = $query->like($key, $value);
                // myPrint($key, $value, true);
            }

            $dbResponse = $query
                ->orderBy('updated_at', 'DESC')
                ->paginate($limit, 'paginator', $page);

            // Paginação
            $pager = \Config\Services::pager();
            $paginationLinks = $pager->makeLinks($page, $limit, $pager->getTotal('paginator'), 'default_full');
            $linksArray = $this->pagination->extractPaginationLinks($paginationLinks);
            //
            // myPrint($dbResponse, 'src\app\Controllers\AdolescenteDbController.php');
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
        // myPrint('$response :: ', $response);
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
                $this->ModelCadastros->dbUpdate($key, $parameter);
            } else {
                $this->ModelCadastros->dbUpdate($key, $this->dbFields($parameter));
                $ver = $this->dbFields($parameter);
                // myPrint($key, 'src\app\Controllers\AdolescenteDbController.php', true);
                // myPrint($ver, 'src\app\Controllers\AdolescenteDbController.php', true);
            }
            #
            $affectedRows = $this->ModelCadastros->affectedRows();
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
            // myPrint($response, 'src\app\Controllers\AdolescenteDbController.php');
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
            $this->ModelCadastros->dbDelete('id', $parameter);
            $affectedRows = $this->ModelsVCadastroAdolescentes->affectedRows();
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
                    ->ModelsVCadastroAdolescentes
                    ->where('id', $parameter)
                    ->where('deleted_at !=', NULL)
                    ->orderBy('id', 'DESC')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelsVCadastroAdolescentes
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
