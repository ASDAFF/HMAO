/**
 * Created by dogr on 20.05.2016.
 */
/*Валидация пароля*/
$(document).ready(function () {
    var registerButton = $('input[name="register_submit_button"]'),
        loginvalid = /(^[a-zA-Z](.[a-zA-Z0-9_-]*){3,20})$/,
        login = $('input[name="REGISTER[LOGIN]"]'),
        emailvalid = /^([0-9a-zA-Z]([-.w]*[0-9a-zA-Z])+@+([0-9a-zA-Z][-w]*[0-9a-zA-Z].{1,8})+[a-zA-Z]{2,6})$/,
        email = $('input[name="REGISTER[EMAIL]"]'),
        passvalid=/((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%]).{6,20})/,
        pass = $('#pass'),
        captcha = $('captcha_word'),
        passconfirm = $('#confclass'),
        reginput = $('.reginput'),
        success = $('.inputSuccess1'),
        increment = 0,
        inc_email = 0,
        inc_login = 0,
        inc_pass = 0,
        inc_passconfirm = 0;
    inc_login = validfield(login, loginvalid, inc_login);
    inc_email = validfield(email, emailvalid, inc_email);
    inc_pass = validfield(pass, passvalid, inc_pass);
    inc_passconfirm = confirmpass();
    registerformvalidate(inc_pass,inc_login,inc_email,inc_passconfirm);
    function registerformvalidate(a,b,c,d) {
        increment = a + b + c + d;
        if(increment == 4){
            registerButton.prop("disabled", false);
        }
    }
    /*Валидация электронной почты*/
        email.on( "blur",
        function() {
            if(emailvalid.test(email.val()) == false){
                email.addClass('inputWarning1');
                email.removeClass('inputSuccess1');
                registerButton.prop("disabled", true);
                inc_email = 0;
            }else{
                email.removeClass('inputWarning1');
                email.addClass('inputSuccess1');
                inc_email = 1;
            }
        });
    /*Валидация пароля*/
        pass.on( "blur",
        function () {
        var err = $('.err');
        if(passvalid.test(pass.val()) == false){
            pass.addClass('inputWarning1');
            pass.removeClass('inputSuccess1');
            err.css('display','block');
            registerButton.prop("disabled", true);
            inc_pass = 0;
        }else{
            pass.removeClass('inputWarning1');
            pass.addClass('inputSuccess1');
            err.css('display','none');
            inc_pass = 1;
        }
    });
    /*Совпадение пароля*/
    passconfirm.on( "blur",
        function() {
            if(passconfirm.val() != pass.val() || passconfirm.val() == ''){
                passconfirm.addClass('inputWarning1');
                passconfirm.removeClass('inputSuccess1');
                registerButton.prop("disabled", true);
                inc_passconfirm = 0;
            }else{
                passconfirm.removeClass('inputWarning1');
                passconfirm.addClass('inputSuccess1');
                inc_passconfirm = 1;
            }
        });
    /*Валидация логина*/
        login.on( "blur",
        function() {
            if( loginvalid.test(login.val()) == false){
                login.addClass('inputWarning1');
                login.removeClass('inputSuccess1');
                registerButton.prop("disabled", true);
                inc_login = 0;
            }else{
                login.removeClass('inputWarning1');
                login.addClass('inputSuccess1');
                inc_login = 1;
            }
        });
        /*Проверка формы целиком*/
        reginput.on("blur",
        function () {
            increment = inc_email + inc_login + inc_pass + inc_passconfirm;
            if(increment == 4){
                registerButton.prop("disabled", false);
            }
        });
    function confirmpass() {
        if(passconfirm.val() == ''){
            passconfirm.removeClass('inputWarning1');
            passconfirm.removeClass('inputSuccess1');
        } else if(passconfirm.val() != pass.val()){
            passconfirm.addClass('inputWarning1');
            passconfirm.removeClass('inputSuccess1');
            registerButton.prop("disabled", true);
            inc_passconfirm = 0;
        }else{
            passconfirm.removeClass('inputWarning1');
            passconfirm.addClass('inputSuccess1');
            inc_passconfirm = 1;
        }
        return inc_passconfirm;
    }
    function validfield(namefield, namevalid, name_inc) {
        if (namefield.val() == ''){
            namefield.removeClass('inputWarning1');
            namefield.removeClass('inputSuccess1');
        } else if( namevalid.test(namefield.val()) == false){
            namefield.addClass('inputWarning1');
            namefield.removeClass('inputSuccess1');
            registerButton.prop("disabled", true);
            name_inc = 0;
            console.log(name_inc);
        }else{
            namefield.removeClass('inputWarning1');
            namefield.addClass('inputSuccess1');
            name_inc = 1;
            console.log(name_inc);
        }
        return name_inc;
    }
    });





