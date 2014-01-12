<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Controller Library
 * 
 * PHP version 5.4+
 * 
 * LICENSE: Licensed by AT&T under the 'Software Development Kit Tools 
 * Agreement.' 2013. 
 * TERMS AND CONDITIONS FOR USE, REPRODUCTION, AND DISTRIBUTIONS:
 * http://developer.att.com/sdk_agreement/
 *
 * Copyright 2013 AT&T Intellectual Property. All rights reserved.
 * For more information contact developer.support@att.com
 * 
 * @category  MVC 
 * @package   Controller 
 * @author    Pavel Kazakov <pk9069@att.com>
 * @copyright 2013 AT&T Intellectual Property
 * @license   http://developer.att.com/sdk_agreement AT&T License
 * @link      http://developer.att.com
 */

require_once dirname(__FILE__) . '../../OAuth/OAuthTokenService.php';
require_once dirname(__FILE__) . '../../OAuth/OAuthCodeRequest.php';
require_once dirname(__FILE__) . '../../Srvc/Service.php';

/**
 * Base class used to implement an MVC controller. 
 * 
 * This implementation is a very rough implementation of MVC and not a full 
 * one. For simplicity and minimization, a full MVC framework is not used. 
 * Furthemore, this controller definition is specific to handling APIs.
 *
 * @category MVC 
 * @package  Controller 
 * @author   Pavel Kazakov <pk9069@att.com>
 * @license  http://developer.att.com/sdk_agreement AT&T License
 * @version  Release: @package_version@ 
 * @link     http://developer.att.com
 */
abstract class APIController
{

    /**
     * Fully qualified domain name.
     *
     * @var string
     */
    protected $FQDN;

    /**
     * Client id used for authenticaton.
     *
     * @var string
     */
    protected $clientId;
    /**
     * Client secret used for authenticaton.
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * Scope used for authenticaton.
     */
    protected $scope;

    /**
     * Results stored after API calls.
     *
     * @var array
     */
    protected $results;

    /**
     * Errors stored after API calls.
     *
     * @var array
     */
    protected $errors;

    /** 
     * Gets an access token that will be cached using a session. 
     *
     * This method works first trying to load the token from the user's 
     * session, and, if a saved OAuth token isn't found, this method will send 
     * an API request. The OAuth token will then be saved in session for future
     * use.
     *
     * @return OAuthToken OAuth token that can be used for authorization
     * @throws OAuthException if API request was not successful or if 
     *                        there was a session issue  
     */
    protected function getSessionToken() 
    {
        // Try loading token from session 
        $token = isset($_SESSION['token']) ?
            unserialize($_SESSION['token']) : null;

        // load redirect URL 
        include dirname(__FILE__) . '/../../config.php';

        // No token or token is expired... send token request
        if (!$token || $token->isAccessTokenExpired()) {
            $codeURL = $this->FQDN . '/oauth/authorize';
            $codeRequest = new OAuthCodeRequest(
                $codeURL, $this->clientId, $this->scope, 
                $authorize_redirect_uri
            );
            $code = $codeRequest->getCode();

            $tokenSrvc = new OAuthTokenService(
                $this->FQDN, $this->clientId, $this->clientSecret
            );
            $token = $tokenSrvc->getTokenUsingCode($code);
            $_SESSION['token'] = serialize($token);
        }

        return $token;
    }

    /** 
     * Gets an access token that will be cached using a file. 
     *
     * This method works first trying to load the file specified in config, 
     * and, if a saved OAuth token isn't found, this method will send an API 
     * request. The OAuth token will then be saved for future use.
     *
     * @return OAuthToken OAuth token that can be used for authorization
     * @throws OAuthException if API request was not successful or if 
     *                        there was a file IO issue
     */
    protected function getFileToken() 
    {
        // load location where to save token 
        include dirname(__FILE__) . '/../../config.php';

        if (!isset($oauth_file)) {
            // set default if can't load
            $oauth_file = 'token.php';
        }

        $token = OAuthToken::loadToken($oauth_file);
        if ($token == null || $token->isAccessTokenExpired()) {
            $tokenSrvc = new OAuthTokenService(
                $this->FQDN, 
                $this->clientId,
                $this->clientSecret
            );
            $token = $tokenSrvc->getTokenUsingScope($this->scope);
            // save token for future use
            $token->saveToken($oauth_file);
        }

        return $token;
    }

    /**
     * Given an array of names, this method will copy the named values from 
     * request parameters to session.
     *
     * @param array $vnames list of variable names
     *
     * @return void
     */
    protected function copyToSession($vnames) 
    {
        foreach ($vnames as $vname) {
            if (isset($_REQUEST[$vname])) {
                $_SESSION[$vname] = $_REQUEST[$vname];
            }
        }
    }

    /**
     * Unsets the session for the specified names.
     *
     * @param array $vnames list of variable names
     *
     * @return void
     */
    protected function clearSession($vnames) 
    {
        foreach ($vnames as $vname) {
            unset($_SESSION[$vname]);
        }
    }

    /**
     * Initializes common information for a Controller object.
     */
    protected function __construct() 
    {
        // Copy config values to member variables
        include dirname(__FILE__) . '/../../config.php';
        $this->FQDN = $FQDN;
        $this->clientId = $api_key;
        $this->clientSecret = $secret_key;
        $this->scope = $scope;

        $this->results = array();
        $this->errors = array();

        // set any RESTFul environmental settings
        $proxyHost = isset($proxy_host) ? $proxy_host : null;
        $proxyPort = isset($proxy_port) ? $proxy_port : -1;
        $trustAllCerts = isset($accept_all_certs) ? $accept_all_certs : false;
        RESTFulRequest::setDefaultProxy($proxyHost, $proxyPort);
        RESTFulRequest::setDefaultAcceptAllCerts($trustAllCerts);
    }

    /**
     * Handles the http request sent to this server, such as a form submission.
     * If the http request has a form submission or other case where an API 
     * call is made, results and errors of this call MUST be stored in:
     * <code>
     * $this->results
     * $this->errors
     * </code>
     *
     * @return void
     */
    public abstract function handleRequest();

    /**
     * Gets any API results to be displayed in the view.
     *
     * handleRequest() MUST be called before this method is called.
     *
     * @return array results or an empty array if no results.
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Gets any errors that occured as a result of API requests.
     *
     * handleRequest() MUST be called before this method is called.
     *
     * @return array errors or an empty array if no errors.
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
?>
