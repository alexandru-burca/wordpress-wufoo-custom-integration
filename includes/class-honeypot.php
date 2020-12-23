<?php
namespace WURE;

class HONEYPOT{
    public $log = '';
    public $errors = '';
    private $honeypot = 'first-name';

    function __construct($log, $errors){
        $this->log = $log;
        $this->errors = $errors;
    }

    public function validate(){
        if(isset($_POST[$this->honeypot]) && !empty($_POST[$this->honeypot])):
            $this->log->honeypot();
            $this->log->submittedData($_POST);//for debug
            $this->errors->addFormError("Something went wrong, try again later.");
            $this->errors->writeError();
            return false;
        else:
            return true;
        endif;
    }
}