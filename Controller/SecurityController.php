<?php
 
namespace DesarrolloHosting\YubikeyLoginBundle\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
 
class SecurityController extends Controller {
 
    public function loginAction(Request $request) {
        
        //Redirect if already logged in
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $destination = $this->getParameter('yubikey_login.redirect_login.destination');
            switch($param = $this->getParameter('yubikey_login.redirect_login.type')){
                case 'url':
                    return $this->redirect($destination ?: $this->get('router')->getContext()->getBaseUrl());
                case 'route':
                    return $this->redirectToRoute($destination ?: 'homepage', array());
            }
        }
        
        $authenticationUtils = $this->get('security.authentication_utils');
        
        return $this->render(
                'DesarrolloHostingYubikeyLoginBundle:Security:login.html.twig', array(
                    'error' => $authenticationUtils->getLastAuthenticationError(),
                    'system_name' => $this->getParameter('yubikey_login.system_name'),
                    'background_color' => $this->getParameter('yubikey_login.background_color'),
                    'assets' => array(
                        'css' => $this->getParameter('yubikey_login.assets.css'),
                        'js' => $this->getParameter('yubikey_login.assets.js'),
                        'logo' => $this->getParameter('yubikey_login.assets.logo'),
                    ),
                )
        );
    }
 
}