<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Public Consultation System</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Bootstrap -->
  <link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <style>
    .hero-section {
      background: url('dist/img/consultation-bg.jpg') no-repeat center center/cover;
      height: 60vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      text-align: center;
      padding: 20px;
    }
    .hero-section h1 {
      font-size: 3rem;
      font-weight: bold;
    }
    .hero-section p {
      font-size: 1.2rem;
    }
  </style>
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">
      <a href="#" class="navbar-brand">
        <span class="brand-text font-weight-bold">Public Consultation System</span>
      </a>
      <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a href="#appointment" class="nav-link">Schedule an Appointment</a>
          </li>
          <li class="nav-item">
            <a href="#track" class="nav-link">Track</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="hero-section">
    <div>
      <h1>Welcome to the Public Consultation System</h1>
      <p>Book your consultation appointment and track its status with ease.</p>
      <a href="public/schedule.php" class="btn btn-primary btn-lg">Schedule Now</a>
      <a href="#track" class="btn btn-outline-light btn-lg">Track Appointment</a>
    </div>
  </div>

  <div class="container my-5">
    <section id="appointment" class="text-center py-5">
      <h2>Schedule an Appointment</h2>
      <p>Choose a date and time for your consultation.</p>
      <a href="#" class="btn btn-success">Book Now</a>
    </section>

    <section id="track" class="text-center py-5">
      <h2>Track Your Appointment</h2>
      <p>Check the status of your consultation request.</p>
      <a href="#" class="btn btn-info">Track Now</a>
    </section>
  </div>
</div>

<!-- jQuery -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/dist/js/adminlte.min.js"></script>
</body>
</html>
