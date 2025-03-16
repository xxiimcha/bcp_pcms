<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Public Consultation System</title>

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- AdminLTE -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">

  <style>
    .hero-section {
      background: url('assets/dist/img/consultation-bg.jpg') no-repeat center center/cover;
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
      text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
    }
    .hero-section p {
      font-size: 1.2rem;
    }
    .announcement-carousel {
      background: #f8f9fa;
      padding: 20px;
      border-radius: 10px;
    }
  </style>
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">
  
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-white shadow-sm">
    <div class="container">
      <a href="#" class="navbar-brand">
        <span class="brand-text font-weight-bold">Public Consultation System</span>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item"><a href="public/schedule.php" class="nav-link">Schedule an Appointment</a></li>
          <li class="nav-item"><a href="public/track.php" class="nav-link">Track</a></li>
          <li class="nav-item"><a href="#announcements" class="nav-link">Announcements</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <div class="hero-section">
    <div>
      <h1>Welcome to the Public Consultation System</h1>
      <p>Book your consultation appointment and track its status with ease.</p>
      <a href="public/schedule.php" class="btn btn-primary btn-lg"><i class="fas fa-calendar-alt"></i> Schedule Now</a>
      <a href="public/track.php" class="btn btn-outline-light btn-lg"><i class="fas fa-search"></i> Track Appointment</a>
    </div>
  </div>

  <div class="container my-5">
    
    <!-- Announcements Section with Carousel -->
    <section id="announcements" class="mb-5 text-center">
      <h2 class="mb-4">Latest Announcements</h2>
      <div id="announcementCarousel" class="carousel slide announcement-carousel" data-bs-ride="carousel">
        <div class="carousel-inner" id="announcement-list">
          <!-- Announcements will be loaded here dynamically -->
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#announcementCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#announcementCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
      </div>
    </section>

  </div>

  <!-- Footer -->
  <footer class="bg-dark text-white text-center py-3">
    <p>Public Consultation System &copy; 2025 | All Rights Reserved</p>
  </footer>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>

<script>
$(document).ready(function() {
    loadAnnouncements();
});

function loadAnnouncements() {
    $.ajax({
        url: 'controller/get_announcements.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.announcements.length > 0) {
                let announcementsHtml = '';
                response.announcements.forEach((announcement, index) => {
                    announcementsHtml += `
                        <div class="carousel-item ${index === 0 ? 'active' : ''}">
                            <div class="text-center">
                                <h5><i class="fas fa-bullhorn"></i> ${announcement.title}</h5>
                                <p>${announcement.content}</p>
                                <small class="text-muted">${announcement.date}</small>
                            </div>
                        </div>
                    `;
                });
                $('#announcement-list').html(announcementsHtml);
            } else {
                $('#announcement-list').html('<div class="carousel-item active"><p class="text-center text-muted">No announcements available.</p></div>');
            }
        }
    });
}
</script>

</body>
</html>
