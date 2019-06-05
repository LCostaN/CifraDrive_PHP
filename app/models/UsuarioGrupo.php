<?php

class UsuarioGrupo extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $idUsuario;

    /**
     *
     * @var integer
     */
    public $idGrupo;

    /**
     * 
     * @var date
     */
    public $dataIngresso;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("cifraDriveTeste");
        $this->setSource("usuarioGrupo");
        $this->belongsTo(
            'idUsuario', 
            'Usuarios', 
            'id',
            [
                'foreignKey' => [
                    'message' => 'The idUsuario does not exist on the Usuarios model'
                ]
            ]
        );
        $this->belongsTo(
            'idGrupo', 
            'Grupos', 
            'id',
            [
                'foreignKey' => [
                    'message' => 'The idGrupo does not exist on the Grupos model'
                ]
            ]
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'usuarioGrupo';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return UsuarioGrupo[]|UsuarioGrupo|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return UsuarioGrupo|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Returns a boolean indicating if the user belongs to that group
     * 
     * @param int idUsuario
     * @param int idGrupo
     * 
     * @return boolean
     */
    public static function isParticipante(int $idUsuario, int $idGrupo){
        return !!parent::findFirst([
           "idUsuario = $idUsuario AND idGrupo = $idGrupo"
        ]);
    }

    public function ingressarGrupo(int $idUsuario, int $idGrupo){
        $novo = [
            'idUsuario' => $idUsuario,
            'idGrupo'   => $idGrupo,
            'dataIngresso' => date('Y-m-d H:i:s'),
        ];

        $result = $this->create($novo);

        if($result === FALSE){
            $messages = $user->getMessages();

            foreach ($messages as $message) {
                $exception =  $message . "\n";
            }
            throw Exception($exception);
        }

        return $result;
    }

    public function sairGrupo(int $idUsuario, int $idGrupo){
        $membro = $this->findFirst([
            "idUsuario = $idUsuario AND idGrupo = $idGrupo"
         ]);

        $result = $membro->delete();
        if($result === FALSE){
            $messages = $membro->getMessages();

            foreach ($messages as $message) {
                $exception =  $message . "\n";
            }
            throw Exception($exception);
        }

        $grupo = Grupos::findFirst($idGrupo);
        if( $idUsuario == $grupo->idUsuario){
            $grupo->escolherNovoLider();
        }
        
        return $result;
    }
}
