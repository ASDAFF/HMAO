initDateTimePicker = function(){
    var selectorIds = [], i,
        datePickerIds = [
            'inputStartDate',
            'inputEndDate'
        ];

    for(i=0; i<datePickerIds.length; i++){
        selectorIds.push('#' + datePickerIds[i]);
    }

    $(selectorIds.join(',')).datetimepicker({
        format: 'd-m-Y',
        lang: 'ru',
        timepicker: false,
        dayOfWeekStart: 1,
        minDate:  '01/01/' + selectedYear,
        maxDate: '31/12/' + selectedYear,
        formatDate: 'd/m/Y'
    });
};

$(function(){

    initDateTimePicker();

	$('.edit-admission-plan-link').on('click', function(event){

        var organizationSpecialtyId  = $(event.target).attr('data-organization-specialty-id');
        $('#inputOrganizationSpecialtyId').attr('value', organizationSpecialtyId);

        var admissionPlanData = $('#admissionPlan' + organizationSpecialtyId);

        if (admissionPlanData.length > 0) {
            $('#inputStartDate').attr('value', admissionPlanData.find('.startDate').text());
            $('#inputEndDate').attr('value', admissionPlanData.find('.endDate').text());
            $('#inputGrantStudentsNumber').attr('value', admissionPlanData.find('.grantStudentsNumber').text());
            $('#inputTuitionStudentsNumber').attr('value', admissionPlanData.find('.tuitionStudentsNumber').text());
        }

	});

});

