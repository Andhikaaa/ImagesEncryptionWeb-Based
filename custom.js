$(document).ready(function () {
    fetch_data()

    function fetch_data(){
        $('#task_table').DataTable({
            responsive: true,
            "processing": true,
            "serverSide": true,
            "info": true,
            "stateSave": true,
            "ajax":{
                url :"fetch.php", // json datasource
                type: "post",  // method  , by default get
                error: function(){  // error handling
                    $(".task_table-error").html("");
                    $("#task_table").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#task_table_processing").css("display","none");
                },  
            },
            "columns": [
                { "data": "name" },
                { "data": "task" },
                { "data": "key" },
                { "data": "iv" },
                { "data": "status" },
                { "data": "action" }
            ]
        });
    }

    $(document).on('click', '.view', function(){
        var id = $(this).attr("id");
        $('#imagepreview').attr('src', id);
        $('#view_modal').modal('show');
    });

    $("#task_form").submit(function(e){
        e.preventDefault();
        $("#add_task_modal").addClass('loading');
        
        var form_data = new FormData(this); 
        /*
        console.log(form_data);
        for (var pair of form_data.entries()) {
            console.log(pair[0]+ ', ' + pair[1]); 
        }*/
        
        $.ajax({
            url:"add_task.php",
            method:"POST",
            data: form_data,
            processData: false,
            contentType: false,
            success:function(data)
            {
                $('#alert_message').html('<div class="alert alert-success">'+data+'</div>');
                $("#add_task_modal").removeClass('loading');
                $('#add_task_modal').modal('hide');
                $("#task_form")[0].reset();
                $('#task_table').DataTable().destroy();
                    fetch_data();
                }
            });
            setInterval(function(){
                $('#alert_message').html('');
            }, 5000);
            
    })

    $(document).on('click', '#add_task', function(){
        // validate input
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
            images = true;
        }
    
        if(key == "") {
            $("#key").closest('.form-group').addClass('has-error');
            $("#key").after('<p class="text-danger">Please input key</p>');
        }
        else if(key.length < 10){
            $("#key").closest('.form-group').addClass('has-error');
            $("#key").after('<p class="text-danger">Key length is 80 bit in hex</p>');
        }
        else {
            $("#key").closest('.form-group').removeClass('has-error');
            $("#key").closest('.form-group').addClass('has-success');
            key = true;
        }
    
        if(iv == "") {
            $("#iv").closest('.form-group').addClass('has-error');
            $("#iv").after('<p class="text-danger">Please input IV</p>');
        }
        else if(key.length < 8){
            $("#key").closest('.form-group').addClass('has-error');
            $("#key").after('<p class="text-danger">IV length is 64 bit in hex</p>');
        }
        else {
            $("#iv").closest('.form-group').removeClass('has-error');
            $("#iv").closest('.form-group').addClass('has-success');
            iv = true;
        }
    
        if(task == undefined) {
            $("input[name=task]").closest('.form-group').addClass('has-error');
        } else {
            $("input[name=task]").closest('.form-group').removeClass('has-error');
            $("input[name=task]").closest('.form-group').addClass('has-success');
            task = true;
        }
    
    
        if(images && key && iv && task)
        {
            $("#task_form").submit();
        }
    });
    
    $('#add_task_modal').on('hidden.bs.modal', function (e) {
        $(".text-danger").remove();
        $("#images").closest('.form-group').removeClass('has-error');
        $("#images").closest('.form-group').removeClass('has-success');
        $("#key").closest('.form-group').removeClass('has-error');
        $("#key").closest('.form-group').removeClass('has-success');
        $("#iv").closest('.form-group').removeClass('has-error');
        $("#iv").closest('.form-group').removeClass('has-success');
        $("input[name=task]").closest('.form-group').removeClass('has-error');
        $("input[name=task]").closest('.form-group').removeClass('has-success');
    })
    
    $(document).on('show.bs.modal', '#confirm-delete', function(e) {
        var id = $(e.relatedTarget).data('id');
        $('.btn-ok', this).data('id', id);
    });

    $('#confirm-delete').on('click', '.btn-ok', function(e) {
        
        var $modalDiv = $(e.delegateTarget);
        var id = $(this).data('id');
        
        $modalDiv.addClass('loading');
        $.ajax({
            url:"delete.php",
            method:"POST",
            data:{id:id},
            success:function(data){
                $('#alert_message').html('<div class="alert alert-success">'+data+'</div>');
                $('#task_table').DataTable().destroy();
                $modalDiv.modal('hide').removeClass('loading');
                fetch_data();
            }
        });
        setInterval(function(){
            $('#alert_message').html('');
        }, 5000);
    });

    var updateTable = true;
    //Track Progress using SSE
    if(typeof(EventSource) !== "undefined") {
        var source = new EventSource("Job.php");
        source.onmessage = function(event) {
            var data = JSON.parse(event.data);
            if(data.id != null){
                if(data.progress == '100'){
                    var table= $('#task_table').DataTable();
                    var row = table.row( '#' + data.id);
        
                    var status = 
                    '<div class="progress"> \
                        <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" \
                            aria-valuenow="' + data.progress +'"aria-valuemin="0" aria-valuemax="100" style="width:' + data.progress + '%"> \
                            ' + data.progress + '% Complete \
                        </div> \
                    </div>';
                    //table.cell(row, 4).data(status).draw();
                    //myDataTable.cell(row, 2).data("New Text").draw();
                    $('#task_table').dataTable().fnUpdate(status , $('tr#' + data.id), 4, updateTable );
                    updateTable = false;
                }
                else{
                    var table= $('#task_table').DataTable();
                    var row = table.row( '#' + data.id);
        
                    var status = 
                    '<div class="progress"> \
                        <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" \
                            aria-valuenow="' + data.progress +'"aria-valuemin="0" aria-valuemax="100" style="width:' + data.progress + '%"> \
                            ' + data.progress + '% Complete \
                        </div> \
                    </div>';
                    //table.cell(row, 4).data(status).draw();
                    //myDataTable.cell(row, 2).data("New Text").draw();
                    $('#task_table').dataTable().fnUpdate(status , $('tr#' + data.id), 4 , false);
                    updateTable = true;
                }
                //{"id":null,"progress":null}
            }
            //console.log(event.data);
        };
    } else {
        //document.getElementById("result").innerHTML = "Sorry, your browser does not support server-sent events...";
    }
});