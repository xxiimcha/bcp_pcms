<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Track Appointment</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Bootstrap -->
  <link rel="stylesheet" href="../assets/plugins/bootstrap/css/bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .tracking-card {
      background-color: #17a2b8;
      color: white;
      padding: 15px;
      border-radius: 10px;
      font-size: 16px;
    }
    .follow-up-btn {
      margin-top: 10px;
    }
  </style>
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">
      <a href="../index.html" class="navbar-brand">
        <span class="brand-text font-weight-bold">Public Consultation System</span>
      </a>
      <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a href="schedule.html" class="nav-link">Schedule an Appointment</a>
          </li>
          <li class="nav-item">
            <a href="track.html" class="nav-link">Track</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container my-5">
    <h2 class="text-center">Track Your Appointment</h2>
    <p class="text-center">Enter your reference code to check the status of your appointment.</p>
    
    <div class="card">
      <div class="card-body">
        <form id="trackForm">
          <div class="form-group">
            <label for="reference_code">Reference Code</label>
            <input type="text" class="form-control" id="reference_code" name="reference_code" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Track Appointment</button>
        </form>
        <div id="trackingResult" class="mt-4"></div>
      </div>
    </div>
  </div>
</div>

<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    $('#trackForm').submit(function(event) {
      event.preventDefault();
      let formData = $(this).serialize();
      
      $.ajax({
        url: '../controller/consultation.php?action=track',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            let followUpButton = response.status === 'Pending' ? 
              `<button class='btn btn-warning follow-up-btn' onclick='followUp("${response.reference_code}")'>Follow Up</button>` : '';
            
            let resultHtml = `<div class='tracking-card'>
              <strong>Status:</strong> ${response.status} <br>
              <strong>Scheduled Date:</strong> ${response.schedule_date} <br>
              <strong>Scheduled Time:</strong> ${response.schedule_time} <br>
              <strong>Comments:</strong> ${response.comments || 'No comments available'} <br>
              ${followUpButton}
            </div>`;
            $('#trackingResult').html(resultHtml);
          } else {
            Swal.fire({
              title: 'Error!',
              text: response.message,
              icon: 'error',
              confirmButtonText: 'OK'
            });
            $('#trackingResult').html('');
          }
        },
        error: function(xhr, status, error) {
          console.error('Error:', error);
        }
      });
    });
  });

  function followUp(referenceCode) {
    Swal.fire({
      title: 'Follow Up Request',
      text: 'Your follow-up request has been sent successfully!',
      icon: 'success',
      confirmButtonText: 'OK'
    });
  }
</script>

<!-- Bootstrap 4 -->
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/dist/js/adminlte.min.js"></script>
</body>
</html>
