<?php

class TesteController extends \Phalcon\Mvc\Controller
{
    protected $data;

    public function indexAction()
    {
        $this->data = array(
            array(
                "nome"   => "Geovana",
                "idade"  => 20,
                "altura" => 1,
                "telefone" => array(
                    "fixo" => 33332222,
                    "celular" => 911112222,
                    "namorado" => 123456789
                ),
            ),
            array(
                "nome"   => "Devair",
                "idade"  => 20,
                "estado" => "depressivo",
                "medidas" => array(
                    180,
                    200,
                    300,
                ),
            ),
            array(
                "nome"   => "Lucas",
                "idade"  => 23,
                "estCivil" => "noivo"
            ),
        );
        
        echo $this->data = json_encode($this->data);
        echo "<br/>";
        $json = json_decode($this->data);

        foreach($json AS $row){
            echo $row->nome . "<br/>";
            echo $row->idade . "<br/>";
            echo "<br/>";

            $row->telefone = (array) $row->telefone;
            
            echo $row->telefone['fixo'] . "<br/>";
            die;
        }
        die;
    }
}

