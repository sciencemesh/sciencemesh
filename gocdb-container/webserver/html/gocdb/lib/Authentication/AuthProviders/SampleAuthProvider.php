<?php
namespace org\gocdb\security\authentication;
include_once __DIR__.'/../_autoload.php';
//require_once __DIR__.'/../IAuthenticationProvider.php';
//require_once __DIR__.'/../IAuthentication.php';
//require_once __DIR__.'/../Exceptions/AuthenticationException.php';
//require_once __DIR__.'/../Exceptions/BadCredentialsException.php';
//require_once __DIR__.'/../IUserDetails.php';
//require_once __DIR__.'/../ApplicationSecurityConfigService.php';
//require_once __DIR__.'/../AuthTokens/UsernamePasswordAuthenticationToken.php';
//require_once __DIR__.'/../UserDetails/GOCDBUserDetails.php';


/**
 * Sample IAuthenticationProvider implementation that will authenticate any user
 * whose username and password are the same. It assigns a single role to every user
 * (ROLE_REGISTERED_USER).
 * <p>
 * It requires a <code>UsernamePasswordAuthenticationToken</code> as the given IAuth token.
 *
 * @author David Meredith
 */
class SampleAuthProvider implements IAuthenticationProvider{

    public function authenticate(IAuthentication $auth) {
        if($auth == null){
            throw new BadCredentialsException(null, 'Bad credentials - null given');
        }
        if(!($auth instanceof UsernamePasswordAuthenticationToken)){
            throw new BadCredentialsException(null, 'Bad credentials - expected UsernamePasswordAuthenticationToken');
        }

        // Normally we would use a IUserDetailsService implementation here to
        // lookup our given user against the local credential store, we don't
        // need to as we are simply authenticating users if un==pw.

        if(true){
            $roles = array();
            $roles[] = 'ROLE_REGISTERED_USER';
            $auth->setAuthorities($roles);
            $userDetails = new GOCDBUserDetails($auth->getPrinciple(), true, $roles, 'customVal2', '');
            $auth->setDetails($userDetails);
            return $auth;
        }

        // We didn't manage to authenticate the user, so throw exception
        throw new AuthenticationException(null, 'Authentication failed');
    }

    public function supports(IAuthentication $auth){
        if($auth instanceof UsernamePasswordAuthenticationToken){
            return true;
        }
        return false;
    }
}

?>
