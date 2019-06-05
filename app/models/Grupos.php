<?php

class Grupos extends \Phalcon\Mvc\Model
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
     * 
     * @var string
     */
    protected $imagemPerfil;

    /**
     *
     * @var string
     */
    public $descricao;

    /**
     *
     * @var string
     */
    public $website;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("cifraDriveTeste");
        $this->setSource("grupos");
        $this->hasMany(
            'id',
            'TagGrupo',
            'idGrupo'
        );
        $this->hasManyToMany(
            'id',
            'TagGrupo',
            'idGrupo', 'idTag',
            'Tags',
            'id'
        );
        $this->hasManyToMany(
            'id',
            'UsuarioGrupo',
            'idGrupo', 'idUsuario',
            'Usuarios',
            'id'
        );
        $this->hasMany(
            'id',
            'UsuarioGrupo',
            'idGrupo'
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'grupos';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Grupos[]|Grupos|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Grupos|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Get the value of imagemPerfil
     *
     * @return  string
     */ 
    public function getImagemPerfil()
    {
        return $this->imagemPerfil ?: "img/grupos/no-group-image.jpeg";
    }

    /**
     * Set the value of imagemPerfil
     *
     * @param  string  $imagemPerfil
     */ 
    public function setImagemPerfil(string $imagemPerfil)
    {
        $this->imagemPerfil = $imagemPerfil;
    }

    public function infoGrupo(array $info){
        $this->nome         = $info['nome'];
        $this->descricao    = $info['descricao'];
        $this->website      = $info['website'];
        if( !empty($info['imagemPerfil']) ){
            $this->imagemPerfil = $info['imagemPerfil'];
        }
        $newTags = explode(' ', $info['tags'] );

        $tags = [];
        foreach($newTags AS $key => $value){
            $tags[$key] = (Tags::findFirst("nome = '$value'")) ?: new Tags();
            $tags[$key]->nome = $value;
        }
        $this->removeOldTags();
        $this->tags = $tags;

        $success = $this->save();
        
        return $success;
    }

    public function listarMeusGrupos($idUsuario){
        $usuario = Usuarios::findFirst($idUsuario);

        $lista = [];
        foreach($usuario->grupos AS $key => $row){
            $lista[$key]        = new stdClass();
            $lista[$key]->id    = $row->id; 
            $lista[$key]->nome  = $row->nome;
            $lista[$key]->total = count($row->usuarios);
            $lista[$key]->tags  = $row->toStringTags();
        }

        return $lista;
    }

    public function getGruposDisponiveis(int $idUsuario, $nomeGrupo){
        $usuarioGrupo = new UsuarioGrupo();
        $gruposDoUsuario = $usuarioGrupo->find("idUsuario = $idUsuario");
        
        $ids = [];
        foreach($gruposDoUsuario AS $row){
            $ids[] = $row->idGrupo;
        }

        $conditions = '';
        $bind = [];

        $conditions = "nome LIKE :nome:";
        $bind['nome'] = $nomeGrupo;

        if( !empty($ids) ){
            $conditions .= " AND id NOT IN ({ids:array})";
            $bind['ids'] = $ids;
        }

        $where = [
            $conditions,
            "bind"  => $bind,
        ];

        $grupos = Grupos::find($where);

        $lista = [];
        if( $grupos->count() > 0 ){
            foreach($grupos AS $key => $grupo){
                $lista[$key] = new stdClass();
                $lista[$key]->id      = $grupo->id;
                $lista[$key]->nome    = $grupo->nome;
                $lista[$key]->total   = count($grupo->usuarios);
                $lista[$key]->tags    = $grupo->toStringTags($grupo->tags);
            }
        }

        return $lista;
    }

    public function getGrupo($idGrupo, $idUsuario){
        $grupo       = $this->findFirst($idGrupo);
        $participante = UsuarioGrupo::isParticipante($idUsuario, $idGrupo);

        $info = new stdClass();
        $info->id        = $grupo->id;
        $info->nome      = $grupo->nome;
        $info->lider     = $grupo->idUsuario;
        $info->descricao = $grupo->descricao;
        $info->website   = $grupo->website;
        $info->foto      = $grupo->getImagemPerfil();
        $info->privado   = $grupo->privado;
        $info->tags      = $grupo->toStringTags();
        $info->participa = $participante;
        $info->membros   = array();
        foreach($grupo->usuarioGrupo AS $membro){
            $object = new stdClass();
            $object->nome = $membro->usuarios->nome;
            $object->dataIngresso = $membro->dataIngresso;
            $info->membros[] = $object;
        }

        return $info;
    }

    public function escolherNovoLider(){
        $membros = $this->usuarioGrupo;
        if($membros->count() == 0){
            return FALSE;
        }
        $lider = NULL;
        $dataIngresso = time();
        foreach($membros AS $membro){
            if(strtotime($membro->dataIngresso) < $dataIngresso){
                $lider = $membro->idUsuario;
                $dataIngresso = $membro->dataIngresso; 
            }
        }

        $this->idUsuario = $lider;
        $this->save();

        return TRUE;
    }

    public function removeOldTags(){
        foreach($this->tagGrupo AS $row){
            $row->delete();
        }
        return TRUE;
    }

    public function toStringTags(){
        $lista = [];
        foreach($this->tags AS $tag){
            $lista[] = $tag->nome;
        }

        return implode(' ', $lista);
    }
}
