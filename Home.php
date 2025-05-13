<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        // return view('dist/index');
        return view('welcome_message');
    }

    public function showReactApp()
    {
        // Caminho completo para o arquivo HTML
        $filePath = APPPATH . 'Views/dist/index.html';

        // Verificar se o arquivo existe
        if (file_exists($filePath)) {
            // Ler o conteúdo do arquivo
            $content = file_get_contents($filePath);

            // Retornar o conteúdo com o cabeçalho correto
            return $this->response->setHeader('Content-Type', 'text/html')
                ->setBody($content);
        }

        // Retorna uma mensagem de erro se o arquivo não for encontrado
        return 'Arquivo HTML não encontrado';
    }
}
