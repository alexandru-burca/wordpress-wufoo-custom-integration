<?php
namespace WURE;

class NONCE{
    public $log = '';
    public $errors = '';

    function __construct($log, $errors){
        $this->log = $log;
        $this->errors = $errors;
    }

    public function validate(){
        if(!isset($_POST['wurenonce']) || !\wp_verify_nonce($_POST['wurenonce'], 'wuresecretaction')):
            $this->log->nounce();
            $this->log->submittedData($_POST);//for debug
            $this->errors->addFormError("Nonce not set");
            $this->errors->writeError();
            return false;
        else:
            return true;
        endif;
    }
}