<?php include '../partials/head.php'; ?>
<body class="hold-transition layout-top-nav">
<?php include 'partials/nav.php'; ?>
  <div class="container my-5">
    <h2 class="text-center">Online Consultation Booking</h2>
    <p class="text-center">Fill in the details to book your consultation.</p>
    
    <div class="card">
      <div class="card-body">
        <form id="appointmentForm">
          <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="form-group">
            <label for="birthdate">Birthdate</label>
            <input type="date" class="form-control" id="birthdate" name="birthdate" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" class="form-control" id="phone" name="phone" required>
          </div>
          <div class="form-group">
            <label for="consultationType">Consultation Type</label>
            <select class="form-control" id="consultationType" name="consultationType" required>
              <option value="">Select type</option>
              <option value="Public Safety and Security">Public Safety and Security</option>
              <option value="Waste Management">Waste Management</option>
              <option value="Health Services">Health Services</option>
              <option value="Infrastructure and Development">Infrastructure and Development</option>
              <option value="Poverty and Unemployment">Poverty and Unemployment</option>
              <option value="Education">Education</option>
              <option value="Social Services">Social Services</option>
              <option value="Natural Disasters and Climate Change">Natural Disasters and Climate Change</option>
              <option value="Others">Others</option>
            </select>
          </div>
          <div class="form-group" id="secondaryConcernContainer">
            <label for="secondaryConcern">Secondary Concern</label>
            <input type="text" class="form-control" id="secondaryConcern" name="secondaryConcern">
          </div>
          
          <!-- Date & Time Row -->
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="schedule_date">Preferred Date</label>
                <input type="date" class="form-control" id="schedule_date" name="schedule_date" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="schedule_time">Preferred Time</label>
                <select class="form-control" id="schedule_time" name="schedule_time" required>
                  <option value="">Select time</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="message">Comments / Justification</label>
            <textarea class="form-control" id="message" name="message" rows="3"></textarea>
          </div>

          <!-- Terms & Conditions -->
          <div class="form-group">
            <input type="checkbox" id="termsCheckbox" disabled>
            <label for="termsCheckbox">
              I agree to the <a href="#" id="openTermsModal">Terms and Conditions</a>
            </label>
          </div>

          <button type="submit" class="btn btn-primary btn-block">Book Online Session</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Terms & Conditions Modal -->
  <div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>
            By booking an appointment, you agree to our policies. You must provide accurate information and comply with consultation schedules.
          </p>
          <p>
            Cancellations should be made at least 24 hours in advance. Failure to attend multiple appointments may result in restrictions.
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="agreeTerms">I Agree</button>
        </div>
      </div>
    </div>
  </div>

<!-- jQuery & Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>

<script>
  $(document).ready(function() {
    let today = new Date().toISOString().split('T')[0];
    $('#schedule_date').attr('min', today);

    // Ensure modal opens
    $("#openTermsModal").on("click", function (e) {
      e.preventDefault();
      $("#termsModal").modal("show");
    });

    // Enable checkbox after agreeing
    $("#agreeTerms").on("click", function () {
      $("#termsCheckbox").prop("disabled", false);
      $("#termsCheckbox").prop("checked", true);
      $("#termsModal").modal("hide");
    });

    function generateTimeSlots() {
      let startHour = 8;
      let endHour = 17; // 5 PM
      let interval = 30;
      let timeDropdown = $('#schedule_time');
      timeDropdown.empty().append('<option value="">Select time</option>');

      for (let hour = startHour; hour < endHour; hour++) {
        for (let min = 0; min < 60; min += interval) {
          let displayHour = hour > 12 ? hour - 12 : hour;
          let period = hour >= 12 ? 'PM' : 'AM';
          let formattedMin = min === 0 ? '00' : min;

          let timeText = `${displayHour}:${formattedMin} ${period}`;
          timeDropdown.append(`<option value="${hour}:${formattedMin}">${timeText}</option>`);
        }
      }
    }

    generateTimeSlots();

    // **Form Submission via AJAX**
    $('#appointmentForm').submit(function(event) {
      event.preventDefault();
      if (!$("#termsCheckbox").prop("checked")) {
        Swal.fire("Please agree to the Terms and Conditions before proceeding.");
        return;
      }

      let formData = $(this).serialize();

      $.ajax({
        url: '../controller/consultation.php?action=schedule',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            Swal.fire({
              title: 'Appointment Booked!',
              text: `Your reference code is: ${response.reference_code}`,
              icon: 'success',
              confirmButtonText: 'OK'
            });
            $('#appointmentForm')[0].reset();
            generateTimeSlots();
          } else {
            Swal.fire('Error!', response.message, 'error');
          }
        },
        error: function(xhr, status, error) {
          console.error('Error:', error);
        }
      });
    });
  });
</script>
</body>
</html>
