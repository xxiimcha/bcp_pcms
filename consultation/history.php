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
            <h1>Consultation History</h1>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Consultation History Log</h3>
          </div>
          <div class="card-body">
            <table id="historyTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Consultation ID</th>
                  <th>Status</th>
                  <th>Remarks</th>
                  <th>Updated At</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $query = "SELECT * FROM consultation_history ORDER BY updated_at DESC";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$row['consultation_id']}</td>
                            <td><span class='badge badge-" . ($row['status'] == 'Pending' ? 'warning' : ($row['status'] == 'Completed' ? 'success' : 'danger')) . "'>{$row['status']}</span></td>
                            <td>{$row['remarks']}</td>
                            <td>{$row['updated_at']}</td>
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

<script>
$(function () {
  $('#historyTable').DataTable();
});
</script>
</body>
</html>
