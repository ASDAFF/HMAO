initDateTimePicker = function(){
    var selectorIds = [], i,
        datePickerIds = [
            'abiturientProfileBirthday',
            'abiturientProfileIdentityDocumentIssuedDate',
            'abiturientProfileEducationCompletionDate',
            'abiturientProfileInsuranceDate',
            'abiturientProfileBirthdayParentMama',
            'abiturientProfileBirthdayParentPapa',
            'abiturientProfileParentIdentityDocumentIssuedDateMama',
            'abiturientProfileParentIdentityDocumentIssuedDatePapa',
        ];

    for(i=0; i<datePickerIds.length; i++){
        selectorIds.push('#' + datePickerIds[i]);
    }

    $(selectorIds.join(',')).datetimepicker({
        format:        'd-m-Y',
        lang:          'ru',
        timepicker:     false,
        dayOfWeekStart: 1
    });
};
initFormValidation = function(){
    $('#abiturient-profile-form').validate({
        rules: {
            // вкладка 1
            'AbiturientProfile[abiturientProfileGender]': {},
            'AbiturientProfile[abiturientProfileNationality]': {},
            'AbiturientProfile[abiturientProfileNationalityCountry]': {
                //required: true
            },
            'AbiturientProfile[abiturientProfileBirthday]': {
                required: true
            },
            'AbiturientProfile[abiturientProfileBirthplace]': {
                required: true
            },
            'AbiturientProfile[abiturientProfileRegistrationAddress]': {
                required: true
            },
            'AbiturientProfile[abiturientProfilePhone]': {
                required: true
            },
            'AbiturientProfile[abiturientProfileINN]': {},
            'AbiturientProfile[abiturientProfileSNILS]': {
                required: true
            },
            'AbiturientProfile[abiturientProfileIdentityDocumentType]': {
                required: true
            },
            'AbiturientProfile[abiturientProfileIdentityDocumentSeries]': {
                required: true
            },
            'AbiturientProfile[abiturientProfileIdentityDocumentNumber]': {
                required: true
            },
            'AbiturientProfile[abiturientProfileIdentityDocumentIssuedBy]': {
                required: true
            },
            'AbiturientProfile[abiturientProfileIdentityDocumentIssuedDate]': {
                required: true
            },
            'AbiturientProfile[abiturientProfileIdentityDocumentIssuedCode]': {
                required: true
            },
            // вкладка 2
            'AbiturientProfile[abiturientProfileEducationOrganizationType]': {
                required: true
            },
            'AbiturientProfile[abiturientProfileEducationOrganizationCity]': {
                required: true
            },
            'AbiturientProfile[abiturientProfileEducationOrganizationName]': {
                required: true
            },
            'AbiturientProfile[abiturientProfileEducationOrganizationNumber]': {
                required: true
            },
            'AbiturientProfile[abiturientProfileEducationCompletionDate]': {
                required: true
            },
            'AbiturientProfile[abiturientProfileEducationDocumentType]': {},
            'AbiturientProfile[abiturientProfileEducationDocumentSeries]': {
                required: true
            },
            'AbiturientProfile[abiturientProfileEducationDocumentNumber]': {
                required: true
            },
            'AbiturientProfile[abiturientProfileCAS]': {},
            'AbiturientProfile[abiturientProfileGraduatedWithHonours]': {},
            'AbiturientProfile[abiturientProfileOlympiadWinner]': {},
            'AbiturientProfile[abiturientProfileOlympiadWinnerString]': {},
            'AbiturientProfile[abiturientProfileEducation]': {},
            'AbiturientProfile[abiturientProfileFirstTimeEnrolment]': {},
            'AbiturientProfile[abiturientProfileAdditionalLanguage]': {},
            // вкладка 3
            'AbiturientProfile[abiturientProfileInsuranceCompanyName]': {},
            'AbiturientProfile[abiturientProfileInsuranceNumber]': {},
            'AbiturientProfile[abiturientProfileInsuranceSeries]': {},
            'AbiturientProfile[abiturientProfileInsuranceDate]': {},
            // вкладка 4
            'AbiturientProfile[abiturientProfileIsReservist]': {},
            'AbiturientProfile[abiturientProfileMilitaryDocumentSeries]': {},
            'AbiturientProfile[abiturientProfileMilitaryDocumentNumber]': {},
            'AbiturientProfile[abiturientProfileMilitaryDocumentRegion]': {},
            'AbiturientProfile[abiturientProfileSeniority]': {},
            'AbiturientProfile[abiturientProfileSeniorityString]': {},
            'AbiturientProfile[abiturientProfileAdditionalData]': {},
            // вкладка 5
            'AbiturientProfile[identityDocumentScanFile]': {},
            'AbiturientProfile[INNScanFile]': {},
            'AbiturientProfile[SNILSScanFile]': {},
            'AbiturientProfile[educationDocumentScanFile]': {}
        },

        ignore:       '',
        onfocusout:    function(element, event){
            $(element).valid();
        },
        errorElement: 'span',
        submitHandler: function(validator, event){
            return $(this.currentForm).valid();
        },
        errorPlacement: function($error, $element) {
            $element.parent().find('.error-list').append($error);
        },
        highlight: function(element, errorClass, validClass) {
            var $el   = $(element),
                tabId = $el.parents('.tab-pane').attr('id');

            $el.parents('form')
                .find('.nav-tabs a[href="#' + tabId + '"]')
                .addClass('error-tab')
                .trigger('click');

            $el.parent().find('.error-list').show();
        },
        unhighlight: function(element, errorClass, validClass) {
            var $el    = $(element),
                tab    = $el.parents('.tab-pane'),
                tabId  = tab.attr('id'),
                errCnt = tab.find('.error-list:visible span').length;

            $el.parent().find('.error-list').hide();

            // если не сделать проверку is(':visible'), получится, что errCnt будет заведомо = 0
            if(tab.is(':visible') && errCnt === 0){
                $el.parents('form')
                    .find('.nav-tabs a[href="#' + tabId + '"]')
                    .removeClass('error-tab');
            }
        }
    });
};
initProfileForm = function() {
    var orgForm = new SpoForm($('form'), {
        onErrorShow: function ($fld, message, name) {
            var tabId = $fld.parents('.tab-pane').attr('id');
            $fld.parents('form')
                .find('.nav-tabs a[href="#' + tabId + '"]')
                .addClass('error-tab')
                .trigger('click');
        }
    });

    orgForm.showValidationErrors(errorList);

    if (spoUrl.isHashSet()) {
        $('ul.nav a[href="' + spoUrl.getHash() + '"]').tab('show');
    }

    $('.nav-tabs a').click(function (e) {
        //$(this).tab('show');
        window.location.hash = this.hash;
    });

    $('#isOlympiadWinnerCheckbox').on('change', function (event) {
        $('#abiturientProfileOlympiadWinnerString')[$(this).is(':checked') ? 'show' : 'hide']();
    }).trigger('change');

    $('#hasSeniorityCheckbox').on('change', function (event) {
        $('#abiturientProfileSeniorityString')[$(this).is(':checked') ? 'show' : 'hide']();
    }).trigger('change');

    $('#isReservistCheckbox').on('change', function (event) {
        $('#militaryDocumentAttributes')[$(this).is(':checked') ? 'show' : 'hide']();
    }).trigger('change');

    $('#nationalityCountrySelect').on('change', function (event) {
        var $el = $(this),
            val = $el.val(),
            condition = (val == nationalityNONE || val == nationalityRU);

        $('#NationalityCountry')[condition ? 'hide' : 'show']();
    }).trigger('change');
};
$(function(){
    initDateTimePicker();
    initFormValidation();
    initProfileForm();
});
