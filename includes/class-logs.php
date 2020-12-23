<?php

namespace WURE;

class LOGS{
    protected $file = '';
    protected $pathToFile = ABSPATH.'/wp-content/wure.log';

    function __construct(){
        $this->openFile();
        $this->writeTime();
        $this->writeIP();
        $this->referer();
        $this->browser();
    }

    function __destruct(){
        fputs($this->file, "\n\n");
        $this->closeFile();
    }

    protected function openFile(){
        $this->file = fopen($this->pathToFile, "a");//Open in write mode
    }

    protected function closeFile(){
        fclose($this->file);
    }

    protected function writeTime(){
        fputs($this->file, date('Y-m-d H:i:s'));
    }

    protected function writeIP(){
        if (!empty($_SERVER['HTTP_CLIENT_IP'])):
            //whether ip is from share internet
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])):
            //whether ip is from proxy
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else:
            //whether ip is from remote address
            $ip_address = $_SERVER['REMOTE_ADDR'];
        endif;
        fputs($this->file, " [".$ip_address."] ");
    }

    protected function referer(){
        if(isset($_POST['_wp_http_referer'])):
            fputs($this->file, '    Referer- "'.$_POST['_wp_http_referer'].'".');
        endif;
    }

    protected function browser(){
        fputs($this->file, "    Browser- ".$_SERVER['HTTP_USER_AGENT'].".");
    }

    public function reCaptcha($message){
        fputs($this->file, "    reCaptcha- ".$message.".");
    }

    public function wufoo($message){
        fputs($this->file, "    Wufoo- ".$message.".");
    }

    public function nounce(){
        fputs($this->file, "    Nonce- not set.");  
    }

    public function honeypot(){
        fputs($this->file, "    Honeypot set.");  
    }

    public function submittedData($message = []){
        //Remove reCaptcha post data, it's useless in log
        unset($message['g-recaptcha-response']);

        //Put post data
        fputs($this->file, "    Data sent- ".json_encode($message));
    }
}