/*Маски полей серия документа, тип документа*/
function mask() {
    var optionNumber = $("select[name='AbiturientProfile[abiturientProfileIdentityDocumentType]']").val();
    if (optionNumber == 1){
        jQuery(function($){
            console.log(optionNumber, 'маска паспорта');
            $('input[name="AbiturientProfile[abiturientProfileIdentityDocumentSeries]"]').mask("99 99");

        });
    }
    if (optionNumber == 2 ){
        jQuery(function($){
            console.log(optionNumber, 'маска свидетельства');
            $('input[name="AbiturientProfile[abiturientProfileIdentityDocumentSeries]"]').mask("aa-aa");
        });
    }
}

/*Проверка наличия СНИЛС*/
function avaliable(check, snils) {
    if (check.prop("checked") == false){
        snils.animate({
                opacity: '1'}, 300,
            function(){  snils.css('display', 'block'); }
        );
    } else {
        snils.animate({
                opacity: '0'}, 300,
            function(){  snils.css('display', 'none'); }
        );
    }
}


//Политика безопасности. Проверка согласия на обработкуперсональных данных
function persdan() {
    var accept = $('input[name="Accept"]');
    var save = $('button[type="submit"]');
    if (accept.prop("checked") == false){
        save.attr("disabled", "disabled");
    } else {
        save.removeAttr("disabled");
    }
}
jQuery(function($){
    $('.phone').inputmask("+7(999) 999-9999");
    $('.snils').inputmask("999-999-999 99");
    $('.nomdoc').inputmask("999999");
    $('.seriesdoc').inputmask("99 99");
    $('.inn').inputmask("999999999999");
    $('.obrnum').inputmask("99999999999999");
    $('.ballat').inputmask("9.99");
    $('.birthday').inputmask({ "mask": "d-m-y", 'autoUnmask' : true});
    $('.kodpod').inputmask("999-999");
    $('input[name="REGISTER[PERSONAL_PHONE]"]').inputmask("+7(999) 999-9999");
    $('input[name="AbiturientProfile[abiturientProfileSNILS]"]').inputmask("999-999-999 99");
    $('input[name="AbiturientProfile[abiturientProfileIdentityDocumentNumber]"]').inputmask("999999");
    $('input[name="AbiturientProfile[abiturientProfileIdentityDocumentSeries]"]').inputmask("99 99");
});

//Функция учета приоритета заявки
function changepriority() {
    $('.icon-move').css('display','inline-block');
    $('.saveprior').css('display','inline-block');
    $('.infprior').css('display','inline-block');
    $('.changeprior').css('display','none');
}
function savepriority() {
    $('.icon-move').css('display','none');
    $('.saveprior').css('display','none');
    $('.infprior').css('display','none');
    $('.changeprior').css('display','inline-block');
    var mass = $('ul.example li .prior');
    var j=0;
    for(var i=0; i<mass.length; i++){
        if (mass.eq(i).attr('data') !=mass.eq(i-1).attr('data'))
            j=1;
        else
            j++;
        if (mass.eq(i).val()!=j)
        {
            var id=mass.eq(i).attr('id');
            console.log(mass.eq(i).attr('data'));
            console.log(mass.eq(i).val());
            console.log(j);
            console.log(mass.eq(i).val()!=j);
            console.log("#bul_"+mass.eq(i).attr('id'));
            $("#bul_"+id).val(1);
            mass.eq(i).val(j);
        }
    }
    $("form:first").submit();


}

//Сортировка и прописывание приоритета
$(document).ready(function() {
    var mass = $('ul.example li');
    if (mass.length > 1){
        $('.changeprior').css('display','inline-block');
    } else {
        $('.changeprior').css('display','none');
    }
});

