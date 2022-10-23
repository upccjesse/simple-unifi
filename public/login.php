<?php

	$username = $_REQUEST["username"] ?? null;
	$password = $_REQUEST["password"] ?? null;

	if (ctype_print($username) && ctype_print($password)) {
		header('HTTP/1.1 200 OK');
		$pdo = new \PDO("sqlite:" . __DIR__ . "/../database.db");
		$statement = $pdo->prepare("SELECT * FROM login WHERE email = :email");
		$statement->execute([':email' => $username]);
        $user = $statement->fetch();
        if (password_verify($password, $user["password"])) {
			if (!isset($_SESSION)) {
				session_start();
			}
            $_SESSION["id"] = base64_encode(random_bytes(10));
			$_SESSION["username"] = $username;
			$_SESSION["permissions"] = [];
			$_SESSION["permissions"]["radius"] = $user["radius"];
			$_SESSION["permissions"]["ap_group"] = $user["ap_group"];
			header('HTTP/1.1 200 OK');
			die();
        }
		header('HTTP/1.1 401 Unauthorized');
		die();
	}
	elseif (isset($username) || isset($password)) {
		header('HTTP/1.1 401 Unauthorized');
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
</head>
<body style="background-color: lightslategray">
	<main>
		<div class="container" style="background-color: white; margin-top: 48px;">
			<div style="padding: 12px 48px">
				<form>
					<!-- Email input -->
					<div class="form-outline mb-4">
						<input type="email" id="form2Example1" name="username" class="form-control" />
						<label class="form-label" for="form2Example1">Email address</label>
					</div>

					<!-- Password input -->
					<div class="form-outline mb-4">
						<input type="password" id="form2Example2" name="password" class="form-control" />
						<label class="form-label" for="form2Example2">Password</label>
					</div>

					<!-- 2 column grid layout for inline styling -->
					<div class="row mb-4">
						<div class="col d-flex justify-content-center">
							<!-- Checkbox -->
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="" id="form2Example31" checked />
								<label class="form-check-label" for="form2Example31"> Remember me </label>
							</div>
						</div>
					</div>

					<!-- Submit button -->
					<button type="button" class="btn btn-primary btn-block mb-4" onclick="$('form').submit()">Sign in</button>
			</form>
				<div id="alerts"></div>
			</div>
		</div>
	</main>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
	<script
		src="https://code.jquery.com/jquery-3.6.1.min.js"
		integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ="
		crossorigin="anonymous"></script>
	<script>
		$("form").on("submit", function(e) {
		    e.preventDefault()
            let form = $("form")
            let url = "login.php"

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize()
            }).done(function(data) {
                $("#alerts").html("")
                window.location.href = "index.php"
                setTimeout(function() {

                }, 1000)
            }).fail(function(data) {
                $("#alerts").html("")
				$("#alerts").html("<div class='alert alert-warning'>Failed to login</div>")
                setTimeout(function() {

                }, 1000)
            }).always(function() {

			})
		})
	</script>
</body>
</html>
