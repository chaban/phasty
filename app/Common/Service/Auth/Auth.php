<?php namespace Phasty\Common\Service\Auth;

use Phalcon\Http\Client\Exception;
use Phalcon\Mvc\User\Component,
    Phasty\Common\Models\Users,
    Phasty\Common\Models\RememberTokens,
    Phasty\Common\Models\SuccessLogins,
    Phasty\Common\Models\FailedLogins;

/**
 * Shop\Auth\Auth
 *
 * Manages Authentication/Identity Management in Shop
 */
class Auth extends Component
{

    /**
     * Checks the user credentials
     *
     * @param array $credentials
     * @return boolan
     */
    public function check($credentials)
    {
        //Check if the user exist
        $user = Users::findFirstByEmail($credentials['email']);
        if (!$user) {
            $this->registerUserThrottling(0);
            return $this->response->redirect('session/login');
        }

        //Check the password
        if (!$this->security->checkHash($credentials['password'], $user->password)) {
            $this->registerUserThrottling($user->id);
            return $this->response->redirect('session/login');
        }

        //Check if the user was flagged
        if(!$this->checkUserFlags($user)){
            return $this->response->redirect('session/login');
        }

        //Register the successful login
        $this->saveSuccessLogin($user);

        //Check if the remember me was selected
        if (isset($credentials['remember'])) {
            $this->createRememberEnviroment($user);
        }

        $this->session->set('auth', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role
            ]);
        $user->profile->update();
        $this->response->redirect('index');
    }

    /**
     * Creates the remember me environment settings the related cookies and generating tokens
     *
     * @param Users $user
     * @throws Exception
     */
    public function saveSuccessLogin(Users $user)
    {
        $successLogin = new SuccessLogins();
        $successLogin->usersId = $user->id;
        $successLogin->ipAddress = $this->request->getClientAddress();
        $successLogin->userAgent = $this->request->getUserAgent();
        if (!$successLogin->save()) {
            $messages = $successLogin->getMessages();
            throw new Exception($messages[0]);
        }
    }

    /**
     * Implements login throttling
     * Reduces the efectiveness of brute force attacks
     *
     * @param int $userId
     */
    public function registerUserThrottling($userId)
    {
        $failedLogin = new FailedLogins();
        $failedLogin->usersId = $userId;
        $failedLogin->ipAddress = $this->request->getClientAddress();
        $failedLogin->attempted = time();
        $failedLogin->save();

        $attempts = FailedLogins::count(array('ipAddress = ?0 AND attempted >= ?1', 'bind' => array($this->request->
        getClientAddress(), time() - 3600 * 6)));

        switch ($attempts) {
            case 1:
            case 2:
                // no delay
                break;
            case 3:
            case 4:
                sleep(2);
                break;
            default:
                sleep(4);
                break;
        }

    }

    /**
     * Creates the remember me environment settings the related cookies and generating tokens
     *
     * @param Users $user
     */
    public function createRememberEnviroment(Users $user)
    {

        $userAgent = $this->request->getUserAgent();
        $token = md5($user->email . $user->password . $userAgent);

        $remember = new RememberTokens();
        $remember->usersId = $user->id;
        $remember->token = $token;
        $remember->userAgent = $userAgent;

        if ($remember->save() != false) {
            $expire = time() + 86400 * 8;
            $this->cookies->set('RMU', $user->id, (int)$expire);
            $this->cookies->set('RMT', $token, (int)$expire);
        } else {
            $this->flash->error($remember->getMessages());
        }

    }

    /**
     * Check if the session has a remember me cookie
     *
     * @return boolean
     */
    public function hasRememberMe()
    {
        return $this->cookies->has('RMU');
    }

    /**
     * Logs on using the information in the coookies
     *
     * @return \Phalcon\Http\Response
     */
    public function loginWithRememberMe()
    {
        $userId = $this->cookies->get('RMU')->getValue();
        $cookieToken = $this->cookies->get('RMT')->getValue();

        $user = Users::findFirstById($userId);
        if ($user) {

            $userAgent = $this->request->getUserAgent();
            $token = md5($user->email . $user->password . $userAgent);

            if ($cookieToken == $token) {

                $remember = RememberTokens::findFirst(['usersId = ?0 AND token = ?1', 'bind' => [$user->id, $token]]);
                if ($remember) {

                    //Check if the cookie has not expired
                    if ((time() - (86400 * 8)) < $remember->createdAt) {

                        //Check if the user was flagged
                        if(!$this->checkUserFlags($user)){
                            return $this->response->redirect('session/login');
                        }

                        //Register identity
                        $this->session->set('auth', [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role
                        ]);

                        //Register the successful login
                        $this->saveSuccessLogin($user);

                        return $this->response->redirect('index');
                    }
                }

            }

        }

        $this->cookies->get('RMU')->delete();
        $this->cookies->get('RMT')->delete();

        return $this->response->redirect('session/login');
    }

    /**
     * Checks if the user is banned/inactive/suspended
     *
     * @param Users $user
     * @return boolean
     */
    public function checkUserFlags(Users $user)
    {
        if ($user->confirmed <> 'Y') {
            $this->flashSession->error('You not confirmed yet. Please check your email and confirm registration');
            return false;
        }

        if ($user->banned <> 'N') {
            $this->flashSession->error('You canot be loged in. you are banned!!!');
            return false;
        }
        return true;
    }

    /**
     * Returns the current identity
     *
     * @return array
     */
    public function getIdentity()
    {
        return $this->session->get('auth');
    }

    /**
     * Returns the current user name
     *
     * @return string
     */
    public function getName()
    {
        $identity = $this->getIdentity();
        if ($identity) {
            return $identity['name'];
        } else {
            return false;
        }
    }

    /**
     * Returns the current user email
     *
     * @return string
     */
    public function getEmail()
    {
        $identity = $this->getIdentity();
        if ($identity) {
            return $identity['email'];
        } else {
            return false;
        }
    }

    public function getRole()
    {
        $identity = $this->getIdentity();
        if ($identity) {
            return $identity['role'];
        } else {
            return false;
        }
    }

    public function getId()
    {
        $identity = $this->getIdentity();
        if ($identity) {
            return $identity['id'];
        } else {
            return false;
        }
    }

    /**
     * Removes the user identity information from session
     */
    public function remove()
    {
        if ($this->cookies->has('RMU')) {
            $this->cookies->get('RMU')->delete();
        }
        if ($this->cookies->has('RMT')) {
            $this->cookies->get('RMT')->delete();
        }

        $this->session->remove('auth');
    }

    /**
     * Auths the user by his/her id
     *
     * @param int $id
     * @return \Phalcon\Http\Response
     */
    public function authUserById($id)
    {
        $user = Users::findFirstById($id);
        if (!$user) {
            $this->flashSession->error('There is no such a user');
            return $this->response->redirect('session/login');
        }

        if(!$this->checkUserFlags($user)){
            return $this->response->redirect('session/login');
        }

        $this->session->set('auth', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role
        ]);

    }

    /**
     * Get the entity related to user in the active identity
     *
     * @return Users
     */
    public function getUser()
    {
        $identity = $this->session->get('auth');
        if (isset($identity['id'])) {

            $user = Users::findFirstById($identity['id']);
            if (!$user) {
                return false;
            }

            return $user;
        }

        return false;
    }

    /**
     * Create token for json web token identification
     * @return string
     */

    public function getJwtToken()
    {
        $id = $this->getId();
        $now = strtotime('now');
        $token = [
            "iss" => $this->config->app->publicUrl,//The issuer of the token (defaults to the request url)
            "sub" => $id,//This holds the identifier for the token (defaults to user id)
            "iat" => $now,// When the token was issued (unix timestamp)
            //The token expiry date (unix timestamp)  Now + one day
            "exp" => strtotime('+1 day'),
            //The earliest point in time that the token can be used (unix timestamp)
            "nbf" => strtotime("+60 seconds"),
            "jti" => md5($now.$id)//A unique identifier for the token (md5 of the sub and iat claims)
        ];

        return \JWT::encode($token, $this->config->app->jwtSecret);
    }

    public function checkJwtToken($token = null){
        $decoded_token = null;
        try {
            $decoded_token = \JWT::decode($token, $this->config->app->jwtSecret, array($this->config->app->jwtAlgorithm));
        } catch (Exception $e) {
            return false;
        }
        return $decoded_token;
    }

}
