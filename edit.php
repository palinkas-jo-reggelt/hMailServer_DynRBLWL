<?php include("head.php") ?>

<?php
	include_once("config.php");
	include_once("functions.php");

	if (isset($_GET['id'])) {$id = $_GET['id'];} else {$id = "";}

	if (isset($_GET['edit'])) {
		if ((isset($_GET['updatetrunk'])) && (isset($_GET['updatebranch'])) && (isset($_GET['updatenode'])) && (isset($_GET['updateactive']))) {
			$sql = "UPDATE ".$Database['tablename']." SET trunk='".$_GET['updatetrunk']."', branch='".$_GET['updatebranch']."', node='".preg_replace('/\\\\/','\\\\\\\\',trim($_GET['updatenode']))."', active='".$_GET['updateactive']."'  WHERE id='".$id."';";
			$pdo->exec($sql);
			echo "<script>window.close();</script>";
		}
	}
	if (isset($_GET['delete'])) {
		$sql = "DELETE FROM ".$Database['tablename']." WHERE id='".$id."';";
		$pdo->exec($sql);
		echo "<script>window.close();</script>";
	}

	echo "<div class='section'>";
	echo "<br><br>";
	echo "<b>ID: ".$id."</b>";
	echo "<br><br>";

	$sql = $pdo->prepare("SELECT * FROM ".$Database['tablename']." WHERE id = '".$id."';");
	$sql->execute();
	echo "<table class='section'>";
	while($row = $sql->fetch(PDO::FETCH_ASSOC)){
		echo "<form action='edit.php' method='GET' onsubmit='return confirm(\"Are you sure you want to change the record?\");'>
				<input type='hidden' name='id' value='".$row['id']."'>";
		echo "<tr>
				<td>Trunk:</td>
				<td>
					<input type='text' size='20' name='updatetrunk' value='".$row['trunk']."'>
				</td>
			</tr>";
		echo "<tr>
				<td>Branch:</td>
				<td>
					<input type='text' size='20' name='updatebranch' value='".$row['branch']."'>
				</td>
			</tr>";
		echo "<tr>
				<td>Node:</td>
				<td>
					<input type='text' size='32' name='updatenode' value='".$row['node']."'>
				</td>
			</tr>";
		echo "<tr>
				<td>Hits:</td>
				<td>".$row['hits']."</td>
			</tr>";
		echo "<tr>
				<td>Last Hit:</td>
				<td>".date("y/n/j G:i:s", strtotime($row['tracked']))."</td>
			</tr>";
		if ($row['active']==0) {
			$active="<select name='updateactive'><option value=1>No</option><option value=1>Yes</option></select>";
		} else {
			$active="<select name='updateactive'><option value=1>Yes</option><option value=0>No</option></select>";
		}
		echo "<tr>
				<td>Active:</td>
				<td>".$active."</td>
			</tr>";
		echo "<tr>
				<td>Delete:</td>
				<td><input type='submit' name='delete' value='Delete' ></td>
			</tr>";
		echo "<tr>
				<td>Edit:</td>
				<td><input type='submit' name='edit' value='Edit' ></td>
				</form>
			</tr>";
	}
	echo "</table>";

	echo "<br><br>";

	echo "</div>";

?>