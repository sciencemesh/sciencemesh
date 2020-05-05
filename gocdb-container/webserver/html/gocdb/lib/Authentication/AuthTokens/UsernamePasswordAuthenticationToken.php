<?php
namespace org\gocdb\security\authentication;
require_once __DIR__.'/../IAuthentication.php';

/**
 * Description of UsernamePasswordAuthenticationToken
 *
 * @author David Meredith
 */
class UsernamePasswordAuthenticationToken implements IAuthentication {
    private $userDetails;
    private $username;
    private $password;
    private $authorities;
    private $initialUsername;

    public function __construct($username = "admin", $password = "admin") {
        $this->username = $username;
        $this->initialUsername = $username;
        $this->password = $password;
    }

    public static function isStateless() {
        return true;
    }

    public static function isPreAuthenticating() {
        return true;
    }

    public function getCredentials() {
      return $this->password;
    }

    public function getDetails() {
      return $this->userDetails;
    }

    public function getPrinciple() {
       return $this->username;
    }
    public function setPrinciple($username){
        $this->username = $username;
    }

    public function setDetails($userDetails) {
        $this->userDetails = $userDetails;
    }

    public function eraseCredentials() {
        $this->password = null;
    }

    public function getAuthorities(){
       return $this->authorities;
    }

    public function setAuthorities($authorities) {
        $this->authorities = $authorities;
    }

    public function validate(){
       if($this->getPrinciple() != $this->initialUsername){
           throw new AuthenticationException('Invalid state, principle does not equal initial username');
       }
    }
}

?>
