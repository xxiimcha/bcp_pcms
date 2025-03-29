<?php 
include '../database/config.php'; 
include '../partials/head.php';

// Fetch all users
$query = "SELECT * FROM users";
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
            <h1>User Management</h1>
          </div>
          <div class="col-sm-6 text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">
              <i class="fas fa-user-plus"></i> Add User
            </button>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">User List</h3>
          </div>
          <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Full Name</th>
                  <th>Email</th>
                  <th>Username</th>
                  <th>Role</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($user = mysqli_fetch_assoc($result)) { ?>
                <tr>
                  <td><?php echo $user['id']; ?></td>
                  <td><?php echo $user['full_name']; ?></td>
                  <td><?php echo $user['email']; ?></td>
                  <td><?php echo $user['username']; ?></td>
                  <td><?php echo $user['role']; ?></td>
                  <td>
                    <button 
                      class="btn btn-sm btn-warning edit-btn" 
                      data-id="<?php echo $user['id']; ?>"
                      data-full_name="<?php echo $user['full_name']; ?>"
                      data-email="<?php echo $user['email']; ?>"
                      data-username="<?php echo $user['username']; ?>"
                      data-role="<?php echo $user['role']; ?>"
                      data-toggle="modal" data-target="#editUserModal">
                      <i class="fas fa-edit"></i> Edit
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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="addUserForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Role</label>
            <select name="role" class="form-control" required>
              <option value="Admin">Admin</option>
              <option value="Staff">Staff</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add User</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="editUserForm">
      <input type="hidden" name="id" id="edit-user-id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" id="edit-full_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" id="edit-email" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" id="edit-username" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Role</label>
            <select name="role" id="edit-role" class="form-control" required>
              <option value="Admin">Admin</option>
              <option value="Staff">Staff</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Update User</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php include '../partials/foot.php'; ?>

<script>
  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
      document.getElementById('edit-user-id').value = button.dataset.id;
      document.getElementById('edit-full_name').value = button.dataset.full_name;
      document.getElementById('edit-email').value = button.dataset.email;
      document.getElementById('edit-username').value = button.dataset.username;
      document.getElementById('edit-role').value = button.dataset.role;
    });
  });

  // ADD USER
  document.getElementById('addUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('../controller/user.php?action=add', {
      method: 'POST',
      body: formData
    })
    .then(res => res.text())
    .then(response => {
      Swal.fire({
        title: 'Add User',
        text: response,
        icon: response.includes("successfully") ? 'success' : 'error'
      }).then(() => location.reload());
    });
  });

  // EDIT USER
  document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('../controller/user.php?action=edit', {
      method: 'POST',
      body: formData
    })
    .then(res => res.text())
    .then(response => {
      Swal.fire({
        title: 'Update User',
        text: response,
        icon: response.includes("successfully") ? 'success' : 'error'
      }).then(() => location.reload());
    });
  });
</script>

