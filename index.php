<?php include("head.php") ?>

<?php
	include_once("config.php");
	include_once("functions.php");

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$display_pagination = 1;
	} else {
		$page = 1;
		$total_pages = 1;
		$display_pagination = 0;
	}
	if (isset($_GET['search'])) {$search = trim($_GET['search']);} else {$search = "";}
	if (isset($_GET['clear'])) {
		header("Location: index.php");
	}
	if (isset($_GET['trunk'])) {
		$trunk = trim($_GET['trunk']);
		$branch_option_sql = "SELECT DISTINCT(branch) AS branch_title FROM ".$Database['tablename']." WHERE trunk='".$trunk."';";
	} else {
		$trunk = "";
		$branch_option_sql = "SELECT DISTINCT(branch) AS branch_title FROM ".$Database['tablename'].";";
	}
	if (isset($_GET['branch'])) {$branch = trim($_GET['branch']);} else {$branch = "";}

	if ($search=="") {$search_ph="";} else {$search_ph=$search;}
	if ($trunk=="") {$trunk_ph="Trunk";} else {$trunk_ph=$trunk;}
	if ($branch=="") {$branch_ph="Branch";} else {$branch_ph=$branch;}

	echo "<div class='section'><div style='line-height:24px;'>";
	echo "<form autocomplete='off' id='myForm' action='index.php' method='GET'> ";
	echo	"<br><select name='trunk' onchange='this.form.submit()'>";
	echo		"<option value='".$trunk."'>".$trunk_ph."</option>";
	$sql = $pdo->prepare("SELECT DISTINCT(trunk) AS trunk_title FROM ".$Database['tablename'].";");
	$sql->execute();
	while($row = $sql->fetch(PDO::FETCH_ASSOC)){
		echo "<option value=".$row['trunk_title'].">".$row['trunk_title']."</option>";
	}
	echo	"</select> ";
	echo	" <select name='branch' onchange='this.form.submit()'>";
	echo		"<option value='".$branch."'>".$branch_ph."</option>";
	$sql = $pdo->prepare($branch_option_sql);
	$sql->execute();
	while($row = $sql->fetch(PDO::FETCH_ASSOC)){
		echo "<option value=".$row['branch_title'].">".$row['branch_title']."</option>";
	}
	echo	"</select> ";
	echo	" <input type='text' size='20' name='search' placeholder='Search Term...' value='".$search_ph."'> ";
	echo	" <input type='submit' name='submit2' value='Search' >";
	echo 	" <button class='button' type='submit' name='clear'>Reset</button>";
	echo "</form>";
	echo "</div></div>";
	echo "<div class='section'>";

	if ($search==""){$search_SQL = "";} else {$search_SQL = " AND data='".$search."'";}
	if ($trunk==""){$trunk_SQL = "";} else {$trunk_SQL = " AND trunk='".$trunk."'";}
	if ($branch==""){$branch_SQL = "";} else {$branch_SQL = " AND branch='".$branch."'";}

	$offset = ($page-1) * $no_of_records_per_page;
	
	$total_pages_sql = $pdo->prepare("
		SELECT Count( * ) AS count 
		FROM ".$Database['tablename']." 
		WHERE node LIKE '%".$search."%'".$trunk_SQL.$branch_SQL."
	");
	$total_pages_sql->execute();
	$total_rows = $total_pages_sql->fetchColumn();
	$total_pages = ceil($total_rows / $no_of_records_per_page);

	$sql = $pdo->prepare("
		SELECT *, (hits > 0) as hitsgt FROM ".$Database['tablename']." 
		WHERE node LIKE '%".$search."%'".$trunk_SQL.$branch_SQL."
		ORDER BY active DESC, hitsgt DESC, tracked DESC, trunk ASC, branch ASC, node ASC
		LIMIT ".$offset.", ".$no_of_records_per_page
	);
	$sql->execute();

	if ($search==""){
		$search_res="";
	} else {
		$search_res=" for search term \"<b>".$search."</b>\"";
	}

  	if ($trunk==""){
		$trunk_res="";
	} else {
		$trunk_res=" under trunk \"<b>".$trunk."</b>\"";
	}

	if ($branch==""){
		$branch_res="";
	} else {
		$branch_res=" having branch \"<b>".$branch."</b>\"";
	}

	if ($total_pages < 2){
		$pagination = "";
	} else {
		$pagination = "(Page: ".number_format($page)." of ".number_format($total_pages).")";
	}

	if ($total_rows == 1){$singular = '';} else {$singular= 's';}
	if ($total_rows == 0){
		if ($search == "" && $trunk == "" && $branch == ""){
			echo "Please enter a search term";
		} else {
			echo "No results ".$search_res.$trunk_res.$branch_res;
		}	
	} else {
		echo "<span style='font-size:0.8em;'>Results ".$search_res.$trunk_res.$branch_res.": ".number_format($total_rows)." Record".$singular." ".$pagination."<br></span>";
		echo "
			<div class='div-table'>
				<div class='div-table-row-header'>
					<div class='div-table-col'>Trunk</div>
					<div class='div-table-col'>Branch</div>
					<div class='div-table-col'>Node</div>
					<div class='div-table-col'>Last</div>
					<div class='div-table-col center'>Hits</div>
					<div class='div-table-col center'>Active</div>
				</div>";
		
		while($row = $sql->fetch(PDO::FETCH_ASSOC)){
			echo "<div class='div-table-row'>";
				echo "<div class='div-table-col' data-column='Trunk'>".$row['trunk']."</div>";
				echo "<div class='div-table-col' data-column='Branch'>".$row['branch']."</div>";
				echo "<div class='div-table-col' data-column='Node'><a onClick=\"window.open('./edit.php?id=".$row['id']."','EDIT','resizable,height=420,width=420'); return false;\">".$row['node']."</a></div>";
				echo "<div class='div-table-col' data-column='Last'>".date("y/m/d H:i:s", strtotime($row['tracked']))."</div>";
				echo "<div class='div-table-col center' data-column='Hits'>".$row['hits']."</div>";
				if ($row['active']==1) {$display_active="Y";} else {$display_active="N";}
				echo "<div class='div-table-col center' data-column='Active'>".$display_active."</div>";
			echo "</div>";
		}
		echo "</div>"; // End table

		if ($search==""){$search_page = "";} else {$search_page = "&search=".$search;}
		if ($trunk==""){$trunk_page = "";} else {$trunk_page = "&trunk=".$trunk;}
		if ($branch==""){$branch_page = "";} else {$branch_page = "&branch=".$branch;}
		
		if ($total_pages == 1){echo "";}
		else {
			echo "<span style='font-size:0.8em;'><ul>";
			if($page <= 1){echo "<li>First </li>";} else {echo "<li><a href=\"?page=1".$search_page.$trunk_page.$branch_page."\">First </a><li>";}
			if($page <= 1){echo "<li>Prev </li>";} else {echo "<li><a href=\"?page=".($page - 1).$search_page.$trunk_page.$branch_page."\">Prev </a></li>";}
			if($page >= $total_pages){echo "<li>Next </li>";} else {echo "<li><a href=\"?page=".($page + 1).$search_page.$trunk_page.$branch_page."\">Next </a></li>";}
			if($page >= $total_pages){echo "<li>Last</li>";} else {echo "<li><a href=\"?page=".$total_pages.$search_page.$trunk_page.$branch_page."\">Last</a></li>";}
			echo "</ul></span>";
		}
	}
?>

<br>
</div> <!-- end of section -->

<?php include("foot.php") ?>