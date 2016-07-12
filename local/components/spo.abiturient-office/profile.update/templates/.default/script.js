initDateTimePicker = function(){
	var selectorIds = [], i,
		datePickerIds = [
			'abiturientProfileIdentityDocumentIssuedDate',
			'abiturientProfileEducationCompletionDate',
			'abiturientProfileInsuranceDate',
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
			'AbiturientProfile[abiturientProfileFIO]': {
				required: true
			},
			'AbiturientProfile[abiturientProfileGender]': {
				required: true
			},
			'AbiturientProfile[abiturientProfileNationality]': {},
			'AbiturientProfile[abiturientProfileNationalityCountry]': {
				//required: true
			},
			'AbiturientProfile[abiturientProfileBirthday]': {
				required: true
			},
			'AbiturientProfile[abiturientProfileBirthplace]': {},
			'AbiturientProfile[AddressResidence]': {
				required: true
			},
			'AbiturientProfile[abiturientProfileRegistrationAddress]': {
				// required: true
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
				//required: true
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
				
			},
			'AbiturientProfile[abiturientProfileEducationDocumentNumber]': {
				required: true
			},
			'AbiturientProfile[abiturientProfileCAS]': {
				max: 5,
				required: true
			},
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

			// вкладка Родители
			'AbiturientProfile[abiturientParents]': {
				//required: true
			},
			'AbiturientProfile[abiturientParentsType]': {
				//required: true
			},
			'AbiturientProfileParent[fio][0]': {
				//required: true
			},
			'AbiturientProfileParent[citizenship][0]': {
				//required: true
			},
			'AbiturientProfileParent[birthdate][0]': {},
			'AbiturientProfileParent[Phone][0]': {
				//required: true
			},
			'AbiturientProfileParent[snils][0]': {},
			'AbiturientProfileParent[docserpers][0]': {
				//required: true
			},
			'AbiturientProfileParent[docnumpers][0]': {
				//required: true
			},
			'AbiturientProfileParent[docissuedpers][0]': {},
			'AbiturientProfileParent[docdatepers][0]': {},
			'AbiturientProfileParent[dobdocument][0]': {},
			'AbiturientProfileParent[fio][1]': {},
			'AbiturientProfileParent[citizenship][1]': {},
			'AbiturientProfileParent[birthdate][1]': {},
			'AbiturientProfileParent[Phone][1]': {},
			'AbiturientProfileParent[snils][1]': {},
			'AbiturientProfileParent[docserpers][1]': {},
			'AbiturientProfileParent[docnumpers][1]': {},
			'AbiturientProfileParent[docissuedpers][1]': {},
			'AbiturientProfileParent[docdatepers][1]': {},
			'AbiturientProfileParent[dobdocument][1]': {}
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
			if($element.is(":file")){
				$element.next('.help-block').find('.error-list').append($error);
			} else {
				$element.parent().find('.error-list').append($error);
			}
		},
		highlight: function(element, errorClass, validClass) {
			var $el   = $(element),
				tabId = $el.parents('.tab-pane').attr('id');

			$el.parents('form')
				.find('.nav-tabs a[href="#' + tabId + '"]')
				.addClass('error-tab')
				.trigger('click');

			if($el.is(":file")){
				$el.next('.help-block').find('.error-list').show();
			} else {
				$el.parent().find('.error-list').show();
			}
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
CopyAddress =function() {
	var	reg=$("#Registration").val();
	$("#Residence").val(reg);
	$("#Residence2").html(reg);
}
ProfileEdu = function(a) {
	if(a==5)
	{
		$("#TimeEnrolment").prop("checked", false);
		$("#TimeEnrolment").prop("disabled", true);
	}
	else
	{
		$("#TimeEnrolment").prop("disabled", false);
	}
}

$(function(){
	var availableTags = [
		"Дошкольная общеобразовательная организация",
		"Общеобразовательная организация",
		"Начальная общеобразовательная школа",
		"Гимназия",
		"Прогимназия",
		"Лицей",
		"Средняя (полная) общеобразовательная школа",
		"Средняя (полная) общеобразовательная   школа с углубленным изучением отдельных предметов",
		"Вечерняя (сменная) общеобразовательная школа",
		"Образовательная организация начального профессионального образования",
		"Специальное (коррекционное) образовательное учреждение для обучающихся, воспитанников с отклонениями в развитии",
		"Профессионально-техническое училище",
		"Профессиональная образовательная организация",
		"Профессиональное училище",
		"Профессиональный лицей",
		"Колледж",
		"Техникум",
		"Образовательная организация высшего образования",
		"Высшее училище",
		"Школа-студия",
		"Консерватория",
		"Институт",
		"Университет",
		"Академия",
		"Негосударственная (частная) образовательная организация",
		"Учреждение для детей-сирот и детей, оставшихся без попечения родителей",
		"Основная школа",
		"Семилетняя школа",
		"Восьмилетняя школа",
		"Девятилетняя школа",
		"Другие учреждения, осуществляющие образовательный процесс",
		"Специальная общеобразовательная школа закрытого типа",
		"Православная гимназия",
		"Средняя общеобразовательная кадетская школа",
		"Средняя (полная) общеобразовательная школа-интернат",
	];
	$("#typeOrg").autocomplete({source: availableTags});
	initDateTimePicker();
	initFormValidation();
	initProfileForm();
});
