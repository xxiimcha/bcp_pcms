<?php include '../partials/head.php'; ?>
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
<body class="hold-transition layout-top-nav">
<?php include 'partials/nav.php'; ?>

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
