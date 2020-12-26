<?php
namespace WURE;

class WufooHelper{
    public $log = '';
    private $fields = [];
    private $wufooFields = [];
    private $data = [];
    private $errors = '';

    public function __construct($log, $errors){
        $this->fields = $_POST;
        $this->log = $log;
        $this->errors = $errors;
    }

    public function process(){
        foreach(array( 'getFields', 'setData', 'saveEntry', 'redirect') as $method):
            if($this->errors->noErrors()): 
                $this->$method();
            else: 
                $this->errors->writeError();
                return;
            endif;
        endforeach;
    }

    public function getFields() {
        $curlFields = curl_init(sprintf("https://%s/api/v3/forms/%s/fields.json", urlencode(get_option( 'wufoo_subdomain' )), $this->fields['idForm']));
        $this->curlBasicOptions($curlFields);

        $response = curl_exec($curlFields);
        $resultStatus = curl_getinfo($curlFields);

        if($resultStatus['http_code'] == 200):
            $json = json_decode($response, true);
            $index = 0;

            foreach ($json['Fields'] as $field):
                if( substr( $field['ID'], 0, 5 ) === "Field" ):
                    if( !array_key_exists( 'SubFields', $field )):
                        $this->wufooFields[$index]['id'] = $field['ID'];
                        $this->wufooFields[$index]['type'] = $field['Type'];
                        $this->wufooFields[$index]['IsRequired'] = $field['IsRequired'];
                    else:
                        switch($field['Type']):
                            case 'checkbox':
                                $this->wufooFields[$index]['id'] = $field['ID'];
                                $this->wufooFields[$index]['type'] = $field['Type'];//checkbox
                                $this->wufooFields[$index]['IsRequired'] = $field['IsRequired'];
                                foreach($field['SubFields'] as $subfield):
                                    $this->wufooFields[$index]['subfields'][] = $subfield['ID'];
                                endforeach;
                                break;
                            default:
                                foreach($field['SubFields'] as $subfield):
                                    $this->wufooFields[$index]['id'] = $subfield['ID'];
                                    $this->wufooFields[$index]['type'] = $field['Type'];
                                    $this->wufooFields[$index]['IsRequired'] = $field['IsRequired'];
                                    $index++;
                                endforeach;
                                break;
                        endswitch;
                    endif;
                    $index++;
                endif;
            endforeach;
        else:
            $this->errors->addFormError('Connection Error');
        endif;
    }
    
    private function setData(){ 
        foreach($this->wufooFields as $field):
            $checker = 1;
            switch($field['type']):
                case 'checkbox':
                    foreach($field['subfields'] as $subfield):
                        if(array_key_exists($subfield, $this->fields)):
                            $this->data[$subfield] = $this->fields[$subfield];
                        endif;
                        $checker = 0;
                    endforeach;
                    break;
                case 'file':
                    if (file_exists($_FILES[$field['id']]['tmp_name']) || is_uploaded_file($_FILES[$field['id']]['tmp_name'])):
                        $tmpfile = $_FILES[$field['id']]['tmp_name'];
                        $filename = basename($_FILES[$field['id']]['name']);
                        $this->data[$field['id']] = curl_file_create($tmpfile, $_FILES[$field['id']]['type'], $filename);
                        $checker = 0;
                    endif;
                    break;
                case 'phone':
                    if(
                        isset($this->fields[$field['id']]) &&
                        isset($this->fields[$field['id'].'-1']) && 
                        isset($this->fields[$field['id'].'-2']) &&
                        is_numeric($this->fields[$field['id']]) &&
                        is_numeric($this->fields[$field['id'].'-1']) &&
                        is_numeric($this->fields[$field['id'].'-2']) &&
                        strlen($this->fields[$field['id']].$this->fields[$field['id'].'-1'].$this->fields[$field['id'].'-2']) == 10
                    ):
                        $this->data[$field['id']] = $this->fields[$field['id']].$this->fields[$field['id'].'-1'].$this->fields[$field['id'].'-2'];
                        $checker = 0;
                    endif;
                    break;
                case 'date':
                    if(
                        isset($this->fields[$field['id']]) && !empty($this->fields[$field['id']]) &&
                        isset($this->fields[$field['id'].'-1']) && !empty($this->fields[$field['id'].'-1']) &&
                        isset($this->fields[$field['id'].'-2']) && !empty($this->fields[$field['id'].'-2'])
                    ):
                        $this->data[$field['id']] = $this->fields[$field['id']].$this->fields[$field['id'].'-1'].$this->fields[$field['id'].'-2'];
                        $checker = 0;
                    endif;
                    break;
                case 'eurodate':
                    if(
                        isset($this->fields[$field['id']]) && !empty($this->fields[$field['id']]) &&
                        isset($this->fields[$field['id'].'-1']) && !empty($this->fields[$field['id'].'-1']) &&
                        isset($this->fields[$field['id'].'-2']) && !empty($this->fields[$field['id'].'-2'])
                    ):
                        $this->data[$field['id']] = $this->fields[$field['id']].$this->fields[$field['id'].'-2'].$this->fields[$field['id'].'-1'];
                        $checker = 0;
                    endif;
                    break;
                default:
                    if( isset($this->fields[$field['id']]) ):
                        $this->data[$field['id']] = $this->fields[$field['id']];
                        $checker = 0;
                    endif;
            endswitch;

            //check if is required and filled
            if($field['IsRequired'] && $checker):
                $this->errors->addFieldError($field['id'], 'This field is required');
            endif;

        endforeach;
    }

    private function saveEntry(){
        $curlEntry = curl_init(sprintf( "https://%s/api/v3/forms/%s/entries.json", urlencode(get_option( 'wufoo_subdomain' )), $this->fields['idForm'] ));
        $this->curlBasicOptions( $curlEntry );
        curl_setopt( $curlEntry, CURLOPT_POST, 1) ;
        curl_setopt( $curlEntry, CURLOPT_POSTFIELDS, $this->data);

        $wufooResponse = json_decode(curl_exec($curlEntry));
        $resultStatus = curl_getinfo($curlEntry);
        if($resultStatus['http_code'] == 200 || $resultStatus['http_code'] == 201):
            if($wufooResponse->{'Success'}):
                $this->redirectURL = $wufooResponse->{'RedirectUrl'};
            else:
                foreach($wufooResponse->{'FieldErrors'} as $fieldError):
                    $this->errors->addFieldError($fieldError->{'ID'}, $fieldError->{'ErrorText'});
                endforeach;
            endif;
        else:
            $this->errors->addFormError('Connection Error');
        endif;
    }

    private function redirect(){
        $this->log->wufoo(json_encode(array("Form id" => $this->fields['idForm'], "Message" => "Form successfully submitted")));
        header('Location: '.$this->redirectURL, true, 302);
        exit();
    }

    private function curlBasicOptions($curl){
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERPWD, get_option( 'wufoo_api_key' ).':footastic');
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Wufoo Sample Code');
    }
}