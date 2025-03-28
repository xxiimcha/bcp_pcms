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
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Appointment Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <p><strong>Full Name:</strong> <span id="modalFullName"></span></p>
            <p><strong>Email:</strong> <span id="modalEmail"></span></p>
            <p><strong>Phone:</strong> <span id="modalPhone"></span></p>
          </div>
          <div class="col-md-6">
            <p><strong>Consultation Type:</strong> <span id="modalConsultationType"></span></p>
            <p><strong>Schedule Date:</strong> <span id="modalScheduleDate"></span></p>
            <p><strong>Schedule Time:</strong> <span id="modalScheduleTime"></span></p>
          </div>
        </div>
        <p><strong>Status:</strong> <span id="modalStatus"></span></p>
        <p><strong>Remarks:</strong> <span id="modalRemarks">None</span></p>

        <hr>
        <h6>Consultation History</h6>
        <table class="table table-sm table-bordered">
          <thead>
            <tr>
              <th>Date</th>
              <th>Status</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody id="historyTableBody">
            <!-- History records will be inserted here -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer" id="statusActionButtons">
        <!-- Dynamic Buttons -->
      </div>
    </div>
  </div>
</div>

<script>
function getStatusColor(status) {
  switch (status.toLowerCase()) {
    case 'pending': return 'warning';
    case 'approved': return 'primary';
    case 'ongoing': return 'info';
    case 'completed': return 'success';
    case 'rejected': return 'danger';
    default: return 'secondary';
  }
}

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

        // Load consultation history
        $.ajax({
          url: '../controller/consultation.php?action=view_history',
          type: 'POST',
          data: { reference_code: referenceCode },
          dataType: 'json',
          success: function(historyRes) {
            if (historyRes.success) {
              let historyRows = '';
              historyRes.data.forEach(item => {
                historyRows += `
                  <tr>
                    <td>${item.updated_at}</td>
                    <td><span class="badge badge-${getStatusColor(item.status)}">${item.status}</span></td>
                    <td>${item.remarks}</td>
                  </tr>`;
              });
              $('#historyTableBody').html(historyRows);
            } else {
              $('#historyTableBody').html('<tr><td colspan="3">No history found.</td></tr>');
            }
          }
        });

        $("#viewModal").modal("show");
      } else {
        Swal.fire('Error', response.message, 'error');
      }
    }
  });
}
</script>
</body>
</html>
