<!DOCTYPE html>
<html>
<head>
	<link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- <link href="css/style.css" rel="stylesheet"> -->
    <?php
        require_once("db/db.php");
        require_once("data/from_db.php");
    ?>
</head>
<body>


	<table style="width: 100%; background-color: lightgreen;">
	<tr>
		<td width=33%>
			<a href="https://fantasy.premierleague.com/" target="_blank">
                <img id="home" src="i/epl.png" style="width: 10%; float: left; padding-left: 10px;">
            </a>
        </td>
		<td width="33%">
			<h3 style="text-align: center; margin-bottom: 10px;">FPL Prodigies</h3>
		</td>
		<td width="33%">
			<h5 style="text-align: right; padding-right: 10px;">User Name</h5>
		</td>
	</tr>
	</table>

    <script type="text/javascript" src="js/jq.js"></script>
    <script type="text/javascript" src="js/test.js"></script>
    <script type="text/javascript" src="js/dt.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
</body>
</html>