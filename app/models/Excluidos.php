<?php
class Excluidos extends \Phalcon\Mvc\Model
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
    public $idUsuario;

    /**
     *
     * @var string
     */
    public $motivo;

    /**
     *
     * @var string
     */
    public $dataExclusao;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("cifraDriveTeste");
        $this->setSource("excluidos");
        $this->belongsTo('idUsuario', 'Usuarios', 'id');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'excluidos';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Excluidos[]|Excluidos|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Excluidos|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
