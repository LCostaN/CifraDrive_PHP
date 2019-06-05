<?php

class Tags extends \Phalcon\Mvc\Model
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
        $this->setSource("tags");
        $this->hasMany(
            'id',
            'TagVersao',
            'idTag'
        );
        $this->hasMany(
            'id',
            'TagGrupo',
            'idTag'
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'tags';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Tags[]|Tags|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Tags|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function removeUnusedTags(){
        $tagGrupo   = TagGrupo::find(['columns' => "idTag"]);
        $tagMusica  = TagVersao::find(['columns' => "idTag"]);

        $lista = [];
        foreach($tagGrupo AS $row){
            $lista[$row->idTag] = $row->idTag;
        }

        foreach($tagMusica AS $row){
            $lista[$row->idTag] = $row->idTag;
        }
        $lista = array_values($lista);

        $tags = $this->find([
            'conditions' => "id NOT IN ({ids:array})",
            "bind"  => [
                "ids" => $lista
            ],
        ]);

        foreach($tags AS $row){
            $row->delete();
        }

        return TRUE;
    }
}
