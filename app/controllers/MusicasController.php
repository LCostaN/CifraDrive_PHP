<?php

class MusicasController extends ControllerBase
{

    public function indexAction()
    {
        $this->response->redirect('musicas/listarMusicas');
    }

    public function listarMusicasAction(){
        $musicas = new Musicas();
        $nomeMusica = '';

        if( $this->request->isPost() ){
            $nomeMusica = $this->request->getPost('procurar','string');
        }

        $musicas = $musicas->listarMusicas($nomeMusica);
        
        $this->mobileResponse($musicas);
        $this->view->musicas = $musicas;
    }

    public function versoesAction($idMusica){
        $musica = new Musicas();
        $musica = $musica->findFirst($idMusica)->versoes();

        $this->mobileResponse($musica);
        $this->view->musica = $musica;
    }

    public function novaMusicaAction(){
        if( $this->request->isPost() ){
            
            $nome = $this->request->getPost('nome','string');
            $versao = $this->request->getPost('versao','string');
            $tom = $this->request->getPost('tom','alphanum');
            $tags = $this->request->getPost('tags','string');
            
            if( $this->request->hasFiles() ){
                $file = $this->request->getUploadedFiles()[0];
                if( $file->getError() == 0 ){
                    $filePath = "files/cifras/" . $nome . "_" . $file->getName();
                    
                    $file->moveTo(
                        $filePath
                    );
                }
            }

            $novo = [
                'nome'          => $nome,
                'versao'        => $versao,
                'idUsuario'     => $this->idUsuario,
                'tomOriginal'   => $tom,
                'arquivo'       => $filePath,
                'dataAdicionado'=> date('Y-m-d H-i-s'),
                'avaliacao'     => 0,
                'tags'          => $tags
            ];
            
            $musica  = Musicas::findFirst("nome LIKE '{$novo['nome']}'") ?: new Musicas();
            $success = $musica->cadastrarMusica($novo);
            
            if ( $success === TRUE ) {
                $msgResultado = "Cadastrado com sucesso!";
                
                $this->mobileResponse($success);
                $this->flashSession->success($msgResultado);
            } else {
                $msgResultado = $success;
                $this->mobileResponse($success);
                // $messages = $versao->getMessages();
                
                // foreach ($messages as $message) {
                //     echo $message->getMessage(), "<br/>";
                // }
                $this->flashSession->error($msgResultado);
            }
        }
    }

    public function verMusicaAction($idVersao){
        $musica = new Versoes();
        $musica = $musica->findFirst($idVersao)->verMusica();

        $this->mobileResponse($musica);
        $this->view->musica = $musica;
    }

    public function downloadAction($idVersao){
        $this->view->disable();
        $musica = Versoes::findFirst($idVersao);

        $path = FILE_PATH . $musica->arquivo;
        $filesize = filesize($path);
        $attachment = $musica->musicas->nome . ' - ' . $musica->nome;
        
        $this->response->setHeader("Cache-Control", 'must-revalidate, post-check=0, pre-check=0');
        $this->response->setHeader("Content-Description", 'File Download');
        $this->response->setContentType('application/pdf');
        $this->response->setContentLength($filesize);
        $this->response->setFileToSend($path, str_replace(" ", "_", $attachment).'.pdf',true);
        $this->response->send();
        die();
    }

    public function likeAction($idVersao){
        $versao = Versoes::findFirst($idVersao);
        $versao->addAvaliacao();

        return $this->response->redirect('musicas/verMusica/'.$idVersao);
    }

    public function hateAction($idVersao){
        $versao = Versoes::findFirst($idVersao);
        $versao->lessAvaliacao();

        return $this->response->redirect('musicas/verMusica/'.$idVersao);
    }

    public function tagsAction($idVersao){
        $versao = Versoes::findFirst($idVersao);
        $tags = $this->request->getPost('tags', 'string');

        $erro = $versao->atualizarTags($tags);

        if($erro){
            $this->flashSession->error($erro);
        }

        return $this->response->redirect('musicas/verMusica/'.$idVersao);
    }

    public function getNomesAction(){
        $musicas = Musicas::findNames();

        $this->mobileResponse($musicas);
    }
}