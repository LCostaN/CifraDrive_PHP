<?php

class GruposController extends ControllerBase
{
    public function indexAction()
    {
        $this->response->redirect('grupos/meusGrupos');
    }

    public function meusGruposAction(){
        
        $grupos = new Grupos();

        $lista = $grupos->listarMeusGrupos($this->idUsuario);
        
        $this->mobileResponse($lista);
        $this->view->grupos = $lista;
    }

    public function novoGrupoAction(){
        if( $this->request->isPost() ){
            $novo['nome']       = $this->request->getPost('nome','string');
            if( Grupos::findFirst("nome LIKE ".$novo['nome']) ){
                $msgResultado = "Nome de grupo já existe!";
                $this->flashSession->error($msgResultado);
            } else {
                $grupo = new Grupos();
                $novo['website']    = $this->request->getPost('website','string');
                $novo['descricao']  = $this->request->getPost('descricao','string');
                $novo['tags']       = $this->request->getPost('tags','string');
                
                if( $this->request->hasFiles() ){
                    $file = $this->request->getUploadedFiles()[0];
                    if($file->getError() == 0){
                        $filePath = "img/grupos/".$file->getName();
                        
                        $file->moveTo(
                            $filePath
                        );
                        
                        $novo['imagemPerfil'] = $filePath;
                    }
                }

                $success = $grupo->infoGrupo($novo);

                if ($success) {
                    $usuarioGrupo = new UsuarioGrupo();
                    $usuarioGrupo->ingressarGrupo($this->idUsuario, $grupo->id);

                    $this->mobileResponse( $success );

                    $msgResultado = "Cadastrado com sucesso!";
                    $this->flashSession->success($msgResultado);
                } else {
                    $this->mobileResponse( !!$success );

                    $msgResultado = "ERRO! Os seguintes problemas ocorreram: ";

                    // $messages = $grupo->getMessages();

                    // foreach ($messages as $message) {
                    //     $msgResultado .= $message->getMessage()."\n";
                    // }
                    $this->flashSession->error($msgResultado);
                }
            }
        }
    }

    public function buscarAction(){
        $grupo      = new Grupos();
        $nomeGrupo  = "%";
        
        if( $this->request->isPost() ){
            $nomeGrupo .= $this->request->getPost('buscarNome','string');
        }
        $nomeGrupo  .= "%";

        $lista = $grupo->getGruposDisponiveis($this->idUsuario, $nomeGrupo);

        $this->mobileResponse($lista);
        $this->view->grupos = $lista; 
    }

    public function verGrupoAction($idGrupo){        
        $grupo = new Grupos();
        $grupo = $grupo->getGrupo($idGrupo, $this->idUsuario);

        $this->mobileResponse($grupo);
        $this->view->grupo = $grupo;
        // TODO: REMOVER APÓS CRIAR SERVIÇO DE USUARIO LOGADO;
        $this->view->logadoAs = $this->idUsuario;
    }

    public function sairGrupoAction($idGrupo){
        
        $usuarioGrupo = new UsuarioGrupo();

        $ok = $usuarioGrupo->sairGrupo($this->idUsuario, $idGrupo);

        if( $this->destruirGrupoSeVazio($idGrupo) ){
            $this->mobileResponse($ok);
            return $this->response->redirect('grupos/meusGrupos');
        }
        $this->mobileResponse($ok);
        return $this->response->redirect('grupos/verGrupo/'.$idGrupo);
    }

    public function ingressarGrupoAction($idGrupo){
        
        $usuarioGrupo = new UsuarioGrupo();

        $ok = $usuarioGrupo->ingressarGrupo($this->idUsuario, $idGrupo);
        
        $this->mobileResponse($ok);
        return $this->response->redirect('grupos/verGrupo/'.$idGrupo);
    }

    public function downloadAction($idGrupo){
        $this->view->disable();
        $mimes = [
            '.png'  => "image/png",
            '.jpeg' => "image/jpeg",
            '.jpg'  => "image/jpeg",
        ];

        $grupo = Grupos::findFirst($idGrupo);

        $path = FILE_PATH . $grupo->imagemPerfil;
        $filesize = filesize($path);
        $attachment = "Foto_$grupo->nome";

        $type = ".".pathinfo($path, PATHINFO_EXTENSION);
        $mimetype = $mimes[$type];
        
        $this->response->setHeader("Cache-Control", 'must-revalidate, post-check=0, pre-check=0');
        $this->response->setHeader("Content-Description", 'File Download');
        $this->response->setContentType($mimetype);
        $this->response->setContentLength($filesize);
        $this->response->setFileToSend($path, str_replace(" ", "_", $attachment).$type, true);
        $this->response->send();
        die;
    }

    public function atualizarAction($idGrupo){
        $grupo = Grupos::findFirst($idGrupo);
        
        if( $this->request->isPost() ){
            $update['nome']       = $this->request->getPost('nome','string');
            $update['website']    = $this->request->getPost('website','string');
            $update['descricao']  = $this->request->getPost('descricao','string');
            $update['tags']       = $this->request->getPost('tags', 'string');
            
            if( $this->request->hasFiles() ){
                $file = $this->request->getUploadedFiles()[0];
                if($file->getError() == 0){
                    $filePath = "img/grupos/".$file->getName();
                    
                    $file->moveTo(
                        $filePath
                    );
                    
                    $update['imagemPerfil'] = $filePath;
                }
            }
            $success = $grupo->infoGrupo($update);
            $tags = new Tags();
            $tags->removeUnusedTags();
            
            $this->mobileResponse($success);
            
            if($success){
                return $this->response->redirect('grupos/verGrupo/'.$grupo->id);
            } else {
                $this->flashSession->error("Não foi possível atualizar os dados!");
            }
        }
        
        $grupo = $grupo->getGrupo($idGrupo, $this->idUsuario);
        $this->mobileResponse($grupo);
        $this->view->grupo = $grupo;
    }

    public function destruirGrupoSeVazio($idGrupo){
        $total = UsuarioGrupo::count([
            "idGrupo = $idGrupo"
        ]);
        if($total == 0){
            $grupo = new Grupos();
            $grupo = $grupo->getGrupo($idGrupo);

            $grupo->delete();

            return TRUE;
        }

        return FALSE;
    }
}
