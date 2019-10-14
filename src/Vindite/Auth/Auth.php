<?php
/**
 * @copyright   2019 - Vindite
 * @author      Vinicius Oliveira <vinicius_o.a@live.com>
 * @category    Micro Framework
 * @since       2019-10-14
 */

namespace Vindite\Auth;

use Vindite\App\AppCreator;
use Psr\Http\Message\ServerRequestInterface;
use Vindite\Config\ConfigConstant;
use Vindite\Session\Session;
/**
 * Trata as solicitaçoes de autenticacao do framework
 */
class Auth
{
    use \Vindite\Config\ConfigAwareTrait;

    /**
     * Guarda o objeto de sessão
     *
     * @var Session
     */
    protected $session;

    /**
     * Guarda os dados do user
     *
     * @var mixed
     */
    protected $user;

    /**
     * Constructor
     *
     * @param Session $session
     * @return void
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
        AppCreator::container('auth_table')->set(
            AuthGateway::class
        );
    }

    /**
     * Seta os dados da requisição de autenticacao
     *
     * @param ServerRequestInterface $request
     * @return self
     */
    public function setRequest(ServerRequestInterface $request) : self
    {
        $this->request = $request;
        return $this;
    }

   /**
    * Registra o usuário
    *
    * @param string $email
    * @param string $password
    * @return bool
    */
    public function registerUser(string $email, string $password) : bool
    {
        $storeInDatabase = \sodium_crypto_pwhash_str(
            $password,
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
        );

        $authGateway = AppCreator::container()->get('auth_table');
        return (bool) $authGateway->registerUser($email, $storeInDatabase);
    }

    /**
     * Autentica user
     *
     * @param string $email
     * @param string $password
     * @return boolean
     */
    public function authenticate(string $email, string $password) : bool
    {
        $storedPassword = $this->generateFakePassword($password);
        $this->user = $this->findByEmail($email);

        if ($this->user) {
            $storedPassword = $this->user[0]["password"];
        }

        return (bool) ($this->verifyPassword($password, $storedPassword)) && ($this->user !== null);
    }

    /**
     * Find user by email
     *
     * @param string $email
     * @return array
     */
    public function findByEmail(string $email) : array
    {
        $authGateway = AppCreator::container()->get('auth_table');
        return $authGateway->findByEmail($email);
    }

    /**
     * Generate fake password
     *
     * @return string
     */
    public function generateFakePassword($password) : string
    {
        $genericSecretKey = random_bytes(32);
        return sodium_crypto_generichash($password, $genericSecretKey);
    }

    /**
     * Verifica o password
     *
     * @param string $password
     * @param string $storedPassword
     * @return boolean
     */
    public function verifyPassword(string $password, string $storedPassword) : bool
    {
        /* Once that's stored, you can just test against the hash like so: */
        if (\sodium_crypto_pwhash_str_verify($storedPassword, $password)) {

            /**
             * @todo setar os dados do usuario na sessao
             * @todo inicar a sessão
             * @todo inserir um registro na tabela de sessão
             */
            /* Logged in! */

            echo '<pre>';
            var_dump ($this->user);
            die('e');
            return true;
        }

        /* Incorrect password. */
        return false;
    }

    /**
     * Verifica se o user está autenticado
     *
     * @return boolean
     */
    public function isAuthenticated() : bool
    {
        if (!$this->session->hasSession()) {
            return false;
        }

        if ($this->session->shouldRegenerateSessionId()) {
            $this->session->regenerateSessionId();
        }

        return true;
    }

    /**
     * Retorna a página de login
     *
     * @return string
     */
    public function redirectToLoginPage() : string
    {
        $config = $this->loadConfig(ConfigConstant::AUTH);
        return $config[ConfigConstant::AUTH_LOGIN_URL];
    }
}