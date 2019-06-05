<?php

class AdminController extends \Phalcon\Mvc\Controller
{
    public function indexAction()
    {
        $this->response->setStatusCode(404, "Not Found");
    }

    // EXECUTA O MÉTODO DE HASH NAS SENHAS; ** DESENVOLVIMENTO **
    // public function arrumarSenhasAction(){
    //     foreach(Usuarios::find() AS $row){
    //         $row->senha = hash("sha256", $row->senha.$row->salt);
    //         $row->save();
    //     }

    //     echo "Done!";
    // }
}

