<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
    use Spo\Site\Util\UiMessage;
    /* @var $arResult */
    $messages = $arResult['messages'];
    $selector = $arResult['selector'];
    $markup = '';

    foreach(UiMessage::$types as $type){
        if(is_array($messages[$type])){
            foreach($messages[$type] as $message){
                $markup .= implode('', array(
                    '<div class="alert alert-' , $type, '" role="alert">',
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">',
                            '<span aria-hidden="true">&times;</span>',
                        '</button>',
                        $message,
                    '</div>'
                ));
            }
        }
    }
?>
<script type="text/javascript">
    $(function(){
        $('<?=$selector?>').html('<?=$markup?>');
    });
</script>