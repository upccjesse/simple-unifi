<?php

if (!isset($_SESSION)) {
	session_start();
}

if (!isset($_SESSION["id"])) {
	header("Location: login.php");
	die();
}
elseif (!$_SESSION["permissions"]["ap_group"]) {
    "Unauthorized to see this page.";
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
<div class="container" style="background-color: white">
	<header class="d-flex justify-content-center py-3">
		<ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
			<li class="nav-item">
				<a class="nav-link" href="radius.php">RADIUS</a>
			</li>
			<li class="nav-item">
				<a class="nav-link active" href="apgroup.php">AP Group</a>
			</li>
		</ul>
	</header>
</div>
<div class="container" style="background-color: white;">
        <div class="row justify-content-md-center">
            <div class="col-11 border rounded p-3">
                <p>
                    Wireless uplink is <span id="uplink" class="placeholder-glow"><span class="placeholder col-1"></span></span>.
                    Each AP can broadcast at most <span id="ssid-count" class="placeholder-glow"><span class="placeholder col-1"></span></span> SSIDs.
                </p>
                <p>
                    This tool will not prevent you from making a mistake. It is imperative that you adhere to the maximum
                    number of SSIDs per AP while using this tool.
                </p>
                <p>
                    Choose an AP Group by clicking on its respective row in the table. The lists of SSIDs and APs will
                    update to indicate membership in the selected AP Group.
                    Use the respective checkboxes for each SSID or AP to add or remove from the currently selected AP Group.
                </p>
            </div>
        </div>
		<div class="row justify-content-md-center">
			<div class="col-md-5 col-sm border rounded p-3">
                <h4>AP Groups</h4>
				<table class="display" id="ap-group-list" style="width: 100%;">
					<thead>
					<tr>
						<th>AP Group</th>
						<th># APs</th>
						<th># SSIDs</th>
						<th>Action</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					</tbody>
				</table>
			</div>
			<div class="col-md-6">
				<div class="row">
					<div class="col col-sm border rounded p-3">
                        <h4>SSIDs</h4>
						<table class="display" id="ssid-list" style="width: 100%;">
							<thead>
							<tr>
								<th><input type="checkbox" /></th>
								<th>SSID</th>
								<th># AP Group</th>
								<th>Action</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							</tbody>
						</table>
                        <button class="btn btn-primary" id="ssid-button" onclick="updateSSIDsInGroup(this)"><i class="bi bi-save"></i> Save SSIDs</button>
                        <div id="ssid-error-box"></div>
					</div>
				</div>
				<div class="row">
					<div class="col col-sm border rounded p-3">
                        <h4>Access Points</h4>
						<table class="display" id="ap-list" style="width: 100%;">
							<thead>
							<tr>
								<th><input type="checkbox" /></th>
								<th>AP</th>
								<th># SSID</th>
								<th>MAC</th>
								<th>ID</th>
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
						<button class="btn btn-primary" onclick="updateAPsInGroup(this)"><i class="bi bi-save"></i> Save APs</button>
					</div>
				</div>
			</div>
		</div>
</div>

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Rename AP Group</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="id" name="id" />
                    <div class="mb-3">
                        <label for="u" class="form-label">AP Group Name</label>
                        <input required type="text" class="form-control" id="n" name="n" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="renameAPGroup(this)">Rename</button>
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
<script src="common.js"></script>

<script>
	let apGroupTable, apTable, ssidTable, apGroupId, selectedRow, apGroupName, apCheckedStatus

	$(() => {
	    apGroupTable = $("#ap-group-list").DataTable({
			ajax: "get.php?func=ap-group-list",
            columns: [
                { data: 0 },
                { data: 1 },
                { data: 2 },
                {
                    data: 3,
                    render: function(data, type, row) {
                        if (row[0] !== "All APs") {
                            let edit = createTableEditButton(row)
                            return "<div class='container gx-4'>" + edit + "</div>"
                        }
                        return ""
                    }
                }
            ]
		})

		apTable = $("#ap-list").DataTable({
			ajax: {
			    url: "get.php?func=ap-group",
                data: function(d) {
                    d.id = apGroupId
					d.aps = apCheckedStatus
                }
            },
            columns: [
                {
                    data: 0,
                    render: function(data, type, row) {
                        let checked = data === 1 ? "checked" : ""
						let disabled = apGroupId ? "" : "disabled"
                        return "<input type=checkbox id='" + row[3] + "' " + checked + " " + disabled + " />"
                    }
                },
                { data: 1 },
                { data: 2 },
                { data: 3 },
                { data: 4 }
            ],
            columnDefs: [
                {
                    targets: 3,
                    visible: false
                },
                {
                    targets: 4,
                    visible: false
                }
            ]
		})

		ssidTable = $("#ssid-list").DataTable({
			ajax: {
			    url: "get.php?func=ssid"
			},
			columns: [
				{
				    data: 0,
					render: function(data, type, row) {
				        let json = JSON.parse(data)
				        let checked = (apGroupId && json.includes(apGroupId)) ? "checked" : ""
                        let disabled = apGroupId ? "" : "disabled"
						return "<input type=checkbox id=" + row[3] + " " + checked + " " + disabled + " />"
					}
				},
				{ data: 1 },
				{ data: 2 },
				{ data: 3 }
			],
			columnDefs: [
				{
				    targets: [2, 3],
					visible: false
				}
			]
		})

        updateWirelessUplink()
	})

	$("#ap-group-list tbody").on("click", "tr", function(e) {
	    if (e.target.nodeName === "BUTTON" || e.target.nodeName === "I")
	        return
	    let data = apGroupTable.row(this).data()
		$("#ap-group-list tbody tr").removeClass("selected")
		$(this).addClass("selected")
        selectedRow = this
		apGroupId = data[3]
		apGroupName = data[0]
		apTable.ajax.reload()
        ssidTable.ajax.reload()
	})

    function showEdit(data) {
        data = JSON.parse(data)
        $("#n").val(data[0])
        $("#id").val(data[3])
        $("#editModal").modal("show")
    }

    function renameAPGroup(button) {
        let form = $("#editForm")
        let url = "set.php?func=rename-ap-group"
        setButtonPending(button, "Renaming...")
        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize()
        }).done(function() {
            setButtonSuccess(button)
            closeModalTimeout("#editModal")
        }).fail(function() {
            setButtonFail(button)
        })
    }

	function updateAPsInGroup(button) {
	    setButtonPending(button, "Saving...")
		apCheckedStatus = []
		$("#ap-list tbody input[type='checkbox']").each(function () {
            apCheckedStatus.push({
				"id": $(this).attr("id"),
				"checked": $(this).is(":checked") ? 1 : 0
		    })
		})
		$.ajax({
			url: "set.php?func=update-aps-in-group",
			data: {
			    id: apGroupId,
				name: apGroupName,
				aps: apCheckedStatus
			}
		}).done(function() {
            apTable.ajax.reload()
            reloadAPGroupList()
            setButtonSuccess(button)
		}).fail(function() {
		    setButtonFail(button)
        })
	}

	function updateSSIDsInGroup(button) {
        setButtonPending(button, "Saving...")
        let ssidCheckedStatus = getCheckedItemsInTable("#ssid-list tbody input[type='checkbox']")
        $.ajax({
            url: "set.php?func=update-ssids-in-group",
            data: {
                id: apGroupId,
                name: apGroupName,
                ssids: ssidCheckedStatus
            }
        }).done(function(data) {
            setButtonSuccess(button)
        }).fail(function(data) {
            let json = JSON.parse(data.responseText)
            $("#ssid-error-box").html("<div class='alert alert-warning'>" + json.error + "</div>")
            setButtonFail(button)
            setTimeout(function() {
                $("#ssid-error-box").html("")
            }, 5000)
        }).always(function() {
            apTable.ajax.reload()
            reloadAPGroupList()
            ssidTable.ajax.reload()
        })
    }

	function updateWirelessUplink() {
	    $.ajax({
            url: "get.php?func=wireless-uplink"
        }).done(function(data) {
            let json = JSON.parse(data)
            let status = json.enabled ? "enabled" : "disabled"
            let count = json.enabled ? "4" : "8"
            $("#uplink").text(status)
            $("#ssid-count").text(count)
        })
    }

    function reloadAPGroupList() {
	    apGroupTable.ajax.reload(function() {
	        if (selectedRow) {
	            //TODO: Add selection back, probably can do it this way instead https://datatables.net/extensions/select/examples/initialisation/reload.html
            }
        })
    }
</script>
</body>
