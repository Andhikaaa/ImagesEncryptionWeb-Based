// global variabel tablePegawai
var tabelData;
var no = 1;

$(document).ready(function() {
	// Disable enter untuk submit topik, harus klik button submit
	$(window).keydown(function(event){
		if(event.keyCode == 13) {
		event.preventDefault();
		return false;
		}
	});

	tabelData = $("#tabelData").DataTable();

	$("#addTaskModalBtn").on('click', function() {
		// reset the form
		$("#task_form")[0].reset();
		// remove the error
		$(".form-group").removeClass('has-error').removeClass('has-success');
		$(".text-danger").remove();
		// empty the message div
		$(".messages").html("");
	});
});

function view(src){
	$('#imagepreview').attr('src', src);
	$('#view_modal').modal('show');
};

function addTask()
{
	if(validasi())
	{
		$("#task_form").submit();
	}
};

function validasi()
{
	$(".text-danger").remove();
	
	var images = $("#images").val();
	var key = $("#key").val();
	var iv = $("#iv").val();
	var task = $("input[name=task]:checked").val();
	
	if(images == "") {
		$("#images").closest('.form-group').addClass('has-error');
		$("#images").after('<p class="text-danger">No Images</p>');
	} else {
		$("#images").closest('.form-group').removeClass('has-error');
		$("#images").closest('.form-group').addClass('has-success');
	}

	if(key == "") {
		$("#key").closest('.form-group').addClass('has-error');
		$("#key").after('<p class="text-danger">Please input key</p>');
	} else {
		$("#key").closest('.form-group').removeClass('has-error');
		$("#key").closest('.form-group').addClass('has-success');
	}

	if(iv == "") {
		$("#iv").closest('.form-group').addClass('has-error');
		$("#iv").after('<p class="text-danger">Please input IV</p>');
	} else {
		$("#iv").closest('.form-group').removeClass('has-error');
		$("#iv").closest('.form-group').addClass('has-success');
	}

	if(task == undefined) {
		$("input[name=task]").closest('.form-group').addClass('has-error');
	} else {
		$("input[name=task]").closest('.form-group').removeClass('has-error');
		$("input[name=task]").closest('.form-group').addClass('has-success');
	}



	if(images && key && iv && task)
	{
		return true;
	}
	return false;
};