/*Скрипт добавления/удаления полей во вкладке "Родители"*/
function parenttypechange(){
    var parent = $('input.parent[type="radio"]');
    var guard = $('input.guard[type="radio"]');
    var parentpoint = $('.parent');
    var guardpoint = $('.guard');
    var firstparent = $('#firstparent');
    var parenttype = $('#parenttype');
    if (parent.prop("checked") == true){
        //console.log('parent');
        parentpoint.animate({
                opacity: '1'}, 300,
            function(){  parentpoint.css('display', 'block'); }
        );
        guardpoint.animate({
                opacity: '0'}, 300,
            function(){  guardpoint.css('display', 'none'); }
        );
        parenttype.animate({
                opacity: '1'
            }, 300,
            function () {
                parenttype.css('display', 'block');
            }
        );
    }
    if (guard.prop("checked") == true){
        $("#NameParent").html('законого представителя');
        console.log('guard');
        guardpoint.animate({
                opacity: '1'}, 300,
            function(){  guardpoint.css('display', 'block'); }
        );
        parentpoint.animate({
                opacity: '0'}, 300,
            function(){  parentpoint.css('display', 'none'); }
        );
        firstparent.animate({
                opacity: '1'
            }, 300,
            function () {
                firstparent.css('display', 'block');
            }
        );
    }
}

/*Функция выбора одного/двух родителей или законного представителя*/
function fullparent() {
    var mother = $('#parent1');
    var father = $('#parent2');
    var secondparent = $('#secondparent');
    var firstparent = $('#firstparent');
    var i = 0;
    if ((mother.is(':checked') == true) && (father.is(':checked') == false)) {
        i = 1;
    }

    if ((father.is(':checked') == true) && (mother.is(':checked') == false)) {
        i = 2;
    }

    if ((mother.is(':checked') == true) && (father.is(':checked') == true)){
        i = 3;
    }

    switch (i) {
        case 0:
        {
            firstparent.animate({
                    opacity: '0'
                }, 300,
                function () {
                    firstparent.css('display', 'none');
                }
            );
            secondparent.animate({
                    opacity: '0'
                }, 300,
                function () {
                    secondparent.css('display', 'none');
                }
            );
            break;
        }
        case 1:
        {
            firstparent.animate({
                    opacity: '1'
                }, 300,
                function () {
                    firstparent.css('display', 'block');
                }
            );
            secondparent.animate({
                    opacity: '0'
                }, 300,
                function () {
                    secondparent.css('display', 'none');
                }
            );
            break;
        }
        case 2:
        {
            firstparent.animate({
                    opacity: '1'
                }, 300,
                function () {
                    firstparent.css('display', 'none');
                }
            );
            secondparent.animate({
                    opacity: '1'
                }, 300,
                function () {
                    secondparent.css('display', 'block');
                }
            );
            break;
        }
        case 3:
        {
            firstparent.animate({
                    opacity: '1'
                }, 300,
                function () {
                    firstparent.css('display', 'block');
                }
            );
            secondparent.animate({
                    opacity: '1'
                }, 300,
                function () {
                    secondparent.css('display', 'block');
                }
            );
            break;
        }
    }

}
$(document).ready(
    function () {
        $('#parent1').click( function () {
            fullparent();
        });
        $('#parent2').click( function () {
            fullparent();
        });
    }
);

function snilsrequired() {
    var check = $('#AcceptSNILS');
    var snils = $('#abiturientProfileSNILS');
    if (check.prop('checked')){
        snils.val('Нет СНИЛС');
    } else {
        snils.val('');
    }
}

function AllHostel() {
    var check = $('#hostel');
    var hostel = $('[name="applicationData[needHostel]"]');
    if (check.prop('checked')){
        hostel.attr('checked',true);
    } else {
        hostel.attr('checked', false);
    }
}

function regHome() {
    var check = $('[name="AbiturientProfile[IdentityRegisterType]"]');
    var reg = $('[name="AbiturientProfile[IdentityRegisterType]"]');
    if (check.val()==1){
        $('[name="AbiturientProfile[IdentityRegisterType]"]').val()
    } else {
        hostel.attr('checked', false);
    }
}


