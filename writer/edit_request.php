<?php
require_once 'classloader.php';

if (!$userObj->isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if ($userObj->isAdmin()) {
    header("Location: ../admin/index.php");
    exit;
}

$flashMessage = $_SESSION['message'] ?? null;
$flashStatus  = $_SESSION['status'] ?? null;
unset($_SESSION['message'], $_SESSION['status']);

$incomingRequests = [];
if (isset($editRequestObj)) {
    $incomingRequests = $editRequestObj->getRequestsForAuthor($_SESSION['user_id']);
}

$sentRequests = [];
if (isset($editRequestObj)) {
    $sentRequests = $editRequestObj->getRequestsByRequester($_SESSION['user_id']);
}

$notifications = $notificationObj->getNotificationsByUser($_SESSION['user_id']);

$unreadCount = 0;
foreach ($notifications as $notif) {
    if (!$notif['is_read']) $unreadCount++;
}

$notificationObj->markAllAsRead($_SESSION['user_id']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Edit Requests</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="icon" type="image/svg+xml" href="../images/logo.svg">
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <style>
  body {
      font-family: "Segoe UI", Arial, sans-serif;
      background-color: #fff;
  }

  .section-title {
      text-align: center;
      font-weight: 600;
      color: #355E3B;
      margin-bottom: 20px;
  }

  .card {
      background-color: #fffaeb;
      border-radius: 5px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      transition: transform 0.2s ease;
      padding: 15px;
      margin-bottom: 30px;
  }

  .card-body p {
      margin-bottom: 12px; 
  }

  .card-title {
      font-weight: 600;
      font-size: 1.25rem; 
      margin-bottom: 10px;
  }

  .status-pill {
      font-size: 0.9rem;
      padding: 0.35rem 0.7rem; 
      border-radius: 10px;
  }

  .btn-sm {
      color: #FFF;
      border: none;
      border-radius: 8px;
      font-weight: 500;
      padding: 6px 12px; 
      font-size: 0.9rem;
      margin-right: 5px;
  }

  textarea.form-control {
      resize: vertical;
  }

  p {
      margin-bottom: 10px;
  }

  .alert {
      border-radius: 10px;
  }

  .container {
      margin-top: 30px;
      margin-bottom: 30px;
  }

  h1 {
      color: #355E3B;
      font-weight: 600;
      margin-bottom: 23px;
      text-align: center; 
  }

  .row > .col-md-6 {
      margin-bottom: 20px;
  }
  </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

  <div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="notificationsModalLabel">Notifications</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <?php if (count($notifications) === 0): ?>
              <p>No notifications.</p>
          <?php else: ?>
              <?php foreach ($notifications as $notif): ?>
                  <?php $class = $notif['is_read'] ? "alert-secondary" : "alert-warning"; ?>
                  <div class="alert <?php echo $class; ?>">
                      <?php echo $notif['message']; ?> <br>
                      <small class="text-muted"><?php echo $notif['created_at']; ?></small>
                  </div>
              <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="container mt-4">
    <?php if ($flashMessage): ?>
      <?php $alertClass = ($flashStatus === '200') ? 'alert-success' : 'alert-warning'; ?>
      <div class="alert <?php echo $alertClass; ?>">
        <?php echo htmlspecialchars($flashMessage); ?>
      </div>
    <?php endif; ?>

    <div class="row">
      <div class="col-md-6">
        <h1>Received Requests</h1>
        <?php if (empty($incomingRequests)): ?>
          <p>No incoming requests.</p>
        <?php else: ?>
          <?php foreach ($incomingRequests as $req): ?>
            <div class="card mb-3">
              <div class="card-body">
                <h5 class="card-title mb-1"><?php echo htmlspecialchars($req['title']); ?></h5>
                <p class="mb-3">
                  <strong><?php echo htmlspecialchars($req['requester_name']); ?></strong>
                  requested edit &middot;
                  <small class="text-muted"><?php echo $req['created_at']; ?></small>
                </p>

                <p class="mb-3">
                  Status:
                  <?php if ($req['status'] === 'pending'): ?>
                    <span class="status-pill badge badge-warning">PENDING</span>
                  <?php elseif ($req['status'] === 'approved'): ?>
                    <span class="status-pill badge badge-success">APPROVED</span>
                  <?php else: ?>
                    <span class="status-pill badge badge-secondary">REJECTED</span>
                  <?php endif; ?>
                </p>

                <?php if ($req['status'] === 'pending'): ?>
                  <form method="POST" action="core/handleForms.php" class="d-inline" onsubmit="return confirm('Approve this edit request?');">
                    <input type="hidden" name="request_id" value="<?php echo (int)$req['request_id']; ?>">
                    <button type="submit" name="approveEditBtn" class="btn btn-sm btn-success">Approve</button>
                  </form>

                  <form method="POST" action="core/handleForms.php" class="d-inline" onsubmit="return confirm('Reject this edit request?');">
                    <input type="hidden" name="request_id" value="<?php echo (int)$req['request_id']; ?>">
                    <button type="submit" name="rejectEditBtn" class="btn btn-sm btn-danger">Reject</button>
                  </form>
                <?php else: ?>
                  <small class="text-muted">No actions available.</small>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div class="col-md-6">
        <h1>Sent Requests</h1>
        <?php if (empty($sentRequests)): ?>
          <p>You haven't sent any edit requests yet.</p>
        <?php else: ?>
          <?php foreach ($sentRequests as $r): ?>
            <div class="card mb-3">
              <div class="card-body">
                <h5 class="card-title mb-1"><?php echo htmlspecialchars($r['title']); ?></h5>
                <p class="mb-1">
                  Owner: <strong><?php echo htmlspecialchars($r['author_name'] ?? ($r['username'] ?? '')); ?></strong>
                  &middot; <small class="text-muted"><?php echo $r['created_at']; ?></small>
                </p>

                <p class="mb-0">
                  Status:
                  <?php if ($r['status'] === 'pending'): ?>
                    <span class="status-pill badge badge-warning">PENDING</span>
                  <?php elseif ($r['status'] === 'approved'): ?>
                    <span class="status-pill badge badge-success">APPROVED</span>
                  <?php else: ?>
                    <span class="status-pill badge badge-secondary">REJECTED</span>
                  <?php endif; ?>
                </p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>