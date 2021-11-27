<?php include("head.php") ?>

<?php
	include_once("config.php");
	include_once("functions.php");

	if (isset($_GET['trunk'])) {$trunk = trim($_GET['trunk']);} else {$trunk = "";}
	if (isset($_GET['branch'])) {$branch = trim($_GET['branch']);} else {$branch = "";}
	if (isset($_GET['node'])) {$node = preg_replace('/\\\\/','\\\\\\\\',trim($_GET['node']));} else {$node = "";}

	if ((!empty($trunk)) && (!empty($branch)) && (!empty($node))){
		$pdo->exec("INSERT INTO ".$Database['tablename']." (trunk,branch,node,hits,tracked,active) VALUES ('".$trunk."','".$branch."','".$node."',0,NOW(),1);");
		$id_sql = $pdo->prepare("
			SELECT *
			FROM ".$Database['tablename']." 
			WHERE trunk='".$trunk."' AND branch='".$branch."' AND node='".$node."';
		");
		$id_sql->execute();
		$id = $id_sql->fetchColumn();
		header("Location: ./confirm.php?id=".$id);
	} else {
		echo "All three fields must be filled in.";
	}

	echo "<div class='section'>";
	echo "<br><br>";
	echo "Create New Black/White List Entry";
	echo "<br><br>";

	echo "<table class='section'>";
		echo "<tr>
				<form action='add.php' method='GET' onsubmit='return confirm(\"Are you sure you want to add this entry?\");'>
				<td>Trunk:</td>
				<td>
					<input type='text' size='20' id='trunk' name='trunk'>
				</td>
			</tr>";
		echo "<tr>
				<td>Branch:</td>
				<td>
					<input type='text' size='20' id='branch' name='branch'>
				</td>
			</tr>";
		echo "<tr>
				<td>Node:</td>
				<td>
					<input type='text' size='32' name='node'>
				</td>
			</tr>";
		echo "<tr>
				<td>Submit:</td>
				<td>
					<input type='submit' name='submit' value='Submit' >
				</form>
				</td>
			</tr>";
	echo "</table>";

	echo "<br><br>";

	echo "</div>";

	// JS autocomplete trunk
	echo "
	<script>
	$(function() {
		$('#trunk').autocomplete({
			source: 'autocomplete-trunk.php',
			select: function( event, ui ) {
				event.preventDefault();
				$('#trunk').val(ui.item.value);
			}
		});
	});
	</script>";

	// JS autocomplete branch
	echo "
	<script>
	$(function() {
		$('#branch').autocomplete({
			source: 'autocomplete-branch.php',
			select: function( event, ui ) {
				event.preventDefault();
				$('#branch').val(ui.item.value);
			}
		});
	});
	</script>";

?>