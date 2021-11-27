<?php
	include_once("config.php");
	include_once("functions.php");

	$search = $_GET['term'];

	$count_sql = $pdo->prepare("SELECT COUNT(DISTINCT(branch)) FROM ".$Database['tablename']." WHERE branch LIKE '%".$search."%'");
	$count_sql->execute();
	$count = $count_sql->fetchColumn();

	$all_sql = $pdo->prepare("SELECT COUNT(DISTINCT(branch)) FROM ".$Database['tablename']);
	$all_sql->execute();
	$countall = $all_sql->fetchColumn();

	$sql = $pdo->prepare("SELECT DISTINCT(branch) AS dbranch FROM ".$Database['tablename']." WHERE branch LIKE '%".$search."%' GROUP BY branch ORDER BY branch ASC"); 
	$sql->execute();

	$arr = array(); 
	if (($count > 0) && ($count != $countall)) {

		while($row = $sql->fetch(PDO::FETCH_ASSOC)){
			$data['value'] = $row['dbranch'];
			array_push($arr, $data);
		} 
	}
	echo json_encode($arr);
?>