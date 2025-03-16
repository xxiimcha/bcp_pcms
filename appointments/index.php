<?php 
include '../database/config.php'; 
include '../partials/head.php';
?>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <?php include '../partials/navbar.php'; ?>
  <?php include '../partials/header.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Manage Appointments</h1>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Appointments List</h3>
          </div>
          <div class="card-body">
            <table id="appointmentsTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Reference Code</th>
                  <th>Full Name</th>
                  <th>Consultation Type</th>
                  <th>Schedule Date</th>
                  <th>Schedule Time</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                    <?php
                    $query = "SELECT * FROM consultations ORDER BY schedule_date DESC";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>{$row['reference_code']}</td>
                                <td>{$row['full_name']}</td>
                                <td>{$row['consultation_type']}</td>
                                <td>{$row['schedule_date']}</td>
                                <td>{$row['schedule_time']}</td>
                                <td><span class='badge badge-" . ($row['status'] == 'Pending' ? 'warning' : ($row['status'] == 'Completed' ? 'success' : 'danger')) . "'>{$row['status']}</span></td>
                                <td>
                                    <button class='btn btn-info btn-sm' onclick='viewDetails(\"{$row['reference_code']}\")' data-toggle='modal' data-target='#viewModal'>View</button>
                                </td>
                            </tr>";
                    }
                    ?>
                </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </div>
  <?php include '../partials/footer.php'; ?>
</div>
<?php include '../partials/foot.php'; ?>

<!-- Modal for Viewing Details -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewModalLabel">Appointment Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><strong>Full Name:</strong> <span id="modalFullName"></span></p>
        <p><strong>Email:</strong> <span id="modalEmail"></span></p>
        <p><strong>Phone:</strong> <span id="modalPhone"></span></p>
        <p><strong>Consultation Type:</strong> <span id="modalConsultationType"></span></p>
        <p><strong>Schedule Date:</strong> <span id="modalScheduleDate"></span></p>
        <p><strong>Schedule Time:</strong> <span id="modalScheduleTime"></span></p>
        <p><strong>Status:</strong> <span id="modalStatus"></span></p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" onclick="approveAppointment()">Approve</button>
        <button class="btn btn-danger" onclick="updateStatus('Rejected')">Reject</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
let currentReferenceCode = "";

function viewDetails(referenceCode) {
    currentReferenceCode = referenceCode;
    $.ajax({
        url: '../controller/consultation.php?action=view_details',
        type: 'POST',
        data: { reference_code: referenceCode },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $("#modalFullName").text(response.data.full_name);
                $("#modalEmail").text(response.data.email);
                $("#modalPhone").text(response.data.phone);
                $("#modalConsultationType").text(response.data.consultation_type);
                $("#modalScheduleDate").text(response.data.schedule_date);
                $("#modalScheduleTime").text(response.data.schedule_time);
                $("#modalStatus").text(response.data.status);
                $("#viewModal").modal("show");
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }
    });
}

function approveAppointment() {
    Swal.fire({
        title: 'Approve Appointment',
        text: 'Would you like to set the status to Ongoing or Completed?',
        icon: 'question',
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonText: 'Ongoing',
        denyButtonText: 'Completed'
    }).then((result) => {
        if (result.isConfirmed) {
            updateStatus("Ongoing");
        } else if (result.isDenied) {
            Swal.fire({
                title: 'Provide Remarks',
                input: 'text',
                inputLabel: 'How did the consultation go?',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                preConfirm: (remarks) => {
                    if (!remarks) {
                        Swal.showValidationMessage('Remarks are required for completion');
                    }
                    return remarks;
                }
            }).then((remarksResult) => {
                if (remarksResult.isConfirmed) {
                    updateStatus("Completed", remarksResult.value);
                }
            });
        }
    });
}

function updateStatus(status, remarks = '') {
    $.ajax({
        url: '../controller/consultation.php?action=update_status',
        type: 'POST',
        data: { reference_code: currentReferenceCode, status: status, remarks: remarks },
        success: function(response) {
            Swal.fire('Updated!', 'The appointment status has been updated.', 'success').then(() => {
                location.reload();
            });
        }
    });
}

$(function () {
    $('#appointmentsTable').DataTable({
        "responsive": true,
        "autoWidth": false
    });
});
</script>
</body>
</html>
