<?php

class Musicas extends \Phalcon\Mvc\Model
{
    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $nome;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("cifraDriveTeste");
        $this->setSource("musicas");
        $this->hasMany(
            'id',
            'Versoes',
            'idMusica'
        );
        $this->hasOne(
            'idUsuario',
            'Usuarios',
            'id'
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'musicas';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Musicas[]|Musicas|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Musicas|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function findNames(){
        $nomes = parent::find(["column" => "nome"]);

        $string = "";
        foreach($nomes as $row){
            $string .= $string == "" ? "" : ",";
            $string .= $row->nome;
        }

        return $string;
    }

    public function cadastrarMusica(array $novo){
        $this->nome = $novo['nome'];
        
        $novaVersao = new Versoes();
        $novaVersao->idMusica       = $this->id;
        $novaVersao->idUsuario      = $novo['idUsuario'];
        $novaVersao->nome           = $novo['versao'];
        $novaVersao->tomOriginal    = $novo['tomOriginal'];
        $novaVersao->arquivo        = $novo['arquivo'];
        $novaVersao->dataAdicionado = $novo['dataAdicionado'];
        $novaVersao->avaliacao      = $novo['avaliacao'];
        
        $newTags = explode(' ', $novo['tags'] );
        $tags = [];
        foreach($newTags AS $key => $value){
            $tags[$key] = (Tags::findFirst("nome = '$value'")) ?: new Tags();
            $tags[$key]->nome = $value;
        }
        $novaVersao->tags = $tags;
        $this->versoes = $novaVersao;

        $success = $this->save();

        if(!$success){
            $messages = $this->getMessages();
                
            $success = '';
            foreach ($messages as $message) {
                $success .= $message->getMessage() . "<br/>";
            }
        }

        return $success;
    }

    public function listarMusicas($nomeMusica = FALSE){
        $where = '';
        if($nomeMusica){
            $nomeMusica = '%'.$nomeMusica.'%';
            $where = [
                "conditions" => "nome LIKE ?0",
                "bind" => [
                    0 => $nomeMusica
                ]
            ];
        }

        $musicas = $this->find($where);

        $lista = [];
        foreach($musicas AS $key => $row){
            $lista[$key] = new stdClass();
            $lista[$key]->id    = $row->id;
            $lista[$key]->nome  = $row->nome;
            $lista[$key]->totalVersoes = count($row->versoes);
        }

        return $lista;
    }

    public function versoes(){
        $lista = new stdClass();
        $lista->id      = $this->id;
        $lista->nome    = $this->nome;
        foreach($this->versoes AS $key => $versao){
            $lista->versoes[$key] = new stdClass();
            $lista->versoes[$key]->id           = $versao->id;
            $lista->versoes[$key]->nome         = $versao->nome;
            $lista->versoes[$key]->tomOriginal  = $versao->tomOriginal;
            $lista->versoes[$key]->avaliacao    = $versao->avaliacao;
            $lista->versoes[$key]->tags         = $versao->toStringTags();
        }
        return $lista;
    }
}
