<?php

namespace App\Controllers;

// use App\Models\UploadModel;
use App\Controllers\SystemMessageController;
use App\Controllers\SystemBaseController;
use App\Models\VCadastroProntuarioPSModels;
use App\Models\ProntuarioPsicoSocialModels;
use Exception;

class ProntuarioPSDbController extends BaseController
{
    // private $ModelUpload;
    private $ModelsVCadastroProntuarioPS;
    private $ModelsProntuarioPsicoSocial;
    private $pagination;
    private $message;
    private $uri;

    public function __construct()
    {
        // $this->ModelUpload = new UploadModel();
        $this->ModelsVCadastroProntuarioPS = new VCadastroProntuarioPSModels();
        $this->ModelsProntuarioPsicoSocial = new ProntuarioPsicoSocialModels();
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
        $autoColumn = $this->ModelsProntuarioPsicoSocial->getColumnsFromTable();
        // myPrint($autoColumn, '');
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                // myPrint($value_autoColumn, '', true);
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        (isset($processRequestFields['id'])) ? ($dbCreate['id'] = $processRequestFields['id']) : (NULL);
        (isset($processRequestFields['profissional_id'])) ? ($dbCreate['profissional_id'] = $processRequestFields['profissional_id']) : (NULL);
        (isset($processRequestFields['adolescente_id'])) ? ($dbCreate['adolescente_id'] = $processRequestFields['adolescente_id']) : (NULL);
        (isset($processRequestFields['prontuario_MedidasSocioEducativas'])) ? ($dbCreate['MedidasSocioEducativas'] = $processRequestFields['prontuario_MedidasSocioEducativas']) : NULL;
        (isset($processRequestFields['prontuario_UsodeDrogas'])) ? ($dbCreate['UsodeDrogas'] = $processRequestFields['prontuario_UsodeDrogas']) : NULL;
        (isset($processRequestFields['prontuario_Deficiencia'])) ? ($dbCreate['Deficiencia'] = $processRequestFields['prontuario_Deficiencia']) : NULL;
        (isset($processRequestFields['prontuario_necessita_mediador'])) ? ($dbCreate['NecesMediador'] = $processRequestFields['prontuario_necessita_mediador']) : NULL;
        (isset($processRequestFields['prontuario_cad_unico'])) ? ($dbCreate['CadUnico'] = $processRequestFields['prontuario_cad_unico']) : NULL;
        // (isset($processRequestFields['prontuario_referenciado_na_rede'])) ? ($dbCreate['ReferenciadoNaRede'] = $processRequestFields['prontuario_referenciado_na_rede']) : NULL;
        (isset($processRequestFields['prontuario_PontuacaoTotal'])) ? ($dbCreate['PontuacaoTotal'] = $processRequestFields['prontuario_PontuacaoTotal']) : NULL;
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
        $autoColumn = $this->ModelsVCadastroProntuarioPS->getColumnsFromTable();
        // myPrint($autoColumn, '', true);
        if (isset($autoColumn['COLUMN'])) {
            foreach ($autoColumn['COLUMN'] as $key_autoColumn => $value_autoColumn) {
                // myPrint($value_autoColumn, '', true);
                (isset($processRequestFields[$value_autoColumn])) ? ($dbCreate[$value_autoColumn] = $processRequestFields[$value_autoColumn]) : (NULL);
            }
        }
        (isset($processRequestFields['Responsavel_TelefoneMovel'])) ? ($dbCreate['Responsavel_TelefoneMovel'] = $processRequestFields['Responsavel_TelefoneMovel']) : (NULL);
        // {"id":"","profissional_Nome":"","adolescente_Nome":"Lidia","DataCadPsicoSocial":"","PontuacaoTotal":""}
        // (isset($processRequestFields['ReferenciadoNaRede'])) ? ($dbCreate['ReferenciadoNaRede'] = $processRequestFields['ReferenciadoNaRede']) : (NULL);
        // myPrint($dbCreate, 'src\app\Controllers\ExempleDbController.php');
        return ($dbCreate);
    }

