<?php

class IndexController extends \Phalcon\Mvc\Controller
{
    public function indexAction()
    {
        $this->response->redirect('login');
     
    }

}

