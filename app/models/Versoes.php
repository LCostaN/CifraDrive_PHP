<?php

class Versoes extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $idMusica;

    /**
     *
     * @var integer
     */
    public $idUsuario;

    /**
     *
     * @var string
     */
    public $nome;

    /**
     *
     * @var string
     */
    public $tomOriginal;

    /**
     *
     * @var string
     */
    public $arquivo;

    /**
     *
     * @var string
     */
    public $dataAdicionado;

    /**
     *
     * @var integer
     */
    public $avaliacao;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("cifraDriveTeste");
        $this->setSource("versoes");
        $this->belongsTo(
            'idMusica',
            'Musicas',
            'id'
        );
        $this->hasMany(
            'id',
            'TagVersao',
            'idVersao'
        );
        $this->hasManyToMany(
            'id',
            'TagVersao',
            'idVersao', 'idTag',
            'Tags',
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
        return 'versoes';
    }

    public function addAvaliacao(){
        $this->avaliacao += 1;
        $this->save();
    }

    public function lessAvaliacao(){
        $this->avaliacao -= 1;
        $this->save();
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Versoes[]|Versoes|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Versoes|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function atualizarTags(string $newTags){
        $this->tagVersao->delete();
        $newTags = explode(' ', $newTags );

        $tags = [];
        foreach($newTags AS $key => $value){
            $tags[$key] = (Tags::findFirst("nome = '$value'")) ?: new Tags();
            $tags[$key]->nome = $value;
        }
        $this->tags = $tags;

        $erro = $this->update();

        if(!$erro){
            $messages = $this->getMessages();
                
            $erro = '';
            foreach ($messages as $message) {
                $erro .= $message->getMessage() . "<br/>";
            }
            return $erro;
        }
    }

    public function toStringTags(){
        $tags = "";

        foreach($this->tags AS $tag){
            $tags .= $tag->nome . " ";
        }
        return $tags;
    }

    public function verMusica(){
        $dados = new stdClass();
        $dados->id              = $this->id;
        $dados->idMusica        = $this->idMusica;
        $dados->idUsuario       = $this->idUsuario;
        $dados->nome            = $this->nome;
        $dados->tomOriginal     = $this->tomOriginal;
        $dados->arquivo         = $this->arquivo;
        $dados->dataAdicionado  = $this->dataAdicionado;
        $dados->avaliacao       = $this->avaliacao;
        $dados->nomeMusica      = $this->musicas->nome;
        $dados->tags            = $this->toStringTags();

        return $dados ?: array();
    }
}
