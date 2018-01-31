<?php
require 'php-resque/vendor/autoload.php';
$db = new PDO('sqlite:job.db');

if(isset($_FILES['images'])){
	// Count uploaded files
	$len_files = count($_FILES['images']['tmp_name']);
	$all_id = array();

	// Iterate through the array of files
	for($i=0; $i < $len_files; $i++){
		// Check if file was upload via HTTP POST
		if(is_uploaded_file($_FILES['images']['tmp_name'][$i])){
			$filename = basename($_FILES['images']['name'][$i]);
			$task = $_POST['task'];
			$key = $_POST['key'];
			$iv = $_POST['iv'];
			$status = '0';

			$uploaddir = 'assets/images/task/';
			$dir = $uploaddir . time() . $filename;
			move_uploaded_file($_FILES['images']['tmp_name'][$i], $dir);

			
			// Add to SQlite
			$qry = $db->prepare('INSERT INTO Job (name, task, key, iv, status, dir) VALUES (?, ?, ?, ?, ?, ?)');
			$qry->execute(array($filename, $task, $key, $iv, $status, $dir));
			$id = $db->lastInsertID();
			$all_id[] = $id;
			// Add to queue job
			$id = Resque::enqueue('default', 'Job', [
				'name' => $filename,
				'task' => $task,
				'key' => $key,
				'iv' => $iv,
				'dir' => $dir,
				'id' => $id,
			], true);
            //echo 'Queued job ' . $id;
		}
	}
	$data = array('all_id' => $all_id, 'message' => $len_files . ' Task Added');
	echo json_encode($data);
}
?>