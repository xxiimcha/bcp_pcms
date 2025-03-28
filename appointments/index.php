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
                    $statusColor = match(strtolower($row['status'])) {
                        'pending' => 'warning',
                        'completed' => 'success',
                        'ongoing' => 'info',
                        'approved' => 'primary',
                        'rejected' => 'danger',
                        default => 'secondary',
                    };

                    echo "<tr>
                        <td>{$row['reference_code']}</td>
                        <td>{$row['full_name']}</td>
                        <td>{$row['consultation_type']}</td>
                        <td>{$row['schedule_date']}</td>
                        <td>{$row['schedule_time']}</td>
                        <td><span class='badge badge-{$statusColor}'>{$row['status']}</span></td>
                        <td>
                            <button class='btn btn-info btn-sm' onclick='viewDetails(\"{$row['reference_code']}\")'>View</button>
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

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Appointment Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
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
        <p><strong>Remarks:</strong> <span id="modalRemarks">None</span></p>
      </div>
      <div class="modal-footer" id="statusActionButtons">
        <!-- Dynamic Buttons -->
      </div>
    </div>
  </div>
</div>

<!-- Remarks Modal -->
<div class="modal fade" id="remarksModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="remarksForm">
        <div class="modal-header">
          <h5 class="modal-title">Complete Consultation</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="completionRemarks">Remarks / Feedback</label>
            <textarea id="completionRemarks" class="form-control" name="remarks" rows="4" placeholder="Write remarks here..." required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Submit Feedback</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </form>
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
        const data = response.data;
        $("#modalFullName").text(data.full_name);
        $("#modalEmail").text(data.email);
        $("#modalPhone").text(data.phone);
        $("#modalConsultationType").text(data.consultation_type);
        $("#modalScheduleDate").text(data.schedule_date);
        $("#modalScheduleTime").text(data.schedule_time);
        $("#modalStatus").text(data.status);
        $("#modalRemarks").text(data.comments || 'None');

        let status = data.status.toLowerCase();
        let buttons = '';

        if (status === 'pending') {
          buttons += `<button class="btn btn-primary" onclick="approveAppointment()">Approve</button>`;
          buttons += `<button class="btn btn-danger" onclick="rejectAppointment()">Reject</button>`;
        } else if (status === 'approved') {
          buttons += `<button class="btn btn-info" onclick="markAsOngoing()">Mark as Ongoing</button>`;
          buttons += `<button class="btn btn-success" onclick="openRemarksModal()">Complete Consultation</button>`;
        } else if (status === 'ongoing') {
          buttons += `<button class="btn btn-success" onclick="openRemarksModal()">Complete Consultation</button>`;
        }

        buttons += `<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>`;
        $("#statusActionButtons").html(buttons);
        $("#viewModal").modal("show");
      } else {
        Swal.fire('Error', response.message, 'error');
      }
    }
  });
}

function approveAppointment() {
  Swal.fire({
    title: 'Approve Appointment?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Approve'
  }).then((result) => {
    if (result.isConfirmed) {
      updateStatus("Approved", "Approved by admin");
    }
  });
}

function rejectAppointment() {
  Swal.fire({
    title: 'Reject Appointment',
    input: 'text',
    inputLabel: 'Optional Remarks',
    inputPlaceholder: 'Reason for rejection',
    showCancelButton: true,
    confirmButtonText: 'Reject'
  }).then((result) => {
    if (result.isConfirmed) {
      updateStatus("Rejected", result.value || 'Rejected by admin');
    }
  });
}

function markAsOngoing() {
  updateStatus("Ongoing", "Marked as Ongoing");
}

function openRemarksModal() {
  $('#completionRemarks').val('');
  $('#remarksModal').modal('show');
}

$('#remarksForm').submit(function(e) {
  e.preventDefault();
  const remarks = $('#completionRemarks').val().trim();
  if (!remarks) {
    Swal.fire('Required', 'Please enter remarks before completing.', 'warning');
    return;
  }
  updateStatus("Completed", remarks);
  $('#remarksModal').modal('hide');
});

function updateStatus(status, remarks = '') {
  $.ajax({
    url: '../controller/consultation.php?action=update_status',
    type: 'POST',
    data: { reference_code: currentReferenceCode, status: status, remarks: remarks },
    success: function(response) {
      Swal.fire('Updated!', `Appointment marked as ${status}.`, 'success').then(() => {
        location.reload();
      });
    },
    error: function() {
      Swal.fire('Error', 'Something went wrong.', 'error');
    }
  });
}

$(function () {
  $('#appointmentsTable').DataTable({
    responsive: true,
    autoWidth: false
  });
});
</script>
</body>
</html>
