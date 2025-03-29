<?php
include '../database/config.php';
include '../partials/head.php';

$query = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
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
            <h1>Announcements</h1>
          </div>
          <div class="col-sm-6 text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addModal">
              <i class="fas fa-plus"></i> Add Announcement
            </button>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-body table-responsive p-0">
            <table class="table table-hover" id="announcementTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Title</th>
                  <th>Content</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                  <td><?= $row['id']; ?></td>
                  <td><?= htmlspecialchars($row['title']); ?></td>
                  <td style="white-space: normal; max-width: 400px;">
                    <?= htmlspecialchars($row['content']); ?>
                  </td>
                  <td><?= date('M d, Y H:i A', strtotime($row['created_at'])); ?></td>
                  <td>
                    <span class="badge badge-<?= $row['status'] === 'Published' ? 'success' : 'secondary'; ?>">
                      <?= $row['status']; ?>
                    </span>
                  </td>
                  <td>
                    <button 
                      class="btn btn-sm btn-warning edit-btn"
                      data-id="<?= $row['id']; ?>"
                      data-title="<?= htmlspecialchars($row['title'], ENT_QUOTES); ?>"
                      data-content="<?= htmlspecialchars($row['content'], ENT_QUOTES); ?>"
                      data-toggle="modal" data-target="#editModal">
                      <i class="fas fa-edit"></i> Edit
                    </button>

                    <button 
                      class="btn btn-sm btn-info toggle-status"
                      data-id="<?= $row['id']; ?>"
                      data-status="<?= $row['status']; ?>">
                      <?= $row['status'] === 'Published' ? 'Unpublish' : 'Publish'; ?>
                    </button>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </div>

  <?php include '../partials/footer.php'; ?>
</div>

<!-- Add Announcement Modal -->
<div class="modal fade" id="addModal">
  <div class="modal-dialog">
    <form id="addAnnouncementForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Announcement</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Content</label>
            <textarea name="content" class="form-control" rows="4" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Edit Announcement Modal -->
<div class="modal fade" id="editModal">
  <div class="modal-dialog">
    <form id="editAnnouncementForm">
      <input type="hidden" name="id" id="edit-id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Announcement</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" id="edit-title" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Content</label>
            <textarea name="content" id="edit-content" class="form-control" rows="4" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php include '../partials/foot.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // DataTable
  $(document).ready(function () {
    $('#announcementTable').DataTable({
      responsive: true,
      autoWidth: false,
      pageLength: 5
    });
  });

  // Populate Edit Modal
  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
      document.getElementById('edit-id').value = button.dataset.id;
      document.getElementById('edit-title').value = button.dataset.title;
      document.getElementById('edit-content').value = button.dataset.content;
    });
  });

  // Add Announcement
  document.getElementById('addAnnouncementForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('../controller/announcement.php?action=add', {
      method: 'POST',
      body: formData
    })
    .then(res => res.text())
    .then(response => {
      Swal.fire('Add Announcement', response, response.includes('successfully') ? 'success' : 'error')
        .then(() => location.reload());
    });
  });

  // Edit Announcement
  document.getElementById('editAnnouncementForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('../controller/announcement.php?action=edit', {
      method: 'POST',
      body: formData
    })
    .then(res => res.text())
    .then(response => {
      Swal.fire('Edit Announcement', response, response.includes('successfully') ? 'success' : 'error')
        .then(() => location.reload());
    });
  });

  // Toggle Publish/Unpublish
  document.querySelectorAll('.toggle-status').forEach(button => {
    button.addEventListener('click', () => {
      const id = button.dataset.id;
      const newStatus = button.dataset.status === 'Published' ? 'Unpublished' : 'Published';

      fetch('../controller/announcement.php?action=toggle_status', {
        method: 'POST',
        body: new URLSearchParams({ id, status: newStatus })
      })
      .then(res => res.text())
      .then(response => {
        Swal.fire('Status Update', response, response.includes('successfully') ? 'success' : 'error')
          .then(() => location.reload());
      });
    });
  });
</script>
