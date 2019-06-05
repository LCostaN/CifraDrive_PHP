<?php

class TagGrupo extends \Phalcon\Mvc\Model
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
    public $idGrupo;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("cifraDriveTeste");
        $this->setSource("tagGrupo");
        $this->belongsTo('idTag', 'Tags', 'id');
        $this->belongsTo('idGrupo', 'Grupos', 'id');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'tagGrupo';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return TagGrupo[]|TagGrupo|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return TagGrupo|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
}
