<?php

class LoginController extends ControllerBase
{

    public function indexAction(){
        if( !!$this->idUsuario ){
            return $this->response->redirect('home');
        }
    }

    public function entrarAction(){
        if( $this->request->isPost() ){
            $email = $this->request->getPost('email','email');
            $senha = $this->request->getPost('senha','string');
            $hashCode = $this->request->getPost('hash','string');
            $mobileHash = FALSE;
            $success = FALSE;
            
            if($hashCode){
                $mobileHash = $this->hashLogin($hashCode);
            }
            $mobileHash = $mobileHash ?: $this->logar($email, $senha);
            $success = !!$mobileHash;
            
            $result['ok']   = $success;
            $result['hash'] = $mobileHash;

            $this->mobileResponse($result);
            
            if( $success ){
                return $this->response->redirect('home');
            }
            $this->flashSession->error("Usuario ou Senha incorretos.");
        }
        return $this->response->redirect('login');
    }
    
    
    public function hashLogin($hash){
        $usuario = $this->compareHash($hash);

        if($usuario){
            $newHash = sha1( time() );
            $this->saveHash($usuario, $newHash);
            $this->session->set('logId', $usuario);
            return $newHash;
        }
        return FALSE;
    }

    public function logar($email, $senha){
        if(!$email || !$senha){
            return FALSE;
        }
        $usuario = $this->validarLogin($email, $senha);

        if($usuario){
            $newHash = sha1( time() );
            $this->saveHash($usuario, $newHash);
            $this->session->set('logId', $usuario);
            return $newHash;
        }
        return FALSE;
    }

    public function logoutAction(){
        $this->session->destroy();
        $this->mobileResponse(true);
        $this->response->redirect('login');
    }

    public function validarLogin($email, $senha){
        $usuario = Usuarios::findFirst("
            email = '$email'
        ");

        $result = $usuario->isCorrectPassword($senha);

        return $result ? $usuario->id : 0;
    }

    public function compareHash($hash){
        $usuario = Usuarios::findFirst("
            mobileHash = '$hash'
        ");

        return !!$usuario ? $usuario->id : 0;
    }

    public function saveHash($idUsuario, $hash){
        $usuario = Usuarios::findFirst($idUsuario);
        $success = $usuario->saveHash($hash);

        return $success;
    }
}

