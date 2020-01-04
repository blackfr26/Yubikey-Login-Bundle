<?php
 
namespace DesarrolloHosting\YubikeyLoginBundle\Command;
 
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
 
class YubikeyUserCommand extends ContainerAwareCommand {
 
    protected $yubikey_user_provider;
 
    public function __construct($yubikey_user_provider) {
        $this->yubikey_user_provider = $yubikey_user_provider;
        parent::__construct();
    }
 
    protected function configure() {
        // try to avoid work here (e.g. database query)
        // this method is always called - see warning below
        $this
                ->setName('yubikey_login:user')
                ->setDescription('Get the username given the yubikey OTP')
                ->addArgument(
                        'yubikey', InputArgument::REQUIRED, 'OTP'
                )
        ;
    }
 
    protected function execute(InputInterface $input, OutputInterface $output) {
        $otp = $input->getArgument('yubikey');
        $yubikey_user = $this->yubikey_user_provider->loadUserByOTP($otp);
        $output->writeln($yubikey_user->getFullName());
    }
 
}