<style>
.navbar-nav .nav-link {
  transition: color 0.3s ease, background 0.3s ease;
  padding: 0.5rem 0.75rem;
}

.navbar-nav .nav-link:hover {
  color: #355E3B !important;
  background-color: rgba(53, 94, 59, 0.1);
  border-radius: 5px;
}

.notif-badge { 
  position: absolute; 
  top: -5px; 
  right: -5px; 
  font-size: 0.7rem; 
  background-color: #FF4C4C;
  border-radius: 50%;
  padding: 0.25em 0.4em;
}

.navbar-brand {
  font-size: 1.4rem;
}

.navbar {
  background-color: #FEEDB9; 
  font-family: 'Segoe UI', Arial, sans-serif; 
  padding: 0.5rem 1rem;
  min-height: 56px;
}
</style>

<nav class="navbar navbar-expand-lg navbar-light">
  <a class="navbar-brand font-weight-bold text-dark" href="index.php">Writer Panel</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link text-dark" href="articles_submitted.php">Articles Submitted</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-dark" href="shared_articles.php">Shared Articles</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-dark" href="edit_request.php">Edit Requests</a>
      </li>
    </ul>

    <ul class="navbar-nav align-items-center">
      <li class="nav-item position-relative d-flex align-items-center">
        <a class="nav-link text-dark d-flex align-items-center" href="#" data-toggle="modal" data-target="#notificationsModal" style="position: relative;">
          <i class="bi bi-bell" style="font-size: 1.5rem;"></i>
          <?php if ($unreadCount > 0): ?>
            <span class="badge badge-danger notif-badge"><?php echo $unreadCount; ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-dark font-weight-bold" href="core/handleForms.php?logoutUserBtn=1">Logout</a>
      </li>
    </ul>
  </div>
</nav>