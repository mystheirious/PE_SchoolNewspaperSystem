<?php 
require_once 'classloader.php';

if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
  exit;
}

if (!$userObj->isAdmin()) {
  header("Location: ../writer/index.php");
  exit;
}

$notifications = $notificationObj->getNotificationsByUser($_SESSION['user_id']);
$unreadCount = 0;
foreach ($notifications as $notif) {
    if (!$notif['is_read']) $unreadCount++;
}

$notificationObj->markAllAsRead($_SESSION['user_id']);

$categories = $categoryObj->getCategories();
$articles = $articleObj->getArticles();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Pending Articles</title>
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

        .card {
            background-color: #dcf5f9;
            border-radius: 5px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
            padding: 15px;
            margin-bottom: 25px;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-body h1, .card-body h2 {
            font-weight: 600;
            color: #355E3B;
        }

        .card-body p {
            color: #333;
            margin-top: 0.5rem;
        }

        .alert-admin {
            background-color: #FEEDB9;
            color: #000;
            border-radius: 8px;
            padding: 4px 8px;
            display: inline-block;
            margin-bottom: 10px;
        }

        .article-img {
            display: block;
            margin: 10px auto;
            max-width: 100%;
            width: 100%;  
            height: auto;
            border-radius: 10px;
            border: 2px solid #B9EBF3;
            object-fit: cover;
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

        .article-icon {
          width: 190px; 
          display: block; 
          margin: 5px auto;
        }

        .display-4 {
            font-weight: 600;
            margin-top: 5px;
            text-align: center;
            color: #355E3B;
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

        .section-title {
            font-weight: 600;
            color: #355E3B;
            text-align: center;
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

    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="text-center mt-4 mb-2">
                <button id="toggleCategoryFormBtn" class="btn btn-custom">Add New Category</button>
            </div>

            <div id="categoryFormBox" class="card p-3 d-none">
                <h2 class="section-title mb-4">New Category</h2>
                <form id="addCategoryForm" action="core/handleForms.php" method="POST">
                    <div class="form-group">
                        <input type="text" name="name" class="form-control" placeholder="Category Name" required>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <input type="submit" class="btn btn-custom" name="addCategory" value="Add">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="text-center">
            <img src="../images/pending.jpg" alt="Pending Icon" class="article-icon mt-4">
            <div class="display-4 mb-4">Pending Articles</div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php $articles = $articleObj->getArticles(); ?>
                <?php foreach ($articles as $article) { ?>
                <div class="card mt-4">
                    <div class="card-body">
                        <h2><?php echo $article['title']; ?></h2> 
                        <small><?php echo $article['username'] ?> - <?php echo $article['created_at']; ?> </small>
                        <?php if ($article['is_active'] == 0) { ?>
                            <p class="text-danger">Status: PENDING</p>
                        <?php } ?>
                        <?php if ($article['is_active'] == 1) { ?>
                            <p class="text-success">Status: ACTIVE</p>
                        <?php } ?>
                        <?php if (!empty($article['image_path'])): ?>
                            <img src="../uploads/<?php echo $article['image_path']; ?>" class="article-img mb-3" alt="Article Image">
                        <?php endif; ?>
                        <p><?php echo $article['content']; ?> </p>
                        <form class="updateArticleCategory">
                            <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                            <div class="form-group">
                                <label>Category:</label>
                                <select name="category_id" class="form-control article-category" article_id="<?php echo $article['article_id']; ?>">
                                    <option value="">Select category</option>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?= $cat['category_id'] ?>" <?= ($article['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                        <form class="updateArticleStatus">
                            <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>" class="article_id">
                            <select name="is_active" class="form-control is_active_select" article_id=<?php echo $article['article_id']; ?>>
                                <option value="">Select article status</option>
                                <option value="0">Pending</option>
                                <option value="1">Active</option>
                            </select>
                        </form>
                        <form class="deleteArticleForm">
                            <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>" class="article_id">
                            <input type="submit" class="btn btn-danger float-right mt-4 deleteArticleBtn" value="Delete">
                        </form>
                    </div>
                </div>  
                <?php } ?> 
            </div>
        </div>
    </div>

    <script>
    $('.deleteArticleForm').on('submit', function (event) {
       event.preventDefault();
       var formData = {
         article_id: $(this).find('.article_id').val(),
         deleteArticleBtn: 1
       }
       if (confirm("Are you sure you want to delete this article?")) {
         $.ajax({
           type:"POST",
           url: "core/handleForms.php",
           data:formData,
           success: function (data) {
             if (data) {
               location.reload();
             } else {
               alert("Deletion failed");
             }
           }
         })
       }
    })

    $('.is_active_select').on('change', function (event) {
        event.preventDefault();
        var formData = {
          article_id: $(this).attr('article_id'),
          status: $(this).val(),
          updateArticleVisibility:1
        }

        if (formData.article_id != "" && formData.status != "") {
          $.ajax({
            type:"POST",
            url: "core/handleForms.php",
            data:formData,
            success: function (data) {
              if (data) {
                location.reload();
              } else {
                alert("Visibility update failed");
              }
            }
          })
        }
    })

    $('#addCategoryForm').on('submit', function(e){
        e.preventDefault();
        var formData = $(this).serialize() + '&addCategory=1';
        $.ajax({
            type: "POST",
            url: "core/handleForms.php",
            data: formData,
            success: function(data){
                if(data){
                    location.reload();
                } else {
                    alert("Failed to add category");
                }
            }
        });
    });

    $('.article-category').on('change', function(){
        var article_id = $(this).attr('article_id');
        var category_id = $(this).val();
        if(article_id != "" && category_id != ""){
            $.ajax({
                type: "POST",
                url: "core/handleForms.php",
                data: { article_id: article_id, category_id: category_id, updateArticleCategory: 1 },
                success: function(data){
                    if(data){
                        location.reload();
                    } else {
                        alert("Failed to update category");
                    }
                }
            });
        }
    });

    $('#toggleCategoryFormBtn').on('click', function() {
        $('#categoryFormBox').toggleClass('d-none');
    });
    </script>
</body>
</html>