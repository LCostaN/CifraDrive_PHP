<?php

class UsuarioController extends ControllerBase
{
    public function indexAction(){
        $this->request->redirect('usuario/perfil');
    }

    public function novoAction(){
        if( $this->request->isPost() ){
            $email = $this->request->getPost('email','string');
            $nome = $this->request->getPost('nome','string');
            $senha = $this->request->getPost('senha','string');
            $confirm = $this->request->getPost('confirm','string');
            
            if( $email && $nome && $senha && $confirm){
                $usuario = new Usuarios();
            
                $novo = [
                    'email'     => $email,
                    'nome'      => $nome,
                    'senha'     => $senha,
                    'confirm'   => $confirm
                ];

                $valid = $usuario->validar($novo);
                if($valid < 1){
                    $this->mobileResponse($valid);
                } else {
                    $success = $usuario->cadastrar($novo);
                    if ($success) {
                        $this->mobileResponse( (int) $success );
                        $this->flashSession->success( "Bem vindo à comunidade Cifra Drive!" );
                        $this->response->redirect('');
                    } else {
                        $messages = $usuario->getMessages();
                        
                        $msgErro = "";
                        foreach ($messages as $message) {
                            $msgErro .= $message->getMessage() . " ";
                        }
                        $this->flashSession->error( $msgErro );
                    }
                }
            } else {
                $this->mobileResponse(false);
                $this->flashSession->error( "Campos não preenchidos.");
            }
        }
    }

    public function perfilAction(){
        $usuario = Usuarios::findFirst( $this->idUsuario );

        if( $this->request->isPost() ){
            $nome       = $this->request->getPost('nome','string');
            $sexo       = $this->request->getPost('sexo', ['alphanum','lower']);
            $dataNasc   = $this->request->getPost('dataNasc', 'string');

            if( $nome ){
                $this->mobileResponse(false);
            }

            $dataNasc = date('Y-m-d H:i:s', strtotime($dataNasc) );

            $update = [
                'nome' => $nome,
            ];

            if( $dataNasc ){
                $update['dataNasc'] = $dataNasc;
            }

            if($sexo){
                $update['sexo'] = $sexo;
            }

            if( $this->request->hasFiles() ){
                $file = $this->request->getUploadedFiles()[0];
                if( $file->getError() == 0 ){
                    $filePath = "img/usuarios/".$file->getName();
    
                    $file->moveTo(
                        $filePath
                    );
    
                    $update['imagemPerfil'] = $filePath;
                }
            }

            $success = $usuario->update(
                $update
            );

            $this->mobileResponse( $success );

            if ($success) {
                $this->flashSession->success( "Dados de perfil alterados com sucesso!" );
            } else {
                $msgErro = "ERRO! Os seguintes problemas ocorreram: ";

                $messages = $usuario->getMessages();

                foreach ($messages as $message) {
                    $msgErro .= $message->getMessage() . "<br/>";
                }
                $this->flashSession->error( $msgErro );
            }
        } else {
            $this->mobileResponse($usuario);
        }
        $this->view->usuario = $usuario;
    }

    public function downloadAction(){
        $this->view->disable();
        $mimes = [
            '.png'  => "image/png",
            '.jpeg' => "image/jpeg",
            '.jpg'  => "image/jpeg",
        ];

        $usuario = Usuarios::findFirst($this->idUsuario);

        $path = FILE_PATH . $usuario->imagemPerfil;
        $filesize = filesize($path);
        $attachment = "Foto_$usuario->nome";

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
}