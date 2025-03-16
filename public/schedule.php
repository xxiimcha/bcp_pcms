<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Schedule an Appointment</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Bootstrap -->
  <link rel="stylesheet" href="../assets/plugins/bootstrap/css/bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
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
          <div class="form-group">
            <label for="message">Comments / Justification</label>
            <textarea class="form-control" id="message" name="message" rows="3"></textarea>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Book Online Session</button>
        </form>
        <p class="text-center mt-3" id="referenceCode"></p>
      </div>
    </div>
  </div>
</div>

<script>
  document.getElementById("consultationType").addEventListener("change", function() {
    let secondaryConcern = document.getElementById("secondaryConcernContainer");
    if (this.value === "Others") {
      secondaryConcern.style.display = "none";
    } else {
      secondaryConcern.style.display = "block";
    }
  });

  document.getElementById("appointmentForm").addEventListener("submit", function(event) {
    event.preventDefault();
    let formData = new FormData(this);
    let consultationType = formData.get("consultationType");
    let prefix = consultationType.substring(0, 5).toUpperCase().replace(/[^A-Z]/g, "");
    let randomNum = Math.floor(Math.random() * 999) + 1;
    let referenceCode = `CONSULT-${prefix}-A${randomNum.toString().padStart(3, '0')}`;
    document.getElementById("referenceCode").innerHTML = `Your reference code: <strong>${referenceCode}</strong>`;
  });
</script>

<!-- jQuery -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/dist/js/adminlte.min.js"></script>
</body>
</html>
