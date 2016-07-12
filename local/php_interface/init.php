<?php
use Spo\Site\Entities\UserValidDataTable;
define('LOG_FILENAME', $_SERVER['DOCUMENT_ROOT'] . '/local/logs/SPO.txt');

// регистрируем обработчик события "OnBeforeUserRegister"
AddEventHandler("main", "OnBeforeUserRegister", "OnBeforeUserRegisterHandler");
AddEventHandler("main", "OnAfterUserRegister", "OnAfterUserRegisterHandler");

function dumpvar($var){
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}

/*function printvar( $var, $value, $not_admin=1, $trace=0) {
    global $USER;

    if (!$USER->IsAdmin() && $not_admin) return false;

    if($trace){
        echo "<pre>";
        switch ($trace) {
            case 1:
                debug_print_backtrace();
                break;
            case 2:
                print_r( debug_backtrace() );
                break;
        }
        echo "</pre>";
    }
    if($trace!=2){
        $t = gettype($value);
        if( $t == "array" || $t == "object") {
            echo "<b>".$var."</b>";
            echo "<hr>";
            echo "<pre>";
            print_r( $value );
            echo "</pre>";
        }
        else {
            echo "<b>".$var." = </b>".$value;
            echo "<hr>";
        }
    }
}*/

// файл /bitrix/modules/my_module_id/include.php
function OnBeforeUserRegisterHandler(&$arFields)
{
    $arFields['ACTIVE']='N';
    if($arFields['GROUP_ID'][0]==6){
        $arFields['GROUP_ID'][0]=7;
        if($arFields['maderator']=='on'){
            $arFields['GROUP_ID'][1]=9;
        }
        $arFields['ACTIVE']='Y';
    }
    if($arFields['GROUP_ID'][0]!=7) {
        $length = 5;
        $num = rand(11111, 99999);
        $code = md5($num);
        $code = substr($code, 0, $length);
        $GLOBALS['CONFIRMATION_CODE'] = substr($code, 0, $length);
        $arFields['CONFIRMATION_CODE'] = $GLOBALS['CONFIRMATION_CODE'];
    }
}
function OnAfterUserRegisterHandler(&$arFields)
{
    $cUser = new CUser;
    /*$sort_by = "ID";
    $sort_ord = "ASC";
    $arFilter = array(
        "LOGIN" => $arFields['LOGIN'],
    );
    $dbUsers = $cUser->GetList($sort_by, $sort_ord, $arFilter);
    $arUser = $dbUsers->Fetch();*/
    $ID=$arFields['USER_ID'];
    if($arFields['GROUP_ID'][0]!=7 and !empty($ID)){
         $result = UserValidDataTable::add(array(
            'USER_VALID_DATA_EMAIL_CONFIRM_CODE' => $GLOBALS['CONFIRMATION_CODE'],
            'USER_ID' => $ID,
            'USER_VALID_DATA_IS_ACTIVE' => 0,
         ));
    }
    else{
        $cUser->IsAuthorized($ID);
    }
}
//CModule::AddAutoloadClasses('', array(
//	'UserRegistrationHandlers' => '/local/modules/spo.site/lib/util/userregistrationhandlers.php',
//));

//// Обработчик события регистрации нового пользователя. Выполняется ДО регистрации пользователя. При неудачном завершении
//// пользователь зарегистрирован не будет. Не выполняется при регистрации пользователя из административной части
//AddEventHandler('main', 'OnBeforeUserRegister', Array('UserRegistrationHandlers', 'beforeUserRegister'));
//
//
//// Выполняется и при создании пользователя из административной части, и при самостоятельной регистрации пользователя.
//AddEventHandler('main', 'OnAfterUserAdd', Array('UserRegistrationHandlers', 'afterUserCreation'));
//
//
//// Выполняется после регистрации пользователя в системе - только если пользователь регистрировался самостоятельно
//AddEventHandler('main', 'OnAfterUserRegister', Array('UserRegistrationHandlers', 'checkUserIsActive'));
//
//// Выполняется перед авторизацией пользователя
////AddEventHandler('main', 'OnBeforeUserLogin', Array('UserRegistrationHandlers', 'checkUserIsActive'));
//
//// Выполняется после попытки обновить профиль пользователя
//AddEventHandler("main", "OnAfterUserUpdate", Array("UserRegistrationHandlers", "afterUserUpdate"));