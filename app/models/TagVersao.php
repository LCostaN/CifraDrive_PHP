<?php

class TagVersao extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $idTag;

    /**
     *
     * @var integer
     */
    public $idVersao;

    /**
     * @var integer
     */
    public $avaliacao;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("cifraDriveTeste");
        $this->setSource("tagVersao");
        $this->belongsTo('idTag', 'Tags', 'id');
        $this->belongsTo('idVersao', 'Versoes', 'id');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'tagVersao';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return TagVersao[]|TagVersao|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return TagVersao|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function toStringTags(){
        $lista = [];
        foreach($this->tags AS $tag){
            $lista[] = $tag->nome;
        }

        return implode(' ', $lista);
    }
}
