[18:41, 4/1/2020] Felipe: <?php
 
namespace DesarrolloHosting\YubikeyLoginBundle\Security\RolesProcessor;
 
use DesarrolloHosting\YubikeyLoginBundle\Security\Exception\RolesProcessorException;
 
class BinaryRolesProcessor implements RolesProcessorInterface {
 
    private $positive_end;
    private $negative_end;
    
    /**
     * 
     * @param type $parameters expects an array with keys 'allow' and 'deny' 
     * @throws RolesProcessorException
     */
    function __construct($parameters) {
        
        if(!isset($parameters['allow']) || !isset($parameters['deny'])){
            throw new RolesProcessorException("Invalid role processor configuration. It should have params 'allow' and 'deny'");
        }
        $this->positive_end = "_" . strtoupper($parameters['allow']);
        $this->negative_end = "_" . strtoupper($parameters['deny']);
    }
 
    function process(array $roles) {
        $corrected_roles = array();
        foreach ($roles as $role) {
            if ($this->endsWith($role, $this->negative_end)) {
                continue;
            }
            if ($this->endsWith($role, $this->positive_end)) {
                $role = $this->removeEnding($role, $this->positive_end);
            }
            else{
                throw new RolesProcessorException("Invalid role value");
            }
            $corrected_roles[] = $role;
        }
        return $corrected_roles;
    }
 
    private function endsWith($string, $end) {
        $strlen = strlen($string);
        $end_length = strlen($end);
        if ($end_length > $strlen) {
            return false;
        }
        return substr_compare($string, $end, $strlen - $end_length, $end_length) === 0;
    }
 
    private function removeEnding($string, $end) {
        return substr($string, 0, -strlen($end));
    }
 
}