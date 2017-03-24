<?php
namespace EPBOutage\MainBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;

class ContactModel
{
   
    /**
     * @Assert\Length(
     *      min=3,
     *      max=60
     * )
     * @Assert\NotBlank()
     */
    protected $name;
    
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
    * @Assert\Length(
    *       min=10,
    *       minMessage = "Message must be atleast 10 characters long."
    * )
    */
    protected $message;
    
    /**
    * @Recaptcha\IsTrue
    */
    protected $recaptcha;


    public function setName($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setEmail($email) {
        $this->email = $email;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function setMessage($message) {
        $this->message = $message;
    }
    
    public function getMessage() {
        return $this->message;
    }
    
    public function getRecaptcha() {
        return $this->recaptcha;
    }
    
    public function setRecaptcha($recaptcha) {
        $this->recaptcha = $recaptcha;
    }
}