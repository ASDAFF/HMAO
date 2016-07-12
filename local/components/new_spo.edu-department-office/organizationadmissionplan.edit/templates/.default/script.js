$(function(){

	$('#btn-accept').on('click', function(event) {
        $('#inputAdmissionPlanStatus').attr('value', statusAccepted);
        $('#changeStatusForm').submit();
	});

    $('#btn-decline').on('click', function(event) {
        $('#inputAdmissionPlanStatus').attr('value', statusDeclined);
    });

});

