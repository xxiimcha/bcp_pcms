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

// Fetch recent announcements
$announcementQuery = "SELECT title, created_at FROM announcements ORDER BY created_at DESC LIMIT 5";
$announcementResult = mysqli_query($conn, $announcementQuery);

// Fetch consultation types for chart
$typeQuery = "SELECT consultation_type, COUNT(*) AS count FROM consultations GROUP BY consultation_type";
$typeResult = mysqli_query($conn, $typeQuery);

$consultationTypes = [];
$consultationCounts = [];

while ($row = mysqli_fetch_assoc($typeResult)) {
  $consultationTypes[] = $row['consultation_type'];
  $consultationCounts[] = $row['count'];
}
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
          <!-- New Appointments -->
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

          <!-- Completed -->
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

          <!-- Pending -->
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

          <!-- Cancelled -->
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

        <!-- Charts Row -->
        <div class="row">
          <!-- Consultation Status Chart -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Consultation Status Overview</h3>
              </div>
              <div class="card-body">
                <canvas id="consultationChart" height="150"></canvas>
              </div>
            </div>
          </div>

          <!-- Consultation Type Chart -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Consultation Type Distribution</h3>
              </div>
              <div class="card-body">
                <canvas id="typeChart" height="150"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Announcements Table -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Recent Announcements</h3>
              </div>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>Title</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($row = mysqli_fetch_assoc($announcementResult)) { ?>
                      <tr>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>
  </div>

  <?php include '../partials/footer.php'; ?>
</div>
<?php include '../partials/foot.php'; ?>

<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Consultation Status Chart
  const statusCtx = document.getElementById('consultationChart').getContext('2d');
  const consultationChart = new Chart(statusCtx, {
    type: 'pie',
    data: {
      labels: ['New', 'Completed', 'Pending', 'Cancelled'],
      datasets: [{
        data: [
          <?php echo $data['new_appointments']; ?>,
          <?php echo $data['completed']; ?>,
          <?php echo $data['pending']; ?>,
          <?php echo $data['cancelled']; ?>
        ],
        backgroundColor: ['#17a2b8', '#28a745', '#ffc107', '#dc3545']
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });

  // Consultation Type Chart
  const typeCtx = document.getElementById('typeChart').getContext('2d');
  const typeChart = new Chart(typeCtx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($consultationTypes); ?>,
      datasets: [{
        label: 'Consultations',
        data: <?php echo json_encode($consultationCounts); ?>,
        backgroundColor: '#007bff'
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          precision: 0
        }
      }
    }
  });
</script>
