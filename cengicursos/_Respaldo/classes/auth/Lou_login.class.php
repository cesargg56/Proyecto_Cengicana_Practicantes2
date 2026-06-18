<?php
/*
 * Lou_Auth_User file
 * This class performs the access control users on a system.
 * @author António Lourenço (Antlou) <aftlourenco@gmail.com>
 * version 1.0
 * 19/09/2014
 */

class Lou_Auth_User
{
    private $db; // database handler
    private $endereco; // user (mail adress)
    private $senha; // typed password
    private $e_senha; // typed password after encryption
    private $auth; // boolean to accept user connexion

    public function __construct($endereco, $senha)
    {
        session_start();
        $host  = 'mysql'; //host name
        $login = 'root'; //db user
        $pwd   = 'u}bp*H}rWD4-}Q4%'; //db password
        $base  = 'cengi_cursos'; //db name

        $this->endereco = $endereco;
        $this->senha    = $senha;

        $this->db = new PDO('mysql:host=' . $host . ';dbname=' . $base, $login, $pwd);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        /* db connected */

        $sql  = 'SELECT id, password, rol FROM users WHERE email="' . $this->endereco . '"';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result              = $stmt->fetch();
        $this->e_senha       = $result->password;
        $_SESSION['CMenus']  = $result->rol;
        $_SESSION['UActivo'] = $result->id;
        $this->auth          = $this->compare_passwords($senha, $this->e_senha);

        return $this->auth;
    }

    public function isAuthUser()
    {
        return $this->auth;
    }

    private function compare_passwords($password, $remote_pass)
    {
        /* Function to compare the encrypted password is the same in Db    */
        $parts = explode(':', $remote_pass);

        if (count($parts) < 2) {
            $converted_pass = md5($password);
        } else {
            $salt           = $parts[1];
            $converted_pass = md5($password . $salt) . ":" . $salt;
        }

        return $converted_pass == $remote_pass ? true : false;
    }

    private function genPassword($pass)
    {
        /* function to encrypt a password before verification */
        $length   = 32;
        $salt     = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $len      = strlen($salt);
        $makepass = '';
        mt_srand(10000000 * (double) microtime());

        for ($i = 0; $i < $length; $i++) {
            $makepass .= $salt[mt_rand(0, $len - 1)];
        }

        return (md5(stripslashes($pass) . $makepass) . ':' . $makepass);
    }
}
