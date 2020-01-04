YubikeyLoginBundle
This bundle allows you to use just a Yubikey for user authentication.

Installation
Enable the Bundle in your AppKernel.php

public function registerBundles() { $bundles = [ // ... new DesarrolloHosting\YubikeyLoginBundle\DesarrolloHostingYubikeyLoginBundle(), ];

 // ...
}

Import the Bundle routes in your app (routing.yml)

desarrollo_hosting_yubikey_login: resource: "@DesarrolloHostingYubikeyLoginBundle/Resources/config/routing.yml"

The system provides a YubikeyUser which has the following properties:

yubikey_id
full_name
roles
You can extend the YubikeyUser in case you need extra properties in it. In this case you need to create an User and an UserProvider:

#################################### ########### UserProvider ########### ####################################

em = $entityManager; } public function loadUserByYubikeyId($yubikey_id) { $yubikey_user = parent::loadUserByYubikeyId($yubikey_id); $sysadmin = $this->em->getRepository("AppBundle:Sysadmin")->findOneByFullName($yubikey_user->getFullName()); return new OperacionesUser($yubikey_user->getYubikeyId(), $yubikey_user->getFullName(), $yubikey_user->getRoles(), $sysadmin); } } #################################### ############### User ############### #################################### sysadmin = $sysadmin; } public function getSysadmin(){ return $this->sysadmin; } public function isSysadmin(){ return $this->sysadmin !== null; } }
In order to use the new User created at point 3, define your user provider as a child service in services.yml

services: operaciones_user_provider: parent: yubikey_user_provider class: AppBundle\Security\User\OperacionesUserProvider

Edit your security settings security.yml:

providers: yubikey: id: operaciones_user_provider

firewalls: # disables authentication for assets and the profiler, adapt it according to your needs dev: pattern: ^/(_(profiler|wdt)|css|images|js)/ security: false

 secured_area:
     pattern: ^/
     simple_form:
         authenticator: yubikey_authenticator
         password_parameter: yubikey_otp
         login_path: desarrollo_hosting_yubikey_login_login
         check_path: desarrollo_hosting_yubikey_login_login
     logout:
         path:   desarrollo_hosting_yubikey_login_logout
         target: /
     anonymous: ~
access_control: - { path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY } - { path: ^/public_page, roles: IS_AUTHENTICATED_ANONYMOUSLY } - { path: ^/, roles: ROLE_USER }

Configure the Bundle with the parameteres you need in config.yml

If you plan to use the included login view, you need to define in the congifuration file where the needed assets are located. In case you want to use the default CSS, JS and logo remember to install the assets with "php bin/console assets:install --symlink" (composer usually does this automatically).

Configuration
You can configure the bundle adding this parameters in your config.yml file:

yubikey_login: user_provider: "" system_name: Monitoreo background_color: "" # Optional. Background color of the login page. Accepts any valid CSS color, if none is given it uses teh defined in the CSS file (#3f51b5) roles_processor: # Optional. See the Roles section name: dummy params: ~ base_role: ROLE_USER # Optional. database: host: "" user: "" pass: "" name: "" yubikey: api_key: "" client_id: "" redirect_login: # Optional. Where to redirect if an authenticated user visits the /login type: "url" # Optional destination: ~ # Optional. Redirect to the root folder of the app assets: css: 'bundles/desarrollohostingyubikeylogin/css/style.css' js: 'bundles/desarrollohostingyubikeylogin/js/action.css' logo: 'bundles/desarrollohostingyubikeylogin/images/logo.css'

Translation
YubikeyLogin Bundle comes with custom text for the "es" and "en" locales. If it is not working check if the translator is enabled in your config.yml: framework: translator: { fallbacks: ["%locale%"] }

Commands
YubikeyLogin Bundle has the following commands available:

php bin/console yubikey_login:user Prints the full name of the user corresponding to the parameter.

php bin/console yubikey_login:roles Prints roles of the user corresponding to the parameter for the system.

Roles
After a succesfull log in the bundle will give the user the role ROLE_USER. In addition, for each variable dedfined in the database the bundle will add a new role in the format ROLE_{VARIABLENAME}_{VARIABLE_VALUE}. After this roles are retrieved, YubikeyLogin will call the RoleProcessor defined using the yubikey_config.roles_processor.name configuration value. If any params are set in yubikey_config.roles_processor.params, the constructor of the RoleProcessor will receive an array with this params. Please read below to check which RoleProcessors require the params to be set and which not.

The bundle comes with the following RolesProcessors: 1. dummy: keeps the roles unchanged (no params needed) 2. binary: any role finished with _ will be mantained (without the _ text), and roles with _ will be excluded (requires params with key 'allow' and 'deny') 3. single: removes the {VARIABLENAME} in the roles in case there is only one, in addition to ROLE_USER (no params needed)

If your roles have certain hierarchy you must define it manually in your security.yml (http://symfony.com/doc/current/book/security.html#hierarchical-roles). Remember that the roles you use in your hierarchy must match the ones returned by the RoleProcessor you use.