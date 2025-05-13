<?php

namespace App\Controllers;

// use App\Models\UploadModel;
use App\Controllers\SystemBaseController;
use App\Controllers\SystemMessageController;
use App\Models\CadastrosModels;
use App\Models\VCadastroProfissionalModels;
use Exception;

class ProfissionalDbController extends BaseController
{
    // private $ModelUpload;
    private $ModelsVCadastroProfissional;
    private $ModelCadastros;
    private $pagination;
    private $message;
    private $uri;

    public function __construct()
    {
        // $this->ModelUpload = new UploadModel();
        $this->ModelsVCadastroProfissional = new VCadastroProfissionalModels();
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

    # use App\Controllers\FuncionarioDbController;;
    # private $DbController;
    # $this->DbController = new FuncionarioDbController();
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
        (isset($processRequestFields['ProgramaId'])) ? ($dbCreate['programas_id'] = $processRequestFields['ProgramaId']) : (NULL);
        (isset($processRequestFields['CargoFuncaoId'])) ? ($dbCreate['cargo_funcao_id'] = $processRequestFields['CargoFuncaoId']) : (NULL);
        (isset($processRequestFields['AcessoId'])) ? ($dbCreate['acesso_id'] = $processRequestFields['AcessoId']) : (NULL);
        (isset($processRequestFields['PerfilId'])) ? ($dbCreate['perfil_id'] = $processRequestFields['PerfilId']) : (NULL);
        (isset($processRequestFields['SexoId'])) ? ($dbCreate['sexo_biologico_id'] = $processRequestFields['SexoId']) : (NULL);
        (isset($processRequestFields['UnidadeId'])) ? ($dbCreate['unidade_id'] = $processRequestFields['UnidadeId']) : (NULL);
        (isset($processRequestFields['CodProfissao'])) ? ($dbCreate['profissao_id'] = $processRequestFields['CodProfissao']) : (NULL);
        // myPrint('$dbCreate :: ', $dbCreate);
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
        $autoColumn = $this->ModelsVCadastroProfissional->getColumnsFromTable();
        // myPrint($autoColumn, '', true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                // myPrint($value_autoColumn, '', true);
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        (isset($processRequestFields['filtroProfissional'])) ? ($dbCreate['Nome'] = $processRequestFields['filtroProfissional']) : (NULL);
        (isset($processRequestFields['sexo_biologico_id'])) ? ($dbCreate['SexoId'] = $processRequestFields['sexo_biologico_id']) : (NULL);
        (isset($processRequestFields['genero_identidade_id'])) ? ($dbCreate['GeneroIdentidadeId'] = $processRequestFields['genero_identidade_id']) : (NULL);
        (isset($processRequestFields['municipio_id'])) ? ($dbCreate['MunicipioId'] = $processRequestFields['municipio_id']) : (NULL);
        (isset($processRequestFields['unidade_id'])) ? ($dbCreate['UnidadeId'] = $processRequestFields['unidade_id']) : (NULL);
        (isset($processRequestFields['profissao_id'])) ? ($dbCreate['CodProfissao'] = $processRequestFields['profissao_id']) : (NULL);
        (isset($processRequestFields['Descricao'])) ? ($dbCreate['ProfissaoDescricao'] = $processRequestFields['Descricao']) : (NULL);
        // myPrint($dbCreate, 'src\app\Controllers\ExempleDbController.php');
        return ($dbCreate);
    }

    # use App\Controllers\FuncionarioDbController;
    # $this->DbController = new FuncionarioDbController();
    # $this->DbController->dbCreate($parameter);
    public function dbCreate($parameter = NULL)
    {
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
        } catch (\Exception $e) {
            if (DEBUG_MY_PRINT) {
                myPrint($e->getMessage(), 'src\app\Controllers\ExempleDbController.php');
            }
            $message = $e->getMessage();
            $this->message->message([$message], 'danger', $parameter, 5);
            $response = array();
        }
        // myPrint($response, 'src\app\Controllers\ProfissionalDbController.php');
        return $response;
    }

    # use App\Controllers\FuncionarioDbController;
    # $this->DbController = new FuncionarioDbController();
    # $this->DbController->dbRead($parameter);
    public function dbRead($parameter = NULL, $page = 1, $limit = 10)
    {
        try {
            if ($parameter !== NULL) {
                $dbResponse = $this
                    ->ModelsVCadastroProfissional
                    ->where('id', $parameter)
                    ->where('deleted_at', NULL)
                    ->orderBy('id', 'DESC')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelsVCadastroProfissional
                    ->where('deleted_at', NULL)
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
                myPrint($e->getMessage(), 'src\app\Controllers\ProfissionalDbController.php');
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
        // $dt_inicio = isset($parameter['DataAdmissao']) ? $parameter['DataAdmissao'] : null;
        // $dt_fim = isset($parameter['DataDemissao']) ? $parameter['DataDemissao'] : null;
        // myPrint($page, 'src\app\Controllers\ProfissionalDbController.php', true);
        // myPrint($limit, 'src\app\Controllers\ProfissionalDbController.php', true);


        if (in_array('id', $parameter)) {
            return 'Possui ID';
        }

        $parameter = $this->dbFieldsFilter($parameter);
        $getURI = $this->uri->getSegments();

        try {
            if (in_array('id', array_keys($parameter))) {
                $limit = 1;
                $query = $this
                    ->ModelsVCadastroProfissional
                    ->where('AcessoId', 1)
                    ->where('deleted_at', NULL);
            } elseif (in_array('filtrar', $getURI)) {
                $query = $this
                    ->ModelsVCadastroProfissional
                    ->where('AcessoId', 1)
                    ->where('deleted_at', NULL);
            } elseif (in_array('filtrarlixo', $getURI)) {
                $query = $this
                    ->ModelsVCadastroProfissional
                    ->where('AcessoId', 1)
                    ->where('deleted_at !=', NULL);
            } else {
                $query = $this
                    ->ModelsVCadastroProfissional
                    ->where('AcessoId', 1)
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
            // myPrint($response, '');
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

    # use App\Controllers\FuncionarioDbController;
    # $this->DbController = new FuncionarioDbController();
    # $this->DbController->dbUpdate($parameter);
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

    # use App\Controllers\FuncionarioDbController;
    # $this->DbController = new FuncionarioDbController();
    # $this->DbController->dbDelete($parameter);
    public function dbDelete($parameter = NULL)
    {
        try {
            $this->ModelCadastros->dbDelete('id', $parameter);
            $affectedRows = $this->ModelCadastros->affectedRows();
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

    # use App\Controllers\FuncionarioDbController;
    # $this->DbController = new FuncionarioDbController();
    # $this->DbController->dbCleaner($parameter);
    public function dbCleaner($parameter = NULL, $page = 1)
    {
        $limit = 10;
        try {
            // exit('src\app\Controllers\ProfissionalDbController.php');
            if (isset($processRequest['id'])) {
                $dbResponse = $this
                    ->ModelsVCadastroProfissional
                    ->where('id', $processRequest['id'])
                    ->where('deleted_at !=', NULL)
                    ->orderBy('id', 'DESC')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } elseif ($parameter !== NULL) {
                $dbResponse = $this
                    ->ModelsVCadastroProfissional
                    ->where('id', $parameter)
                    ->where('deleted_at !=', NULL)
                    ->orderBy('id', 'DESC')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelsVCadastroProfissional
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
