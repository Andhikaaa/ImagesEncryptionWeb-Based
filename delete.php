<?php
$db = new PDO('sqlite:job.db');

if(isset($_POST['id'])){
	// Get directory
	$result = $db->prepare('SELECT dir FROM Job WHERE id = ?');
	$result->execute(array($_POST['id']));
	$dir = $result->fetchAll();
	
	// Delete from sqlite
	$qry = $db->prepare('DELETE FROM Job WHERE id = ?');
	$qry->execute(array($_POST['id']));

	//unlink($dir[0]['dir']);
    
    echo "Data Deleted";
}
?>