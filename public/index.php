<?php
    if (!isset($_SESSION)) {
        session_start();
    }

    if (!isset($_SESSION["id"])) {
        header("Location: login.php");
        die();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Unifi RADIUS Manager</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
	<link href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet">
</head>
<body style="background-color: lightslategray">
<main>
	<div class="container" style="background-color: white">
		<header class="d-flex justify-content-center py-3">
			<ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Home</a>
                </li>
				<li class="nav-item">
					<a class="nav-link" href="radius.php">RADIUS</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="apgroup.php">AP Group</a>
				</li>
			</ul>
		</header>
	</div>
</main>
<div class="container" style="background-color: white">
    <h1>Simple Unifi Management Interface</h1>
    <p>Choose an option above.</p>
    <p><a href="https://github.com/upccjesse/simple-unifi">Github</a></p>
</div>
</body>
</html>
