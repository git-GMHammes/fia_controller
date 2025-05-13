<?php

namespace App\Controllers;

// use App\Models\UploadModel;
use App\Controllers\SystemBaseController;
use App\Controllers\SystemMessageController;
//App\Models\CadastrosModels;
//App\Models\VCadastroAdolescentesModels;
use Exception;

class ExampleDbController extends BaseController
{
    // private $ModelUpload;
    private $ModelTabelaPrincipal;
    private $ModelVTabelaPrincipal;
    private $pagination;
    private $message;
    private $uri;

    // $this->pagination = new SystemBaseController();
    // $linksArray = $this->pagination->extractPaginationLinks($paginationLinks);

    public function __construct()
    {
        //$this->ModelUpload = new UploadModel();
        //$this->ModelTabelaPrincipal = new TabelaPrincipalModels();
        //$this->ModelVTabelaPrincipal = new VTabelaPrincipalModelsModels();
        $this->pagination = new SystemBaseController();
        $this->message = new SystemMessageController();
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
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
        // myPrint($processRequestFields, 'src\app\Controllers\SystemUploadDbController.php', true);
        $dbCreate = array();
        $autoColumn = $this->ModelVTabelaPrincipal->getColumnsFromTable();
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                // myPrint($value_autoColumn, '', true);
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        if ($dbCreate == array()) {
            (isset($processRequestFields['id'])) ? ($dbCreate['id'] = $processRequestFields['id']) : (NULL);
            (isset($processRequestFields['unidade_id'])) ? ($dbCreate['unidade_id'] = $processRequestFields['unidade_id']) : (NULL);
            (isset($processRequestFields['NMatriculaCertidao'])) ? ($dbCreate['NMatriculaCertidao'] = $processRequestFields['NMatriculaCertidao']) : (NULL);
            (isset($processRequestFields['CPFAdolescente'])) ? ($dbCreate['CPFAdolescente'] = $processRequestFields['CPFAdolescente']) : (NULL);
            (isset($processRequestFields['DataNascimentoAdolescente'])) ? ($dbCreate['DataNascimentoAdolescente'] = $processRequestFields['DataNascimentoAdolescente']) : (NULL);
            (isset($processRequestFields['NomeAdolescente'])) ? ($dbCreate['NomeAdolescente'] = $processRequestFields['NomeAdolescente']) : (NULL);
            (isset($processRequestFields['Enderecodolescente'])) ? ($dbCreate['Enderecodolescente'] = $processRequestFields['Enderecodolescente']) : (NULL);
            (isset($processRequestFields['TelefoneAdolescente'])) ? ($dbCreate['TelefoneAdolescente'] = $processRequestFields['TelefoneAdolescente']) : (NULL);
            (isset($processRequestFields['Certidao'])) ? ($dbCreate['Certidao'] = $processRequestFields['Certidao']) : (NULL);
            (isset($processRequestFields['NumRegistro'])) ? ($dbCreate['NumRegistro'] = $processRequestFields['NumRegistro']) : (NULL);
            (isset($processRequestFields['Folha'])) ? ($dbCreate['Folha'] = $processRequestFields['Folha']) : (NULL);
            (isset($processRequestFields['Livro'])) ? ($dbCreate['Livro'] = $processRequestFields['Livro']) : (NULL);
            (isset($processRequestFields['Circunscricao'])) ? ($dbCreate['Circunscricao'] = $processRequestFields['Circunscricao']) : (NULL);
            (isset($processRequestFields['Zona'])) ? ($dbCreate['Zona'] = $processRequestFields['Zona']) : (NULL);
            (isset($processRequestFields['UFRegistro'])) ? ($dbCreate['UFRegistro'] = $processRequestFields['UFRegistro']) : (NULL);
            (isset($processRequestFields['BairroAdokescente'])) ? ($dbCreate['BairroAdokescente'] = $processRequestFields['BairroAdokescente']) : (NULL);
            (isset($processRequestFields['SexoAdolescente'])) ? ($dbCreate['SexoAdolescente'] = $processRequestFields['SexoAdolescente']) : (NULL);
            (isset($processRequestFields['IdentGeneroAdolescente'])) ? ($dbCreate['IdentGeneroAdolescente'] = $processRequestFields['IdentGeneroAdolescente']) : (NULL);
            (isset($processRequestFields['CorRacaEtniaAdolescente'])) ? ($dbCreate['CorRacaEtniaAdolescente'] = $processRequestFields['CorRacaEtniaAdolescente']) : (NULL);
            (isset($processRequestFields['NomeResponsavelAdolescente'])) ? ($dbCreate['NomeResponsavelAdolescente'] = $processRequestFields['NomeResponsavelAdolescente']) : (NULL);
            (isset($processRequestFields['TelResponsavel'])) ? ($dbCreate['TelResponsavel'] = $processRequestFields['TelResponsavel']) : (NULL);
            (isset($processRequestFields['CPFResponsavel'])) ? ($dbCreate['CPFResponsavel'] = $processRequestFields['CPFResponsavel']) : (NULL);
            (isset($processRequestFields['TipoEscolaAdolescente'])) ? ($dbCreate['TipoEscolaAdolescente'] = $processRequestFields['TipoEscolaAdolescente']) : (NULL);
            (isset($processRequestFields['EscolaridadeAdolescente'])) ? ($dbCreate['EscolaridadeAdolescente'] = $processRequestFields['EscolaridadeAdolescente']) : (NULL);
            (isset($processRequestFields['TurnoEscolarAdolesc'])) ? ($dbCreate['TurnoEscolarAdolesc'] = $processRequestFields['TurnoEscolarAdolesc']) : (NULL);
            (isset($processRequestFields['NomeEscola'])) ? ($dbCreate['NomeEscola'] = $processRequestFields['NomeEscola']) : (NULL);
            (isset($processRequestFields['DataCadastramento'])) ? ($dbCreate['DataCadastramento'] = $processRequestFields['DataCadastramento']) : (NULL);
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
        $autoColumn = $this->ModelTabelaPrincipal->getColumnsFromTable();
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
            $this->ModelVTabelaPrincipal->dbCreate($this->dbFields($parameter));
            $affectedRows = $this->ModelVTabelaPrincipal->affectedRows();
            if ($affectedRows > 0) {
                $dbCreate['insertID'] = $this->ModelVTabelaPrincipal->insertID();
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
    public function dbRead($parameter = NULL, $page = 1)
    {
        $limit = 10;
        try {
            if (isset($processRequest['id'])) {
                $dbResponse = $this
                    ->ModelTabelaPrincipal
                    ->where('id', $processRequest['id'])
                    ->where('deleted_at', NULL)
                    ->orderBy('id', 'DESC')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } elseif ($parameter !== NULL) {
                $dbResponse = $this
                    ->ModelTabelaPrincipal
                    ->where('id', $parameter)
                    ->where('deleted_at', NULL)
                    ->orderBy('id', 'DESC')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelTabelaPrincipal
                    ->where('deleted_at', NULL)
                    ->orderBy('id', 'DESC')
                    ->dBread()
                    ->paginate($limit, 'paginator', $page);
                //
            };
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
        //
        try {
            $query = $this
                ->ModelTabelaPrincipal
                ->where('perfil_id', 5)
                ->where('acesso_Id', 2)
                ->where('deleted_at', NULL);
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

    // route POST /www/sigla/rota
    // route GET /www/sigla/rota
    // Informação sobre o controller
    // retorno do controller [JSON]
    public function dbUpdate($key, $parameter = NULL)
    {
        try {
            if (
                !isset($parameter['deleted_at'])
                && empty($parameter['deleted_at'])
                && count($parameter) == 1
            ) {
                $this->ModelVTabelaPrincipal->dbUpdate($key, $parameter);
            } else {
                $this->ModelVTabelaPrincipal->dbUpdate($key, $this->dbFields($parameter));
            }
            $affectedRows = $this->ModelVTabelaPrincipal->affectedRows();
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
        try {
            $this->ModelVTabelaPrincipal->dbDelete('id', $parameter);
            $affectedRows = $this->ModelVTabelaPrincipal->affectedRows();
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
            // exit('src\app\Controllers\AdolescenteDbController.php');
            if (isset($processRequest['id'])) {
                $dbResponse = $this
                    ->ModelTabelaPrincipal
                    ->where('id', $processRequest['id'])
                    ->where('deleted_at !=', NULL)
                    ->orderBy('id', 'DESC')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } elseif ($parameter !== NULL) {
                $dbResponse = $this
                    ->ModelTabelaPrincipal
                    ->where('id', $parameter)
                    ->where('deleted_at !=', NULL)
                    ->orderBy('id', 'DESC')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelTabelaPrincipal
                    ->where('deleted_at !=', NULL)
                    ->orderBy('id', 'DESC')
                    ->dBread()
                    ->paginate($limit, 'paginator', $page);
                //
            };
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
