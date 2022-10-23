<?php

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    die();
}
elseif (!$_SESSION["permissions"]["radius"]) {
    echo "Unauthorized to see this page.";
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
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="radius.php">RADIUS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="apgroup.php">AP Group</a>
                    </li>
                </ul>
            </header>
        </div>
        <div class="container-md" style="background-color: white">
            <button style="margin-bottom: 14px;" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal">
                New User
            </button>
            <table class="display">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Password</th>
                        <th>Type</th>
                        <th>VLAN</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal -->
    <div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add new User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="newForm">
                        <div class="mb-3">
                            <label for="u" class="form-label">Username</label>
                            <input required type="text" class="form-control" id="u" name="u" />
                        </div>
                        <div class="mb-3">
                            <label for="p" class="form-label">Password</label>
                            <input required type="text" class="form-control" id="p" name="p" />
                        </div>
                        <div class="mb-3">
                            <label for="v" class="form-label">VLAN</label>
                            <input required type="number" min="2" max="4094" class="form-control" id="v" name="v" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="newUser(this)">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editModalLabel">Edit User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" name="id" id="id" />
                        <div class="mb-3">
                            <label for="uu" class="form-label">Username</label>
                            <input required type="text" class="form-control" id="uu" name="u" />
                        </div>
                        <div class="mb-3">
                            <label for="pp" class="form-label">Password</label>
                            <input required type="text" class="form-control" id="pp" name="p" />
                        </div>
                        <div class="mb-3">
                            <label for="vv" class="form-label">VLAN</label>
                            <input required type="number" min="2" max="4094" class="form-control" id="vv" name="v" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="editUser(this)">Edit User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="deleteModalLabel">Delete User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this RADIUS User?</p>
                    <form id="deleteForm">
                        <input type="hidden" name="id" id="idd" />
                        <div class="mb-3">
                            <label for="uuu" class="form-label">Username</label>
                            <input disabled type="text" class="form-control" id="uuu" name="u" />
                        </div>
                        <div class="mb-3">
                            <label for="ppp" class="form-label">Password</label>
                            <input disabled type="text" class="form-control" id="ppp" name="p" />
                        </div>
                        <div class="mb-3">
                            <label for="vvv" class="form-label">VLAN</label>
                            <input disabled type="number" min="2" max="4094" class="form-control" id="vvv" name="v" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="deleteUser(this)">Delete User</button>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <script
            src="https://code.jquery.com/jquery-3.6.1.min.js"
            integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ="
            crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

    <script>
        let table
        $(() => {
            table = $("table").DataTable({
                ajax: "get.php?func=table",
                columns: [
                    { data: 0 },
                    { data: 1 },
                    { data: 2 },
                    { data: 3 },
                    {
                        data: 4,
                        render: function(data, type, row) {
                            let param = JSON.stringify(row)
                            let edit = "<button type=button class='btn btn-primary btn-sm' onclick='showEdit(`" + param + "`)'><i class=\"bi bi-pencil\"></i></button>"
                            let del = "<button type=button class='btn btn-danger btn-sm' onclick='showDelete(`" + param + "`)'><i class=\"bi bi-trash\"></i></button>"
                            return "<div class='container gx-4'>" + edit + del + "</div>"
                        }
                    },
                ]
            })
        })

        function newUser(button) {
            let form = $("#newForm")
            let url = "set.php?func=new-user"

            let origHtml = $(button).html()
            $(button).html("Saving...")
            $(button).attr("disabled", "true")
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize()
            }).done(function(data) {
                table.ajax.reload()
                $(button).removeClass("btn-primary")
                $(button).addClass("btn-success")
                $(button).html("Success...")
                setTimeout(function() {
                    $("#newModal").modal("hide")
                    $(button).html(origHtml)
                    $(button).removeClass("btn-success")
                    $(button).addClass("btn-primary")
                    $(button).removeAttr("disabled")
                }, 1000)
            }).fail(function(data) {
                $(button).removeClass("btn-primary")
                $(button).addClass("btn-warning")
                $(button).html("Failed...")
                setTimeout(function() {
                    $(button).html(origHtml)
                    $(button).removeClass("btn-warning")
                    $(button).addClass("btn-primary")
                    $(button).removeAttr("disabled")
                }, 1000)
            })
        }

        function showEdit(data) {
            data = JSON.parse(data)
            $("#uu").val(data[0])
            $("#pp").val(data[1])
            $("#vv").val(data[3])
            $("#id").val(data[4])
            $("#editModal").modal("show")
        }

        function showDelete(data) {
            data = JSON.parse(data)
            $("#uuu").val(data[0])
            $("#ppp").val(data[1])
            $("#vvv").val(data[3])
            $("#idd").val(data[4])
            $("#deleteModal").modal("show")
        }

        function deleteUser(button) {
            let form = $("#deleteForm")
            let url = "set.php?func=delete-user"

            let origHtml = $(button).html()
            $(button).html("Deleting...")
            $(button).attr("disabled", "true")
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize()
            }).done(function(data) {
                table.ajax.reload()
                $(button).removeClass("btn-danger")
                $(button).addClass("btn-success")
                $(button).html("Success...")
                setTimeout(function() {
                    $("#deleteModal").modal("hide")
                    $(button).html(origHtml)
                    $(button).removeClass("btn-success")
                    $(button).addClass("btn-danger")
                    $(button).removeAttr("disabled")
                }, 1000)
            }).fail(function(data) {
                $(button).removeClass("btn-danger")
                $(button).addClass("btn-warning")
                $(button).html("Failed...")
                setTimeout(function() {
                    $(button).html(origHtml)
                    $(button).removeClass("btn-warning")
                    $(button).addClass("btn-danger")
                    $(button).removeAttr("disabled")
                }, 1000)
            })
        }

        function editUser(button) {
            let form = $("#editForm")
            let url = "set.php?func=edit-user"

            let origHtml = $(button).html()
            $(button).html("Updating...")
            $(button).attr("disabled", "true")
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize()
            }).done(function(data) {
                table.ajax.reload()
                $(button).removeClass("btn-primary")
                $(button).addClass("btn-success")
                $(button).html("Success...")
                setTimeout(function() {
                    $("#editModal").modal("hide")
                    $(button).html(origHtml)
                    $(button).removeClass("btn-success")
                    $(button).addClass("btn-primary")
                    $(button).removeAttr("disabled")
                }, 1000)
            }).fail(function(data) {
                $(button).removeClass("btn-primary")
                $(button).addClass("btn-warning")
                $(button).html("Failed...")
                setTimeout(function() {
                    $(button).html(origHtml)
                    $(button).removeClass("btn-warning")
                    $(button).addClass("btn-primary")
                    $(button).removeAttr("disabled")
                }, 1000)
            })
        }
    </script>
</body>
</html>