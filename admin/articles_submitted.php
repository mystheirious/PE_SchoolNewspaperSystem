<?php 
require_once 'classloader.php';

if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
}

if (!$userObj->isAdmin()) {
  header("Location: ../writer/index.php");
}  

$notifications = $notificationObj->getNotificationsByUser($_SESSION['user_id']);

$unreadCount = 0;
foreach ($notifications as $notif) {
    if (!$notif['is_read']) $unreadCount++;
}

$notificationObj->markAllAsRead($_SESSION['user_id']);

$categories = $categoryObj->getCategories(); 
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Articles Submitted</title>
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

        .card-box {
            background-color: #DCF5F9;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .card {
            background-color: #DCF5F9;
            border-radius: 5px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s ease;
            padding: 35px;
            margin-bottom: 25px;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .card h2, .card h1 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .article-meta {
            display: flex;
            justify-content: flex-end;
            font-size: 0.85rem;
            color: #355E3B;
            align-items: center;
        }

        .article-meta strong {
            margin-right: 5px;
        }

        .article-icon {
            max-width: 20%;
            display: block;
            margin: 5px auto;
        }

        .article-img {
            max-width: 100%;
            margin-top: 10px;
            border-radius: 10px;
            border: 2px solid #B9EBF3;
        }

        .btn-warning, .btn-success, .btn-secondary, .btn-danger, .btn-primary {
            border-radius: 8px;
            font-weight: 500;
            padding: 4px 10px;
            font-size: 0.85rem;
            margin-right: 5px;
        }

        .btn-custom {
            background-color: #FEEDB9;
            color: #000;
            border: none;
        }

        .btn-custom:hover {
            background-color: #FDE59B;
            color: #000;
        }

        textarea.form-control {
            resize: vertical;
        }

        .section-title {
            font-weight: 600;
            color: #355E3B;
            text-align: center;
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

    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="text-center mt-4 mb-4">
              <img src="../images/submit.jpg" alt="Submit Icon" class="article-icon mb-2">
              <button id="toggleFormBtn" class="btn btn-custom">Submit a New Article</button>
          </div>
          <div id="submitFormBox" class="card-box d-none">
              <h2 class="section-title">New Article</h2>
              <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
                  <input type="text" class="form-control mt-3" name="title" placeholder="Article Title" required>
                  <textarea name="description" class="form-control mt-3" placeholder="Write your article here..." required></textarea>

                  <select name="category_id" class="form-control mt-3" required>
                      <option value="">Select Category</option>
                      <?php foreach ($categories as $cat): ?>
                          <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                      <?php endforeach; ?>
                  </select>

                  <input type="file" name="article_image" class="form-control mt-3" accept="image/*">

                  <div class="d-flex justify-content-end mt-3">
                      <input type="submit" class="btn btn-custom" name="insertArticleBtn" value="Submit">
                  </div>
              </form>
          </div>

          <div class="icon-header">
            <h1 class="section-title">All Submitted Articles</h1>
            <p class="lead text-muted text-center mb-4">Double-click an article to edit it.</p>
          </div>
          <?php $articles = $articleObj->getArticlesByUserID($_SESSION['user_id']); ?>
          <?php foreach ($articles as $article) { ?>
          <div class="card">
            <div class="d-flex justify-content-between align-items-start">
                <h2 class="mb-1"><?php echo $article['title']; ?></h2>
                <div class="text-right article-meta mt-1">
                    <strong><?php echo $article['username']; ?></strong>
                    <small class="text-muted"> - <?php echo $article['created_at']; ?></small>
                </div>
            </div>
            <?php if (!empty($article['category_name'])): ?>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($article['category_name']); ?></p>
            <?php endif; ?>
            <?php if (!empty($article['image_path'])): ?>
                <img src="../uploads/<?php echo $article['image_path']; ?>" class="article-img mb-3" alt="Article Image">
            <?php endif; ?>
            <p><?php echo $article['content']; ?></p>
            <?php if ($article['is_active'] == 0) { ?>
                <p class="text-danger">Status: PENDING</p>
            <?php } else { ?>
                <p class="text-success">Status: ACTIVE</p>
            <?php } ?>
            <form class="deleteArticleForm d-inline">
                <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>" class="article_id">
                <input type="submit" class="btn btn-danger btn-sm" value="Delete">
            </form>

            <div class="updateArticleForm d-none mt-3">
                <h5>Edit Article</h5>
                <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
                    <input type="text" class="form-control mt-2" name="title" value="<?php echo $article['title']; ?>">
                    <textarea name="description" class="form-control mt-2"><?php echo $article['content']; ?></textarea>

                    <select name="category_id" class="form-control mt-2" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['category_id']; ?>" 
                                <?php echo ($article['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input type="file" class="form-control mt-2" name="article_image" accept="image/*">

                    <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                    <div class="d-flex justify-content-end mt-4">
                        <input type="submit" class="btn btn-custom" name="editArticleBtn" value="Submit">
                    </div>
                </form>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>

    <script>
    $('#toggleFormBtn').on('click', function() {
        $('#submitFormBox').toggleClass('d-none');
    });

    $('.card').on('dblclick', function () {
        $(this).find('.updateArticleForm').toggleClass('d-none');
    });

    $('.deleteArticleForm').on('submit', function (event) {
        event.preventDefault();
        var formData = {
            article_id: $(this).find('.article_id').val(),
            deleteArticleBtn: 1
        };
        if (confirm("Are you sure you want to delete this article?")) {
            $.ajax({
                type:"POST",
                url: "core/handleForms.php",
                data:formData,
                success: function (data) {
                    if (data) { location.reload(); }
                    else { alert("Deletion failed"); }
                }
            });
        }
    });
    </script>
</body>
</html>