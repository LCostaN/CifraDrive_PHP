<?php

use Phalcon\Mvc\Model\Message as Message;

class Usuarios extends \Phalcon\Mvc\Model
{

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $nome;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $senha;

    /**
     * @var string
     */
    protected $salt;

    /**
     * @var integer
     */
    protected $status;

    /**
     * @var string
     */
    protected $dataNasc;

    /**
     * @var string
     */
    protected $sexo;

    /**
     * @var string
     */
    protected $imagemPerfil;

    /**
     *
     * @var string
     */
    public $mobileHash;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("cifraDriveTeste");
        $this->setSource("usuarios");
        $this->belongsTo(
            "status",
            "StatusTipos",
            'id'
        );
        $this->hasManyToMany(
            'id',
            'UsuarioGrupo',
            'idUsuario', 'idGrupo',
            'Grupos',
            'id'
        );
        $this->hasMany(
            'id',
            'UsuarioGrupo',
            'idUsuario'
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return "usuarios";
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Usuarios[]|Usuarios|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Usuarios|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function saveHash($hash){
        $this->mobileHash = $hash;
        return $this->save();
    }
    
    public function validar($novo){
        $usuario = $this->findFirstByEmail($novo['email']);
        $result  = 1;
        if($usuario){
            $result = -1;

            $text  = "Email já em uso.";
            $field = "email";
            $type  = "InvalidValue";

            $message = new Message($text, $field, $type);
            $this->appendMessage($message);
        }
        if($novo['senha'] != $novo['confirm']){
            $result = 0;
            
            $text  = "Senha não foi confirmada corretamente.";
            $field = "email";
            $type  = "InvalidValue";
            
            $message = new Message($text, $field, $type);
            $this->appendMessage($message);
        }
        return $result;
    }

    public function cadastrar($novo){
        $salt = sha1( time() );
        $senha = $novo['senha'].$salt;
        $encryptedSenha = sha256($senha);

        $this->email    = $novo['email'];
        $this->nome     = $novo['nome'];
        $this->salt     = $salt;
        $this->senha    = $encryptedSenha;

        $success = $this->save();

        return $success;
    }

    public function isCorrectPassword($senha){
        $wholePassword = $senha . $this->salt;
        $hashPassword = hash("sha256", $wholePassword);
        
        return strcmp($hashPassword, $this->senha) == 0;
    }

    /**
     * Get the value of id
     *
     * @return  integer
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param  integer  $id
     */ 
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the value of nome
     *
     * @return  string
     */ 
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set the value of nome
     *
     * @param  string  $nome
     */ 
    public function setNome(string $nome)
    {
        $this->nome = $nome;
    }

    /**
     * Get the value of email
     *
     * @return  string
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @param  string  $email
     */ 
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * Get the value of senha
     *
     * @return  string
     */ 
    public function getSenha()
    {
        return $this->senha;
    }

    /**
     * Set the value of senha
     *
     * @param  string  $senha
     */ 
    public function setSenha(string $senha)
    {
        $this->senha = $senha;
    }

    /**
     * Get the value of salt
     *
     * @return  string
     */ 
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set the value of salt
     *
     * @param  string  $salt
     */ 
    public function setSalt(string $salt)
    {
        $this->salt = $salt;
    }

    /**
     * Get the value of status
     *
     * @return  integer
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param  integer  $status
     */ 
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get the value of dataNasc
     *
     * @return  string
     */ 
    public function getDataNasc()
    {
        return $this->dataNasc;
    }

    /**
     * Set the value of dataNasc
     *
     * @param  string  $dataNasc
     */ 
    public function setDataNasc(string $dataNasc)
    {
        $this->dataNasc = $dataNasc;
    }

    /**
     * Get the value of sexo
     *
     * @return  string
     */ 
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * Set the value of sexo
     *
     * @param  string  $sexo
     */ 
    public function setSexo(string $sexo)
    {
        $this->sexo = $sexo;
    }

    /**
     * Get the value of imagemPerfil
     *
     * @return  string
     */ 
    public function getImagemPerfil()
    {
        return $this->imagemPerfil ?: "img/usuarios/no-profile-image.jpeg";
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
}
