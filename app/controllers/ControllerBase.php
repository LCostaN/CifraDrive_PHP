<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    protected $isJson = FALSE;
    protected $idUsuario;

    public function beforeExecuteRoute($dispatcher){
        $this->isJson( $dispatcher->getParam("mobile") );
        $this->setUsuario( $this->session->get("logId") );
        $controller = $dispatcher->getControllerName();

        if( $controller != 'login'  && !($controller == 'usuario' && $dispatcher->getActionName() == 'novo') ){
            if( !$this->idUsuario ){
                if($this->isJson){
                    $response = array( "ok" => FALSE, "message" => "Realize Login antes de continuar.");
                    $this->mobileNotLogged($response);
                } else {
                    $this->dispatcher->forward(
                        [
                            'controller' => 'login',
                            'action'     => 'index',
                        ]
                    );
                }
            }
        }
    }

    public function afterExecuteRoute($dispatcher){
        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();

        if( $this->request->isPost() && ($action != "buscar" && $action != "musicas" && $action != "versoes") ){
            $this->gerarLog($controller, $action);
        }
    }

    public function initialize(){
        $controller = $this->dispatcher->getControllerName();
        if( $controller == 'login' || ($controller == 'usuario' && $this->dispatcher->getActionName() == 'novo') ){
            $this->assets->addCss('css/login.css');
        } else {
            $this->view->setTemplateAfter('common');
            $this->assets->addCss('css/style.css');
        }
    }

    public function setUsuario($id){
        $this->idUsuario = $id;
    }

    public function isJson($api){
        if($api == TRUE){
            $this->isJson = TRUE;
        }
    }

    public function mobileNotLogged($response){
        if( $this->isJson ){
            $this->view->disable();

            $this->response->setContentType('application/json', 'UTF-8');
            $this->response->setContent( json_encode(["logged" => FALSE, "data" => ""]) );

            $this->response->send();
            die;
        }
    }

    public function mobileResponse($response){
        if( $this->isJson ){
            $this->view->disable();

            $this->response->setContentType('application/json', 'UTF-8');
            $this->response->setContent( json_encode(["logged" => TRUE, "data" => $response]) );

            $this->response->send();
            die;
        }
    }

    /**
     * @todo Transferir método de log do BD para arquivos.
     * @todo Criar Logs de Sistema(** Necessário para lançar aplicação em produção **)
     * @todo Diferenciar Registros( Ação do usuario ) e Logs ( Informações de Sistema ** Para lançar em produção **)
     */
    public function gerarLog($categoria, $acao){
        $novo = [];
        $novo['idUsuario']  = $this->idUsuario;
        $novo['data']       = date('Y-m-d H:i:s');
        $novo['categoria']  = $categoria;
        $novo['acao']       = $acao;

        $logs = new Logs();
        $logs->create($novo);
    }
}
