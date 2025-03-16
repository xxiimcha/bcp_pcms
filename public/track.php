<?php include '../partials/head.php'; ?>
<style>
  .tracking-card {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #ddd;
  }
  .tracking-card label {
    font-weight: bold;
  }
  .form-control-plaintext {
    background-color: #fff;
    border: 1px solid #ced4da;
    border-radius: 5px;
    padding: 10px;
  }
  .follow-up-btn, .add-concern-btn {
    margin-top: 15px;
    width: 100%;
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

        <!-- Tracking Information Section -->
        <div id="trackingResult" class="mt-4"></div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            let additionalConcerns = response.status === 'Pending' ? `
              <div class="form-group">
                <label>Additional Concerns</label>
                <textarea id="additionalConcerns" class="form-control" rows="3" placeholder="Enter additional concerns..."></textarea>
                <button class='btn btn-success add-concern-btn' onclick='submitAdditionalConcerns("${response.reference_code}")'>Submit Concern</button>
              </div>` : '';

            let followUpButton = response.status === 'Pending' ? 
              `<button class='btn btn-warning follow-up-btn' onclick='followUp("${response.reference_code}")'>Request Follow-Up</button>` : '';

            let resultHtml = `
              <div class="tracking-card">
                <h5 class="text-center">Appointment Details</h5>
                <form>
                  <div class="form-group">
                    <label>Reference Code</label>
                    <input type="text" class="form-control-plaintext" value="${response.reference_code}" readonly>
                  </div>
                  <div class="form-group">
                    <label>Status</label>
                    <input type="text" class="form-control-plaintext" value="${response.status}" readonly>
                  </div>
                  <div class="form-group">
                    <label>Scheduled Date</label>
                    <input type="text" class="form-control-plaintext" value="${response.schedule_date}" readonly>
                  </div>
                  <div class="form-group">
                    <label>Scheduled Time</label>
                    <input type="text" class="form-control-plaintext" value="${response.schedule_time}" readonly>
                  </div>
                  <div class="form-group">
                    <label>Comments</label>
                    <textarea class="form-control-plaintext" rows="2" readonly>${response.comments || 'No comments available'}</textarea>
                  </div>
                  ${additionalConcerns}
                  ${followUpButton}
                </form>
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
      title: 'Follow-Up Request Sent',
      text: 'Your follow-up request has been submitted successfully!',
      icon: 'success',
      confirmButtonText: 'OK'
    });
  }

  function submitAdditionalConcerns(referenceCode) {
    let additionalConcernText = $("#additionalConcerns").val().trim();
    
    if (!additionalConcernText) {
      Swal.fire("Error", "Please enter additional concerns before submitting.", "error");
      return;
    }

    $.ajax({
      url: '../controller/consultation.php?action=add_concern',
      type: 'POST',
      data: { reference_code: referenceCode, additional_concern: additionalConcernText },
      success: function(response) {
        Swal.fire({
          title: 'Submitted!',
          text: 'Your additional concerns have been added successfully.',
          icon: 'success',
          confirmButtonText: 'OK'
        });
        $("#additionalConcerns").val("");
      },
      error: function() {
        Swal.fire("Error", "There was an issue submitting your concerns. Please try again.", "error");
      }
    });
  }
</script>

<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
</body>
</html>
