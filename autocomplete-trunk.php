<?php
	include_once("config.php");
	include_once("functions.php");

	$search = $_GET['term'];

	$count_sql = $pdo->prepare("SELECT COUNT(DISTINCT(trunk)) FROM ".$Database['tablename']." WHERE trunk LIKE '%".$search."%'");
	$count_sql->execute();
	$count = $count_sql->fetchColumn();

	$all_sql = $pdo->prepare("SELECT COUNT(DISTINCT(trunk)) FROM ".$Database['tablename']);
	$all_sql->execute();
	$countall = $all_sql->fetchColumn();

	$sql = $pdo->prepare("SELECT DISTINCT(trunk) AS dtrunk FROM ".$Database['tablename']." WHERE trunk LIKE '%".$search."%' GROUP BY trunk ORDER BY trunk ASC"); 
	$sql->execute();

	$arr = array(); 
	if (($count > 0) && ($count != $countall)) {

		while($row = $sql->fetch(PDO::FETCH_ASSOC)){
			$data['value'] = $row['dtrunk'];
			array_push($arr, $data);
		} 
	}
	echo json_encode($arr);
?>