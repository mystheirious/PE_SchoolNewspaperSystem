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

$sharedArticles = $articleObj->getSharedArticlesByUser($_SESSION['user_id']);

$notifications = $notificationObj->getNotificationsByUser($_SESSION['user_id']);
$categories = $categoryObj->getCategories(); 

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
    <title>Shared Articles</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" type="image/svg+xml" href="../images/logo.svg">
    <style>
    body {
        font-family: "Segoe UI", Arial, sans-serif;
        background-color: #fff;
    }

    .card {
        background-color: #fff8e5;
        border-radius: 5px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        transition: transform 0.2s ease;
        padding: 35px;
        margin-bottom: 20px;
        cursor: pointer;
    }

    .card:hover {
        transform: translateY(-3px);
    }

    .card h4 {
        margin-bottom: 5px;
    }

    .article-meta {
        margin-right: 5px;
    }

    .article-icon {
        max-width: 10%;
        display: block;
        margin: 5px auto;
    }

    .article-img {
        max-width: 100%;
        margin-top: 10px;
        border-radius: 10px;
        border: 2px solid #FEEDB9;
    }

    .btn-custom {
        background-color: #9EE3F0;
        color: #000;
        border: none;
    }

    .btn-custom:hover {
        background-color: #85cfe0;
        color: #000;
    }

    textarea.form-control {
        resize: vertical;
    }

    .section-title {
        font-weight: 600;
        color: #355E3B;
        text-align: center;
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

    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-6">
            <img src="../images/share.jpg" alt="Share Icon" class="article-icon mt-4 mb-2">
            <h1 class="section-title mb-2">Articles Shared With You</h1>
            <p class="lead text-muted text-center mb-5">Double-click an article to edit it.</p>

            <?php if (count($sharedArticles) === 0): ?>
                <p class="text-center">No shared articles yet.</p>
            <?php else: ?>
                <?php foreach ($sharedArticles as $article): ?>
                      <div class="card mt-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <h2 class="mb-1"><?php echo $article['title']; ?></h2>
                              <div class="text-right article-meta mt-1">
                                  <strong><?php echo $article['author_name']; ?></strong>
                                  <small class="text-muted"> - <?php echo $article['created_at']; ?></small>
                            </div>
                        </div>
                        <?php if (!empty($article['category_name'])): ?>
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($article['category_name']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($article['image_path'])): ?>
                            <img src="../uploads/<?php echo $article['image_path']; ?>" class="article-img mb-4" alt="Article Image">
                        <?php endif; ?>
                        <p><?php echo htmlspecialchars($article['content']); ?></p>

                        <div class="updateArticleForm d-none mt-3">
                            <h5>Edit Article</h5>
                            <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
                                <input type="text" class="form-control mt-2" name="title" value="<?php echo htmlspecialchars($article['title']); ?>" required>
                                <textarea name="description" class="form-control mt-2" required><?php echo htmlspecialchars($article['content']); ?></textarea>
                                <input type="file" name="article_image" class="form-control mt-2" accept="image/*">
                                <select name="category_id" class="form-control mt-2" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['category_id']; ?>" 
                                            <?php echo ($article['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                                <div class="d-flex justify-content-end mt-4">
                                    <input type="submit" name="editArticleBtn" class="btn btn-custom" value="Submit">
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
      </div>
    </div>

    <script>
    $(document).ready(function() {
        $('.card').on('dblclick', function() {
            $(this).find('.updateArticleForm').toggleClass('d-none');
        });
    });
    </script>
</body>
</html>