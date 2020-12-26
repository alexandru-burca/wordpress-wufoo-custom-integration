<?php

namespace WURE;

class LOGS{
    protected $insertData = [
        'reCaptcha' => null,
        'wufoo' => null,
    ];

    function __construct(){
        $this->setTime();
        $this->setIP();
        $this->setReferrer();
        $this->setBrowser();
        $this->setSubmittedData();
    }

    function __destruct(){
        global $wpdb;
        $wpdb->insert( $wpdb->prefix.WURE_DB_TABLE, $this->insertData);
    }

    protected  function setTime(){
        $this->insertData['time'] = current_time('mysql');
    }

    protected function setIP(){
        if (!empty($_SERVER['HTTP_CLIENT_IP'])):
            //whether ip is from share internet
            $this->insertData['ip'] = $_SERVER['HTTP_CLIENT_IP'];
        elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])):
            //whether ip is from proxy
            $this->insertData['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else:
            //whether ip is from remote address
            $this->insertData['ip'] = $_SERVER['REMOTE_ADDR'];
        endif;
    }

    protected function setReferrer(){
        if(isset($_POST['_wp_http_referer'])) $this->insertData['referrer'] = $_POST['_wp_http_referer'];
    }

    protected function setBrowser(){
        $this->insertData['browser'] = $_SERVER['HTTP_USER_AGENT'];
    }

    public function reCaptcha($message){
        $this->insertData['reCaptcha'] = $message;
    }

    public function wufoo($message){
        $this->insertData['wufoo'] = $message;
    }

    public function nonce(){
       $this->insertData['nonce'] = false;
    }

    public function honeypot(){
        $this->insertData['honeypot'] = true;
    }

    public function setSubmittedData(){
        $message = $_POST;

        //Remove reCaptcha post data, it's useless in log
        unset($message['g-recaptcha-response']);

        //Post data
        $this->insertData['data'] = json_encode($message);
    }
}