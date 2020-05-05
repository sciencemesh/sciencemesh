<?php

/*
 * Copyright (C) 2012 STFC
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace org\gocdb\security\authentication;

/**
 * A stateless configuration that supports the specified providers, tokens and
 * user details service.
 *
 * @see IConfigFirewallComponent
 * @author David Meredith
 */
class MyConfig1 implements IConfigFirewallComponent {
    private $providerList;
    private $gocdbUserDetailsService;
    private $tokenClassList;

    function __construct() {
       $this->providerList = array();
       $this->providerList[] = new GOCDBAuthProvider();
       //$this->providerList[] = new SampleAuthProvider();

       $this->gocdbUserDetailsService = new GOCDBUserDetailsService();

       $this->tokenClassList = array();
       //$this->tokenClassList[] = 'org\gocdb\security\authentication\X509AuthenticationToken';
       //$this->tokenClassList[] = 'org\gocdb\security\authentication\ShibAuthToken';
       //$this->tokenClassList[] = 'org\gocdb\security\authentication\SimpleSamlPhpAuthToken';
       $this->tokenClassList[] = 'org\gocdb\security\authentication\UsernamePasswordAuthenticationToken';
    }

    /**
     * Get an array containing <codeGOCDBAuthProvider</code> as the first element.
     * @see IConfigFirewallComponent::getAuthProviders()
     * @return
     */
    public function getAuthProviders(){
        return $this->providerList;
    }

    /**
     * @see IConfigFirewallComponent::getUserDetailsService()
     * @return \org\gocdb\security\authentication\GOCDBUserDetailsService
     */
    public function getUserDetailsService(){
        return $this->gocdbUserDetailsService;
    }

    /**
     * Get the supported auth token class names as strings.
     * <pre>
     * [0] = 'org\gocdb\security\authentication\X509AuthenticationToken'
     * [1] = 'org\gocdb\security\authentication\SimpleSamlPhpAuthToken'
     * </pre>
     * @see IConfigFirewallComponent::getAuthTokenClassList()
     * @return array
     */
    public function getAuthTokenClassList(){
        return $this->tokenClassList;
    }

    /**
     * @see IConfigFirewallComponent::getCreateSession()
     * @return false
     */
    public function getCreateSession(){
        return true;
    }
}
