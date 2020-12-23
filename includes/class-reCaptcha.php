<?php 
namespace WURE;

class RECAPTCHA{
    public $log = '';
    public $errors = '';

    function __construct($log, $errors){
        $this->log = $log;
        $this->errors = $errors;
    }
    
    public function validate(){
        if(get_option( 'recaptcha_secret_key' ) && get_option( 'recaptcha_secret_key' )):
            $url = sprintf( "https://www.google.com/recaptcha/api/siteverify?secret=%s&response=%s&remoteip=%s", 
                urlencode(get_option( 'recaptcha_secret_key' )), 
                urlencode($_POST['g-recaptcha-response']), 
                urlencode($_SERVER['REMOTE_ADDR'])
            );

            $result = json_decode(file_get_contents($url), TRUE);

            if( $result['success'] == 1 ):
                $this->log->reCaptcha('approved');
                return true;
            else:
                $this->reCaptchaErrorsLog($result['error-codes'][0]);
                return false;
            endif;
        else:
            $this->reCaptchaErrorsLog('The key is not configured.');
            return false;
        endif;
    }


    protected function reCaptchaErrorsLog($message){
        $this->log->reCaptcha($message);
        $this->log->submittedData($_POST);//for debug
        $this->errors->addFormError('reCaptcha - Try again');
        $this->errors->writeError();
    }
    
}