function nameparent() {

    console.log(res>18);
   // alert(rs);

}

/*Дополнительные поля с проверкой и обязательным заполнением, не проверяющиеся библиотекой validate.js*/
function validatebitch() {

    if ($('#abiturientProfileBirthday').val() == '--'){
        $('#SpanAbiturientProfileBirthday').css('display','inline');
    }
    if ($('#Registration').val() == ''){
        $('#SpanRegistration').css('display','inline');
    }
    if ($('#Residence').val() == ''){
        $('#SpanResidence').css('display','inline');
    }
    if ($('#abiturientProfileIdentityDocumentIssuedDate').val() == '--'){
        $('#SpanAbiturientProfileIdentityDocumentIssuedDate').css('display','inline');
    }
    if ($('#docissuedpersParent1').val() == ''){
        $('#SpandocissuedpersParent1').css('display','inline');
    }
    if ($('#abiturientProfileBirthdayParentMama').val() == ''){
        $('#SpanabiturientProfileBirthdayParentMama').css('display','inline');
    }

    /*вычисляем если абитуриенту 18 лет*/
    var B=$("#abiturientProfileBirthday").val();


    var now = new Date(B[4]+B[5]+B[6]+B[7], B[2]+B[3],B[0]+B[1]);

    var res=(new Date().getTime()-now) / (24 * 3600 * 365.25 * 1000);

    if(res<18)
    {
        $.validator.addClassRules("parents",
            {
                required: true
            });
    }

    if ($('.guard').prop("checked") || $('#parent1').prop("checked"))
    {
        $.validator.addClassRules("maza",
            {
                required: true
            });
        $.validator.addClassRules("birthdate1",
            {
                required: true
            });

        if ($('#AcceptParentSNILS1').prop("checked")==false)
        {
            $.validator.addClassRules("msnils",
                {
                    required: true
                });
        }
        $.validator.addClassRules("seriesdoc",
            {
                required: true
            });
        $.validator.addClassRules("nomdoc",
            {
                required: true
            });
        $.validator.addClassRules("docissuedpers",
            {
                required: true
            });
        $.validator.addClassRules("docdatepers",
            {
                required: true
            });
        if($('.guard').prop("checked"))
        {
            $.validator.addClassRules("dobdocument",
                {
                    required: true
                });
        }
    }



   /* $.validator.addClassRules("parent",
        {
            required: true
        });
    $.validator.addClassRules("parentdel",
        {
            required: true
        });*/

    if($('#parent2').prop("checked"))
    {
        $.validator.addClassRules("faza",
            {
                required: true
            });
        $.validator.addClassRules("birthdate2",
            {
                required: true
            });

        if ($('#AcceptParentSNILS2').prop("checked")==false)
        {
            $.validator.addClassRules("fsnils",
                {
                    required: true
                });
        }
        $.validator.addClassRules("seriesdoc2",
            {
                required: true
            });
        $.validator.addClassRules("nomdoc2",
            {
                required: true
            });
        $.validator.addClassRules("docissuedpers2",
            {
                required: true
            });
        $.validator.addClassRules("docdatepers2",
            {
                required: true
            });
        if (!$('#AcceptParentSNILS1').prop("checked"))
        {
            $.validator.addClassRules("msnils",
                {
                    required: true
                });
        }
    }


    if ($('.photoview').val()==0)
    {
        $.validator.addClassRules("photoimg",
            {
                required: true
            });
    }
    if ($('.passportview').val()==0)
    {
        $.validator.addClassRules("passportimg",
            {
                required: true
            });
    }
    if ($('.passportregview').val()==0)
    {
        $.validator.addClassRules("passportregimg",
            {
                required: true
            });
    }
    if ($('.snilsview').val()==0)
    {
        $.validator.addClassRules("snilsimg",
            {
                required: true
            });
    }
    if ($('.atistatview').val()==0)
    {
        $.validator.addClassRules("atistatimg",
            {
                required: true
            });
    }

}

/*проверка при регистрации директора*/
function directorchange() {
    var director = $('input[name="REGISTER[maderator]"]').prop("checked");
    var organization = $('#regOrganization');
    if (director == false){
        organization.css('display','block');
    } else {
        organization.css('display','none');
    }
}
