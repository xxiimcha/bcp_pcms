<?php 
include '../database/config.php'; 
include '../partials/head.php';

// Fetch counts from database
$query = "SELECT 
  (SELECT COUNT(*) FROM consultations WHERE status = 'New') AS new_appointments,
  (SELECT COUNT(*) FROM consultations WHERE status = 'Completed') AS completed,
  (SELECT COUNT(*) FROM consultations WHERE status = 'Pending') AS pending,
  (SELECT COUNT(*) FROM consultations WHERE status = 'Cancelled') AS cancelled";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
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
            <h1>Dashboard</h1>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?php echo $data['new_appointments']; ?></h3>
                <p>New Appointments</p>
              </div>
              <div class="icon">
                <i class="fas fa-calendar-check"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?php echo $data['completed']; ?></h3>
                <p>Completed Consultations</p>
              </div>
              <div class="icon">
                <i class="fas fa-check-circle"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?php echo $data['pending']; ?></h3>
                <p>Pending Requests</p>
              </div>
              <div class="icon">
                <i class="fas fa-hourglass-half"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?php echo $data['cancelled']; ?></h3>
                <p>Cancelled Requests</p>
              </div>
              <div class="icon">
                <i class="fas fa-times-circle"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <?php include '../partials/footer.php'; ?>
</div>
<?php include '../partials/foot.php'; ?>