    # use App\Controllers\TokenCsrfController;
    # $this->DbController = new ExempleDbController();
    # $this->DbController->dbCreate($parameter);
    public function dbCreate($parameter = NULL)
    {
        try {
            $this->ModelsProntuarioPsicoSocial->dbCreate($this->dbFields($parameter));
            $affectedRows = $this->ModelsProntuarioPsicoSocial->affectedRows();
            if ($affectedRows > 0) {
                $dbCreate['insertID'] = $this->ModelsProntuarioPsicoSocial->insertID();
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
            $this->message->message([$message], 'success', $parameter, 5);
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
        $limit = 10;
        try {
            if ($parameter != NULL) {
                $dbResponse = $this
                    ->ModelsVCadastroProntuarioPS
                    ->where('id', $parameter)
                    ->where('deleted_at', NULL)
                    ->orderBy('id', 'DESC')
                    ->dBread()
                    ->paginate(1, 'paginator', $page);
                //
            } else {
                $dbResponse = $this
                    ->ModelsVCadastroProntuarioPS
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
    public function dbFilter($parameter = NULL, $page = 1, $limit = 10)
    {
        $dt_inicio = isset($parameter['prontuario_data_inicio']) ? $parameter['prontuario_data_inicio'] : null;
        $dt_fim = isset($parameter['prontuario_data_fim']) ? $parameter['prontuario_data_fim'] : null;
        #
        if ($dt_inicio !== null && $dt_fim == null) {
            $parameter['created_at'] = $parameter['prontuario_data_fim'];
        }
        #
        $parameter = $this->dbFieldsFilter($parameter);
        // myPrint($parameter, 'src\app\Controllers\ProntuarioPSDbController.php');
        $getURI = $this->uri->getSegments();
        try {
            if (in_array('id', array_keys($parameter))) {
                $limit = 1;
                $query = $this
                    ->ModelsVCadastroProntuarioPS
                    ->where('deleted_at', NULL);
            } elseif ($dt_inicio !== null && $dt_fim !== null) {
                $query = $this
                    ->ModelsVCadastroProntuarioPS
                    ->where('deleted_at', NULL)
                    ->where('created_at >=', $dt_inicio)
                    ->where('created_at <=', $dt_fim);
            } else {
                $query = $this
                    ->ModelsVCadastroProntuarioPS
                    ->where('deleted_at', NULL);
            }
            // 
            foreach ($parameter as $key => $value) {
                // myPrint($key , $value, true);
                if ($key == 'id') {
                    $query = $query->where($key, $value);
                } else {
                    $query = $query->like($key, $value);
                }
            }

            $dbResponse = $query
                ->orderBy('updated_at', 'DESC')
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
                myPrint($e->getMessage(), 'src\app\Controllers\ProntuarioPSDbController.php');
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
            $this->ModelsProntuarioPsicoSocial->dbUpdate($key, $parameter);
        } else {
            $this->ModelsProntuarioPsicoSocial->dbUpdate($key, $this->dbFields($parameter));
        }
        try {
            $affectedRows = $this->ModelsProntuarioPsicoSocial->affectedRows();
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
                myPrint($e->getMessage(), 'src\app\Controllers\ProntuarioPSDbController.php');
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
            $this->ModelsProntuarioPsicoSocial->dbDelete('id', $parameter);
            $affectedRows = $this->ModelsProntuarioPsicoSocial->affectedRows();
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
            $this->ModelsVCadastroProntuarioPS->dbDelete($parameter);
            $affectedRows = $this->ModelsVCadastroProntuarioPS->affectedRows();
            if ($affectedRows > 0) {
                $dbDelete['deleteID'] = $parameter;
                $dbDelete['affectedRows'] = $affectedRows;
            } else {
                $dbDelete['deleteID'] = $parameter;
                $dbDelete['affectedRows'] = $affectedRows;
            }
            $response = $dbDelete;
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
