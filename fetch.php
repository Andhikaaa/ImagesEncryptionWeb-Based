<?php
/* Database connection to sqlite */
$db = new PDO('sqlite:job.db');


// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
	0 =>'id', 
	1 => 'name',
    2 => 'task',
    3 => 'key',
    4 => 'iv',
    5 => 'status',
    6 => 'dir'
);

// getting total number records without any search
$result = $db->query('SELECT COUNT(*) FROM Job');
$totalData = $result->fetch()[0];
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if( !empty($requestData['search']['value']) ) {
	// if there is a search parameter
	$sql = "SELECT * FROM Job ";
	$sql.=" WHERE name LIKE '".$requestData['search']['value']."%' ";    // $requestData['search']['value'] contains search parameter
	$sql.=" OR task LIKE '".$requestData['search']['value']."%' ";
    $sql.=" OR key LIKE '".$requestData['search']['value']."%' ";
    $sql.=" OR iv LIKE '".$requestData['search']['value']."%' ";
    $sql.=" OR status LIKE '".$requestData['search']['value']."%' ";

    $query = $db->query($sql);
    $result = $query->fetchAll();
	$totalFiltered = count($result); // when there is a search parameter then we have to modify total number filtered rows as per search result without limit in the query 

    $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   "; // $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc , $requestData['start'] contains start row number ,$requestData['length'] contains limit length.
	$query = $db->query($sql);
	
} 
else {	
	$sql = "SELECT * FROM Job ";
	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	$query = $db->query($sql);
}

$data = array();
while( $row = $query->fetch() ) {  // preparing an array
	$nestedData=array(); 
	$nestedData['DT_RowId'] = $row["id"];
	$nestedData['name'] = $row["name"];
    $nestedData['task'] = $row["task"];
    $nestedData['key'] = $row["key"];
    $nestedData['iv'] = $row["iv"];
    $nestedData['status'] = 
        '<div class="progress">
            <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="'.$row["status"].'"
                aria-valuemin="0" aria-valuemax="100" style="width:'.$row["status"].'%">
                '.$row["status"]. '% Complete
            </div>
        </div>
        ';
    $nestedData['action'] = 
        '<button class="btn btn-primary view" id="'.$row['dir'].'">   
            <span class="glyphicon glyphicon-zoom-in"></span>
        </button>
        <a href="'.$row['dir'].'" download>
            <button class="btn btn-success" id="download_btn">
                <span class="glyphicon glyphicon-download-alt"></span>
            </button>
        </a>
        <button class="btn btn-danger" data-toggle="modal" data-target="#confirm-delete" data-id="'.$row['id'].'">   
            <span class="glyphicon glyphicon-trash"></span>
        </button>';
	
	$data[] = $nestedData;
}

$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
			);
echo json_encode($json_data);  // send data as json format
?>