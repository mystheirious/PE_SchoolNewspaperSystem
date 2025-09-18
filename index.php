<?php require_once 'writer/classloader.php'; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <link rel="icon" type="image/svg+xml" href="images/logo.svg">

    <style>
    body {
        font-family: "Segoe UI", Arial, sans-serif;
        background-color: #EEFAFC;
        color: #333;
        line-height: 1.6;
    }

    .navbar-custom {
        background-color: #FEEDB9;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .navbar-custom .navbar-brand {
        color: #000;
        font-weight: 700;
        font-size: 1.3rem;
        letter-spacing: 0.5px;
    }

    .welcome-section {
        text-align: center;
        margin: 50px 0 60px 0;
    }

    .welcome-section h1 {
        font-weight: 800;
        font-size: 2.6rem;
        color: #1a1a1a;
        line-height: 1.2;
    }

    .welcome-section p {
        font-size: 1.15rem;
        color: #555;
        margin-top: 12px;
        max-width: 780px;
        margin-left: auto;
        margin-right: auto;
    }

    .role-card {
        max-width: 400px;
        margin: 0 auto 35px auto;
        border-radius: 10px;
        background-color: #fffaeb;
        box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        padding-bottom: 10px;
    }

    .role-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.18);
    }

    .role-card h2 {
        font-weight: 700;
        color: #111;
        font-size: 1.7rem;
        margin-bottom: 12px;
        padding-top: 10px;
    }

    .role-card p {
        font-size: 1rem;
        color: #555;
        line-height: 1.6;
        padding: 8px 15px 15px 15px;
    }

    .role-card img {
        margin-bottom: 15px;
        border-radius: 12px;
        max-height: 180px;
        object-fit: cover;
        border: 4px solid #FEEDB9;
    }

    .articles-section {
        margin: 50px 0;
    }

    .articles-section h2 {
        font-weight: 800;
        font-size: 2.2rem;
        margin-bottom: 30px;
        text-align: center;
    }

    .card {
        border-radius: 10px;
        background-color: #fffaeb;
        box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        padding: 20px;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.15);
    }
    
    .card img {
        border-radius: 12px;
        border: 2px solid #FEEDB9;
        max-width: 100%;
        margin-top: 12px;
        margin-bottom: 10px;
    }

    .article-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .article-header h3 {
        font-weight: 700;
        color: #111;
        font-size: 1.5rem;
        margin: 0;
    }

    .article-meta {
        font-size: 0.85rem;
        color: #555;
    }

    .article-meta .author {
        font-weight: 700;
        margin-right: 5px;
    }

    .article-icon-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .article-icon-container .line {
        flex: 1;
        height: 4px;
        background-color: #9EE3F0;
    }

    .article-icon {
        width: 150;
        height: 120px;
        object-fit: contain;
    }

    .article-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.8rem;
        background-color: #9EE3F0;
        color: #000;
        margin-bottom: 8px;
        font-weight: 600;
    }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-custom">
      <a class="navbar-brand" href="#">School Publication Homepage</a>
    </nav>

    <div class="container">
        <div class="welcome-section">
            <h1>Welcome to the School Publication</h1>
            <p>Explore articles from our talented writers and stay updated with messages from the admin team.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="role-card text-center">
                    <h2>Writer</h2>
                    <img src="images/writer.jpg" class="img-fluid">
                    <p>Our writers create compelling and informative articles, capturing readers’ attention while delivering clear and insightful content.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="role-card text-center">
                    <h2>Admin</h2>
                    <img src="images/admin_1.jpg" class="img-fluid">
                    <p>Admins oversee all publications to ensure every article meets our standards, maintaining quality, clarity, and the overall vision of the school publication.</p>
                </div>
            </div>
        </div>

        <div class="articles-section mt-4">
            <h2 class="mb-2">Published Articles</h2>
            <div class="article-icon-container text-center my-3">
                <span class="line"></span>
                <img src="images/articles.png" alt="Article Icon" class="article-icon">
                <span class="line"></span>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8 mt-4">
                    <?php $articles = $articleObj->getActiveArticles(); ?>
                    <?php foreach ($articles as $article) { ?>
                    <div class="card">
                        <?php if ($article['is_admin'] == 1) { ?>
                            <span class="article-badge">Message from Admin</span>
                        <?php } ?>
                        <div class="article-header">
                            <h3><?php echo $article['title']; ?></h3>
                            <div class="article-meta">
                                <span class="author"><?php echo $article['username']; ?></span>
                                <span>- <?php echo $article['created_at']; ?></span>
                            </div>
                        </div>
                        <?php if (!empty($article['image_path'])): ?>
                            <img src="uploads/<?php echo $article['image_path']; ?>" alt="Article Image">
                        <?php endif; ?>
                        <p><?php echo $article['content']; ?></p>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>