$(function(){
	var dialog        = $('.dialog-form-container'),
		errorList     = dialog.find('.error-list'),
		specialtyIdCt = $('.specialty-id-fld'),
		specialtyBeCt = $('.specialty-base-education-fld'),
		specialtySmCt = $('.specialty-study-mode-fld'),
		examListCt    = $('.exam-list'),
		qualListCt    = $('.qualification-list'),
		studyPeriodCt = $('.study-period-fld'),
		abitCountCt   = $('.planned-abiturients-count-fld'),
		groupsCountCt = $('.planned-groups-count-fld'),
		trLevelCt     = $('.training-level-fld'),
		trTypeCt      = $('.training-type-fld'),
        radioAdapted = $('#checkboxAdapted'),
        checkboxNotAdapted = $('#checkboxNotAdapted'),
        adaptionTypeSelectBox = $('#adaptation-type-select-box'),
        adaptationTypeCheckboxes = $('.adaptation-type-checkbox');
        

    checkboxNotAdapted.on('click', function() {
        adaptionTypeSelectBox.hide();
        adaptationTypeCheckboxes.each(function() {
            this.checked = false;
        });
    });

    radioAdapted.on('click', function() {
        adaptionTypeSelectBox.show();
    });

	$('.edit-program-btn').on('click', function(event){
		var url  = $(event.target).attr('href'),
			list = $('#specialty-list');

		list.mask('Отправка данных');
		SpoAjax({
			url: url,
			successCallback: function(data){
				var i = 0, item;
				specialtyIdCt.val(data.specialty).trigger('change').prop('disabled', true);
				specialtyBeCt.val(data.baseEducation);
				specialtySmCt.val(data.studyMode);
				studyPeriodCt.val(data.studyPeriod);
				abitCountCt.val(data.plannedAbiturientsCount);
				groupsCountCt.val(data.plannedGroupsCount);
				trLevelCt.val(data.trainingLevel);
				trTypeCt.val(data.trainingType);

				if(data.qualifications){
					for(i=0; i<data.qualifications.length; i++){
						$('input.qualification-item[value=' + data.qualifications[i].id + ']')
							.prop('checked', 'checked');
					}
				}
				if(data.exams){
					
					if(data.exams.length>0)
						$('#exam').show();
					for(i=0; i<data.exams.length; i++){
						item = addExamItem();
						item.find('.exam-item-discipline').val(data.exams[i].disciplineId);
						item.find('.exam-item-type').val(data.exams[i].type);
						item.find('.exam-item-id').val(data.exams[i].id);
						item.find('.exam-item-date').val(data.exams[i].date);
						item.find('.exam-item-adres').val(data.exams[i].adres);
					}
				}

                if (data.adapted) {
                    radioAdapted.prop('checked', true);
                    checkboxNotAdapted.prop('checked', false);

                    for(i=0; i < data.adaptationTypes.length; i++){
                        $('input.adaptation-type-checkbox[value=' + data.adaptationTypes[i] + ']').prop('checked', 'checked');
                    }

                    adaptionTypeSelectBox.show();
                } else {
                    radioAdapted.prop('checked', false);
                    checkboxNotAdapted.prop('checked', true);
                    adaptationTypeCheckboxes.each(function() {
                        this.checked = false;
                    });
                    adaptionTypeSelectBox.hide();
                }

				dialog.data('id', data.id).modal('show');
			},
			alwaysCallback: function(){
				list.unmask();
			}
		});
		return false;
	});

	$('.delete-program-btn').on('click', function(event){
		var url = $(event.target).attr('href'),
			list = $('#specialty-list');

		list.mask('Отправка данных');
		SpoAjax({
			url: url,
			successCallback: function(){
				spoUrl.reload();
			},
//                    failureCallback: function(errors){
//                        alert(errors);
//                    },
			alwaysCallback: function(){
				list.unmask();
			}
		});
		return false;
	});

	dialog
		.on('hidden.bs.modal', function(){
			resetDialog();
		})
		.on('shown.bs.modal', function(){
		});

	$('.bind-specialty-request-btn').on('click', function(){

		var $btn        = $(this).button('Отправка данных'), url, urlParams,
			edProgramId = parseInt(dialog.data('id')),
			specialtyId = specialtyIdCt.val(),
			specialtyBe = specialtyBeCt.val(),
			specialtySm = specialtySmCt.val(),
			studyPeriod = studyPeriodCt.val(),
			abitCount   = abitCountCt.val(),
			groupsCount = groupsCountCt.val(),
			trLevel     = trLevelCt.val(),
			trType      = trTypeCt.val(),
			examList    = [],
			qualList    = [],
            adapted		= false,
            adaptationTypes = [];
        if (radioAdapted.is(":checked")) {
            adapted = true;

            adaptationTypeCheckboxes.each(function() {
                if ($(this).is(":checked")) {
                    adaptationTypes.push($(this).val());
                }
            });
        }
		if(edProgramId > 0){
			url         = $btn.data('update-url');
			urlParams   = new SpoUrl(url);
			urlParams.addParam('organizationSpecialtyId', edProgramId);
		}else{
			url         = $btn.data('url');
			urlParams   = new SpoUrl(url);
		}
		qualListCt.find('input[type=checkbox]:checked').each(function(index, item){
			qualList.push(parseInt($(item).val()));
		});

		examListCt.find('.exam-item').each(function(index, item){
			var dt,
				$select = $(item).find('.exam-item-discipline'),
				$type   = $(item).find('.exam-item-type'),
				$adres  = $(item).find('.exam-item-adres'),
				$date   = $(item).find('.exam-item-date'),
				id      = parseInt($(item).find('.exam-item-id').val());
			dt = {
				disciplineId: $select.val(),
				type:         $type.val(),
				date:         $date.val(),
				adres:        $adres.val(),
			};

			if(id > 0){
				dt.id = id;
			}
			examList.push(dt);
		});

		//urlParams
			//.addParam('specialtyId', specialtyId)
			//.addParam('nolayout', 1);
		errorList.html('');
		//var date=specialtyId+','+specialtyBe+','+specialtySm+','+examList+','+qualList+','+studyPeriod+','+','+abitCount+','+trLevel+','+trType+','+groupsCount+','+adapted+','+adaptationTypes;
		//alert(date);
		SpoAjax({
			url: urlParams.toString(),
			data: {
				specialtyId:            specialtyId,
				specialtyBaseEducation: specialtyBe,
				specialtyStudyMode:     specialtySm,
				examList:               examList,
				qualificationList:      qualList,
				studyPeriod:            studyPeriod,
				abitCount:              abitCount,
				trainingLevel:          trLevel,
				trainingType:           trType,
				groupsCount:            groupsCount,
                adapted: adapted,
                adaptationTypes: adaptationTypes
			},
			preventDefaultFailureCallback: true,
			sendDataAsJson: true,
			successCallback: function(){
				spoUrl.reload();
			},
			failureCallback: function(msg, data){
				var err;
				if(data.errors && msg === ''){
					for(err in data.errors){
						if(!data.errors.hasOwnProperty(err)){continue;}
						msg += data.errors[err] + '<br>';
					}
				}
				errorList.html(msg);
				dialog.scrollTop(0);
			},
			alwaysCallback: function(){
				$btn.button('reset');
			}
		});
	});

	specialtyIdCt.on('change', function(){
		var tpl = '', i,
			opt = $(this).find('option:selected'),
			qList = opt.data('qualifications');

		for(i=0; i<qList.length; i++){
			tpl += '<div class="checkbox">' +
				'<label>' +
				'<input class="qualification-item" name="Qualification[]" type="checkbox" value="' + qList[i].id + '">' +
				qList[i].title +
				'</label>' +
				'</div>';
		}

		$('.qualification-list').html(tpl);
	});

	examListCt.on('click', '.delete-exam-btn', function(){
		$(this).parents('.exam-item').remove();
	});

	$('.add-exam').on('click', function(){
		addExamItem();
	});

	function resetDialog(){
		dialog.data('id', 0);
		examListCt.html('');

		var spIdOpt = specialtyIdCt.find('option:first').val();
		errorList.html('');
		specialtyIdCt.val(spIdOpt).trigger('change').prop('disabled', false);
		specialtyBeCt.val('');
		specialtySmCt.val('');
		studyPeriodCt.val('');
		abitCountCt.val('');
		groupsCountCt.val('');
		trLevelCt.val('');
		trTypeCt.val('');
	}
});

addExamItem = function(){
	var opt, disciplineOptions = '', examTypeOptions = '';

	for(opt in disciplineList){
		disciplineOptions += '<option value="' + opt + '">' + disciplineList[opt] + '</option>';
	}
	for(opt in examTypeList){
		examTypeOptions += '<option value="' + opt + '">' + examTypeList[opt] + '</option>';
	}
	var adress=$('#adress').val();
	$('#exam').show();
	return $('.exam-list').append(
		'<tr class="exam-item">' +
			'<td>'+
				'<input type="hidden" class="exam-item-id" value="">' +
				'<select class="exam-item-discipline">' +
					disciplineOptions +
				'</select>' +
			'</td>' +
			'<td>' +
				'<select class="exam-item-type">' +
					examTypeOptions +
				'</select>' +
			'</td>' +
			'<td>' +
				'<input type="date" min="2015-01-01" class="exam-item-date" required>'+
			'</td>'+
			'<td>'+
				'<input type="text" class="exam-item-adres" value="'+adress+'">'+
			'</td>'+
			'<td>'+
				'<a class="delete-exam-btn btn btn-danger btn-xs" href="#"><i class="fa fa-times"></i></a>' +
			'</td>'+
			'</tr>'
	).find('.exam-item:last');
};