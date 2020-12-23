<?php 
namespace WURE;

class FormErrors{
    public $fieldErrors = [];
    public $formErrors = [];
    public $log = '';
    public $formId = '';

    function __construct($log, $formId){
        $this->log = $log;
        $this->formId = $formId;
    }

    public function addFieldError($field, $text){
        $size = count($this->fieldErrors);

        foreach($this->fieldErrors as $error):
            if($error['field'] == $field):
                $this->fieldErrors[$size]['errors'][] = $text;
                return;
            endif;
        endforeach;
        
        $this->fieldErrors[$size]['field'] = $field;
        $this->fieldErrors[$size]['errors'] = $text;
    }

    public function addFormError($text){
        $this->formErrors[] = $text;
    }

    public function noErrors(){
        if(empty($this->fieldErrors) && empty($this->formErrors)):
            return true;
        else:
            return false;
        endif;
    }

    public function writeError(){
        $this->log->wufoo( json_encode(array_merge(array('Form id'=> $this->formId), $this->fieldErrors, $this->formErrors)));
        add_action( 'wp_footer', array( $this, 'errorsToJavascript') );
    }

    public function errorsToJavascript(){
        ob_start(); ?>
            <script type="text/javascript">
            jQuery(document).ready(function($){
                var fieldsErrors = JSON.parse('<?php echo json_encode($this->fieldErrors); ?>'); 
                var formErrors = JSON.parse('<?php echo json_encode($this->formErrors); ?>');
                var $form = $("form input#idForm[value='<?php echo $this->formId; ?>']").closest("form");
                
                if(!$.isEmptyObject(fieldsErrors) || !$.isEmptyObject(formErrors)){
                    $('body, html').animate({
                        scrollTop: $form.offset().top-200
                    }, 1500);
                }

                if(!$.isEmptyObject(fieldsErrors)){
                    $.each(fieldsErrors, (i)=>{
                        $form.find("input[name='"+fieldsErrors[i]['field']+"'], input[name='"+fieldsErrors[i]['field']+"-1'], input[name='"+fieldsErrors[i]['field']+"-2'], textarea[name='"+fieldsErrors[i]['field']+"'], select[name='"+fieldsErrors[i]['field']+"']").css({
                            'border-color':'red',
                            'border-style':'solid',
                            'border-width':'1px'
                        });
                    });
                    $form.prepend('<div style="color:red; background-color:#fff">Errors have been <b>highlighted</b> below.</div>');
                }
                if(!$.isEmptyObject(formErrors)){
                    var list='';
                    $.each(formErrors, (i)=>{
                        list += "<li>"+formErrors[i]+"</li>"; 
                    });
                    $form.prepend('<div style="color:red; background-color:#fff"><ul>'+list+'</ul></div>');
                }
            });
            </script>
        <?php  echo ob_get_clean();
    }

}