<?php
require 'php-resque/vendor/autoload.php';
$db = new PDO('sqlite:job.db');

if(isset($_FILES['images'])){
	// Count uploaded files
	$len_files = count($_FILES['images']['tmp_name']);

	// Iterate through the array of files
	for($i=0; $i < $len_files; $i++){
		// Check if file was upload via HTTP POST
		if(is_uploaded_file($_FILES['images']['tmp_name'][$i])){
			$filename = basename($_FILES['images']['name'][$i]);
			$task = $_POST['task'];
			$key = $_POST['key'];
			$iv = $_POST['iv'];
			$status = 'Waiting';

			$uploaddir = 'assets/images/task/';
			$dir = $uploaddir . $filename;
			move_uploaded_file($_FILES['images']['tmp_name'][$i], $dir);

			
			// Add to SQlite
			$qry = $db->prepare('INSERT INTO Job (name, task, key, iv, status, dir) VALUES (?, ?, ?, ?, ?, ?)');
			$qry->execute(array($filename, $task, $key, $iv, $status, $dir));
			
			// Add to queue job
			$id = Resque::enqueue('default', 'Job', [
				'name' => $filename,
				'task' => $task,
				'key' => $key,
				'iv' => $iv,
				'dir' => $dir,
				'id' => $db->lastInsertID()
			], true);
		
			//echo 'Queued job ' . $id;
		}
	}
}

if(isset($_GET['delete_id'])){
	// Get directory
	$result = $db->prepare('SELECT dir FROM Job WHERE id = ?');
	$result->execute(array($_GET['delete_id']));
	$dir = $result->fetchAll();
	unlink($dir[0]['dir']);
	
	// Delete from sqlite
	$qry = $db->prepare('DELETE FROM Job WHERE id = ?');
	$qry->execute(array($_GET['delete_id']));
}

$result = $db->query("SELECT * FROM Job");
?>

<html>
<head>
	<title>WEBSOCKET SUBSCRIBER</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- bootstrap css -->
	<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
	<!-- datatables css -->
	<link rel="stylesheet" type="text/css" href="assets/css/dataTables.bootstrap.min.css">
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<center><h1 class="page-header">IMAGES ENCRYPTION USING GRAIN STREAM CIPHER</h1> </center>

				<div class="removeMessages"></div>
				<p>Refresh to update</p>
				<button class="btn btn-primary pull pull-right" data-toggle="modal" data-target="#add_task_modal" id="addTaskModalBtn">
					<span class="glyphicon glyphicon-plus-sign"></span>	Task
				</button>

				<br /> <br /> <br />
				<!--Datatable-->
				<table class="table" id="tabelData">
					<thead>
						<tr>
							<th width="40">No</th>
							<th>Name</th>
							<th>Task</th>
							<th>Key</th>
							<th>IV</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php $count = 1; ?>
						<?php while( $row = $result->fetch()) : ?>
							<tr>
								<td><?php echo $count ?></td>
								<td><?php echo $row['name'];?></td>
								<td><?php echo $row['task'];?></td>
								<td><?php echo $row['key'];?></td>
								<td><?php echo $row['iv'];?></td>
								<td><?php echo $row['status'];?></td>
								<td>
									<button class="btn btn-primary" id="view_btn" onclick="<?php echo "view('" . $row['dir'] . "')"; ?> ">
										<span class="glyphicon glyphicon-zoom-in"></span>
									</button>
									<a href="<?php echo $row['dir']; ?>" download>
										<button class="btn btn-success" id="download_btn">
											<span class="glyphicon glyphicon-download-alt"></span>
										</button>
									</a>
									<a href="index.php?delete_id=<?php echo $row['id']; ?>">
										<button type="submit" class="btn btn-danger" id="delete_btn">
											<span class="glyphicon glyphicon-trash"></span>
										</button>
									</a>
								</td>
							</tr>
						<?php $count++; endwhile ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- view modal -->
	<!-- Creates the bootstrap modal where the image will appear -->
	<div class="modal fade" id="view_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Image Preview</h4>
			</div>
			<div class="modal-body">
				<img src="" id="imagepreview" style="width: 400px; height: 264px;" >
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
			</div>
		</div>
	</div>
	<!-- end view modal -->

	<!-- add modal -->
	<div class="modal fade" tabindex="-1" role="dialog" id="add_task_modal">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Add Task</h4>
				</div>

				<form class="form-horizontal" action="index.php" method="POST" id="task_form" enctype="multipart/form-data">
					<div class="modal-body">
						<div class="messages"></div> <!--notifikasi pesan -->
						<div class="form-group">
							<div class="col-sm-2 control-label">
								<label for="images">Images</label>
							</div>
							<div class="col-sm-10">
								<input type="file"  id="images" name="images[]" accept="image/png" multiple/>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-2 control-label">
								<label for="key">Key</label>
							</div>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="key" name="key" placeholder="0000000000000000">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-2 control-label">
								<label for="iv">IV</label>
							</div>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="iv" name="iv" placeholder="0000000000000000">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-2 control-label">
								<label for="task">Task</label>
							</div>
							<div class="col-sm-10">
								<label class="radio-inline"><input type="radio" id="task_encrypt" name="task" value="Encrypt">Encrypt</label>
								<label class="radio-inline"><input type="radio" id="task_decrypt" name="task" value="Decrypt">Decrypt</label>
							</div>
						</div>							
					</div>
				</form>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" onclick="addTask()">Submit</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- /add modal -->
	
	<!-- jquery plugin -->
	<script type="text/javascript" src="assets/js/jquery.js"></script>
	<!-- bootstrap js -->
	<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
	<!-- datatables js -->
	<script type="text/javascript" src="assets/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="assets/js/dataTables.bootstrap.min.js"></script>
	<!-- include custom index.js -->
	<script type="text/javascript" src="script.js"></script>
</body>
</html>