<?php
 
namespace DesarrolloHosting\YubikeyLoginBundle\Security;
 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;
use Yubikey\Validate;
use DesarrolloHosting\YubikeyLoginBundle\Security\Exception\RolesProcessorException;
use DesarrolloHosting\YubikeyLoginBundle\Security\Exception\SystemNotFoundException;
 
class YubikeyAuthenticator implements SimpleFormAuthenticatorInterface {
 
    private $api_key;
    private $client_id;
 
    public function __construct($api_key, $client_id) {
        $this->api_key = $api_key;
        $this->client_id = $client_id;
    }
 
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey) {
        $validator = new Validate($this->api_key, $this->client_id);
        $validator->setUseSecure(true);
 
        $credentials = strtolower($token->getCredentials());
        
        try {
            $response = $validator->check($credentials);
        } catch (\InvalidArgumentException $e) {
            throw new CustomUserMessageAuthenticationException('yubikey_validate_error.invalid_otp_length');
        }
 
        if ($response->success() === true) {
            try {
                $user = $userProvider->loadUserByOTP($credentials);
            } catch (SystemNotFoundException $e) {
                throw new CustomUserMessageAuthenticationException('system_not_found', array('%system_name%' => $e->getMessage()));
            } catch (UsernameNotFoundException $e) {
                throw new CustomUserMessageAuthenticationException('user_not_found', array('%yubikey%' => $e->getMessage()));
            } catch (AccessDeniedException $e) {
                throw new CustomUserMessageAuthenticationException('user_not_allowed', array('%user_name%' => $e->getMessage()));
            } catch (RolesProcessorException $e) {
                throw new CustomUserMessageAuthenticationException('role_processor_error', array('%error_message%' => $e->getMessage()));
            } catch (\Exception $e){
                throw new CustomUserMessageAuthenticationException('generic_error', array('%error_message%' => $e->getMessage()));
            }
            return new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
        }
 
        throw new CustomUserMessageAuthenticationException('yubikey_validate_error.' . strtolower($response->current()->status));
    }
 
    public function supportsToken(TokenInterface $token, $providerKey) {
        return $token instanceof UsernamePasswordToken && $token->getProviderKey() === $providerKey;
    }
 
    public function createToken(Request $request, $username, $password, $providerKey) {
        return new UsernamePasswordToken($username, $password, $providerKey);
    } 
}