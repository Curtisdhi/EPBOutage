<?php
namespace EPBOutage\MainBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;

class AlertUserModel
{
   
    /**
    * @Assert\Email(
    *     message = "The email '{{ value }}' is not a valid email.",
    *     checkMX = true
    * )
    * @Assert\NotBlank()
    */
    protected $email;
    
    /**
    * @Assert\NotBlank()
    * @Assert\GreaterThanOrEqual(100)
    */
    protected $customersAffectedThreshold = 1000;
    
    /**
    * @Recaptcha\IsTrue
    */
    protected $recaptcha;

    public function setEmail($email) {
        $this->email = $email;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    function getCustomersAffectedThreshold() {
        return $this->customersAffectedThreshold;
    }

    function setCustomersAffectedThreshold($customersAffectedThreshold) {
        $this->customersAffectedThreshold = $customersAffectedThreshold;
    }

    public function getRecaptcha() {
        return $this->recaptcha;
    }
    
    public function setRecaptcha($recaptcha) {
        $this->recaptcha = $recaptcha;
    }
}