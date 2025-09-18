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
  <title>Writer Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="icon" type="image/svg+xml" href="../images/logo.svg">

  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

  <style>
  body {
      font-family: "Segoe UI", Arial, sans-serif;
      background-color: #fff;
      color: #333;
      line-height: 1.6;
  }

  .display-4 {
      font-weight: 600;
      margin-top: 20px;
      text-align: center;
      color: #355E3B;
  }

  .article-icon-container {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      margin: 20px 0;
  }

  .article-icon-container .line {
      flex: 1;
      height: 4px;
      background-color: #FEEDB9;
  }

  .article-icon {
      width: 190px;      
      height: 80px;
      object-fit: contain;
  }

  .card {
      background-color: #fffaeb;
      border-radius: 5px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.1);
      transition: transform 0.2s ease;
      padding: 15px;
      margin-bottom: 25px;
  }

  .card:hover {
      transform: translateY(-5px);
  }

  .card h2, .card h1 {
      font-weight: 600;
      margin-bottom: 10px;
      color: #355E3B;
  }

  .card p {
      color: #333;
      margin-top: 0.5rem;
      margin-bottom: 0.5rem;
  }

  .article-badge, .alert-admin {
      display: block;
      width: 100%;
      padding: 4px 8px;
      border-radius: 8px;
      font-size: 0.8rem;
      font-weight: 600;
      background-color: #B9EBF3;
      color: #000;
      margin-bottom: 10px;
  }

  .article-img {
      display: block;
      margin: 10px auto;
      max-width: 100%;
      width: 100%;  
      height: auto;
      border-radius: 10px;
      border: 2px solid #FEEDB9;
      object-fit: cover;
  }

  .article-meta {
      display: flex;
      justify-content: flex-end;
      font-size: 0.85rem;
      color: #355E3B;
      align-items: center;
  }

  .article-meta .author {
      font-weight: 700;
      margin-right: 5px;
  }

  .btn-warning, .btn-success, .btn-secondary, .btn-danger, .btn-primary {
      border-radius: 8px;
      font-weight: 500;
      padding: 4px 10px;
      font-size: 0.85rem;
      margin-right: 5px;
  }

  .modal-content {
      border-radius: 15px;
  }

  .alert-warning {
      background-color: #FFF3CD;
      color: #856404;
      border-radius: 8px;
  }

  .alert-secondary {
      background-color: #E2E3E5;
      color: #41464B;
      border-radius: 8px;
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

  <div class="container">
    <div class="text-center mb-4">
      <h1 class="display-4 mb-2">Welcome, <?php echo $_SESSION['username']; ?>!</h1>
      <p class="lead text-muted mb-4">You can view all the articles available here.</p>
      <div class="article-icon-container">
        <span class="line"></span>
        <img src="../images/star.jpg" alt="Article Icon" class="article-icon">
        <span class="line"></span>
      </div>
    </div>

    <div class="row justify-content-center">
      <div class="col-md-10">
        <?php $articles = $articleObj->getActiveArticles(); ?>
        <?php foreach ($articles as $article): 
              $isShared = $editRequestObj->getExistingRequest($article['article_id'], $_SESSION['user_id']);
              $hasPending = $isShared && $isShared['status'] === 'pending';
              $isApproved = $isShared && $isShared['status'] === 'approved';
        ?>
        <div class="card">
          <div class="card-body">
              <?php if ($article['is_admin'] == 1): ?>
                  <span class="article-badge">Message From Admin</span>
              <?php endif; ?>

              <div class="d-flex justify-content-between align-items-center mb-2">
                  <h2 class="mb-0"><?php echo $article['title']; ?></h2>
                  <div class="article-meta text-right">
                      <span class="author"><?php echo $article['username'] ?></span> - 
                      <small class="text-muted"><?php echo $article['created_at']; ?></small>
                  </div>
              </div>

              <?php if (!empty($article['category_name'])): ?>
                  <p><strong>Category:</strong> <?php echo htmlspecialchars($article['category_name']); ?></p>
              <?php endif; ?>

              <?php if (!empty($article['image_path'])): ?>
                <div class="text-center w-100">
                  <img src="../uploads/<?php echo $article['image_path']; ?>" class="article-img">
                </div>
              <?php endif; ?>

              <p><?php echo $article['content']; ?></p>

              <?php if ($article['author_id'] != $_SESSION['user_id'] && $article['is_admin'] != 1): ?>
                <div class="mt-3">
                  <?php if ($isApproved): ?>
                    <a href="shared_articles.php" class="btn btn-success btn-sm">Shared with you</a>
                  <?php elseif ($hasPending): ?>
                    <span class="btn btn-secondary btn-sm">Pending Edit Request</span>
                  <?php else: ?>
                    <form action="core/handleForms.php" method="POST" class="d-inline">
                      <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                      <button type="submit" name="requestEditBtn" class="btn btn-warning btn-sm">Request Edit</button>
                    </form>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</body>
</html>