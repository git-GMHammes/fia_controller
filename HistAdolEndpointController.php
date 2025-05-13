<?php 

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\TokenCsrfController;

use Exception;

class HistAdolEndpointController extends ResourceController
{
    private $template = 'analise/templates/main';
    private $app_message_card = 'analise/AppMessageCard';
    private $app_loading = 'analise/AppLoading';
    private $app_footer = 'analise/AppFooter';
    private $app_json = 'analise/AppJson';
    private $app_head = 'analise/AppHead';
    private $app_menu = 'analise/AppMenu';
    private $message = 'analise/message';
    private $viewFormatacao;
    private $ModelResponse;
    private $viewValidacao;
    private $viewPadroes;
    private $tokenCsrf;
    private $token;
    private $uri;
    #
    public function __construct()
    {
        $this->uri = new \CodeIgniter\HTTP\URI(current_url());
        // $this->viewFormatacao = new SystemBaseController();
        // $this->viewValidacao = new SystemBaseController();
        // $this->viewPadroes = new SystemBaseController();
        // $this->tokenCsrf = new TokenCsrfController();
        $this->tokenCsrf = new TokenCsrfController();
        $this->token = isset($_COOKIE['token']) ? $_COOKIE['token'] : '123';
    }
    #
    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [view]
    public function index()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }
    #
    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [view]
    public function dbRead()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }
    #
    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [view]
    public function dbFilter()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }
    #
    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [view]
    public function dbCreate()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }
    #
    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [view]
    public function dbUpdate()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }
    #
    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [view]
    public function dbDelete()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }
    #
    # route POST /www/sigla/rota
    # route GET /www/sigla/rota
    # Informação sobre o controller
    # retorno do controller [view]
    public function dbCleanner()
    {
        exit('403 Forbidden - Directory access is forbidden.');
    }
}

?>