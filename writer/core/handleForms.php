<?php  
require_once '../classloader.php';

if (isset($_POST['insertNewUserBtn'])) {
	$username = htmlspecialchars(trim($_POST['username']));
	$email = htmlspecialchars(trim($_POST['email']));
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);

	if (!empty($username) && !empty($email) && !empty($password) && !empty($confirm_password)) {

		if ($password == $confirm_password) {

			if (!$userObj->usernameExists($username)) {

				if ($userObj->registerUser($username, $email, $password)) {
					header("Location: ../login.php");
				}

				else {
					$_SESSION['message'] = "An error occured with the query!";
					$_SESSION['status'] = '400';
					header("Location: ../register.php");
				}
			}

			else {
				$_SESSION['message'] = $username . " as username is already taken";
				$_SESSION['status'] = '400';
				header("Location: ../register.php");
			}
		}
		else {
			$_SESSION['message'] = "Please make sure both passwords are equal";
			$_SESSION['status'] = '400';
			header("Location: ../register.php");
		}
	}
	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}
}


if (isset($_POST['loginUserBtn'])) {
	$email = trim($_POST['email']);
	$password = trim($_POST['password']);

	if (!empty($email) && !empty($password)) {

		if ($userObj->loginUser($email, $password)) {
			header("Location: ../index.php");
		}
		else {
			$_SESSION['message'] = "Username/password invalid";
			$_SESSION['status'] = "400";
			header("Location: ../login.php");
		}
	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../login.php");
	}

}


if (isset($_GET['logoutUserBtn'])) {
	$userObj->logout();
	header("Location: ../index.php");
}


if (isset($_POST['insertArticleBtn'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $author_id = $_SESSION['user_id'];


    $imagePath = null;

    if (isset($_FILES['article_image']) && $_FILES['article_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . '_' . basename($_FILES['article_image']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['article_image']['tmp_name'], $targetPath)) {
            $imagePath = $fileName;
        }
    }

    if ($articleObj->createArticle($title, $description, $author_id, $imagePath, $category_id)) {
        $admins = $userObj->getAdmins();
        foreach ($admins as $admin) {
            $message = $_SESSION['username'] . " submitted a new article: '" . $title . "'";
            $notificationObj->createNotification($admin['user_id'], $message);
        }

        header("Location: ../articles_submitted.php");
        exit;
    }
}

if (isset($_POST['editArticleBtn'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $article_id = $_POST['article_id'];

    $imagePath = null;
    if (isset($_FILES['article_image']) && $_FILES['article_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . '_' . basename($_FILES['article_image']['name']);
        $targetPath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['article_image']['tmp_name'], $targetPath)) {
            $imagePath = $fileName;
        }
    }

    if ($articleObj->updateArticle($article_id, $title, $description, $imagePath, $category_id)) {
        header("Location: ../articles_submitted.php");
        exit;
    }
}

if (isset($_POST['deleteArticleBtn'])) {
    $article_id = $_POST['article_id'];
    echo $articleObj->deleteArticle($article_id);
}

if(isset($_POST['markNotificationsRead'])) {
    $notificationObj->markAllAsRead($_SESSION['user_id']);
    exit; 
}


if (isset($_POST['requestEditBtn'])) {
    $article_id   = $_POST['article_id'];
    $requester_id = $_SESSION['user_id'];

    $existing = $editRequestObj->getExistingRequest($article_id, $requester_id);

    if (!$existing) {
        $editRequestObj->createRequest($article_id, $requester_id);

        $article   = $articleObj->getArticles($article_id);
        $author_id = $article['author_id'];

        $message = $_SESSION['username'] . " requested to edit your article '" . $article['title'] . "'.";
        $notificationObj->createNotification($author_id, $message);

        $_SESSION['message'] = "Edit request sent.";
        $_SESSION['status']  = "200";
    } else {
        $_SESSION['message'] = "You already have a pending/approved request for this article.";
        $_SESSION['status']  = "400";
    }

    header("Location: ../index.php");
    exit;
}


if (isset($_POST['approveEditBtn'])) {
    $request_id = $_POST['request_id'];

    $editRequestObj->updateStatus($request_id, 'approved');

    $req     = $editRequestObj->getRequestById($request_id);
    $article = $articleObj->getArticles($req['article_id']);

    $message = "Your edit request for '" . $article['title'] . "' was approved.";
    $notificationObj->createNotification($req['requester_id'], $message);

    $_SESSION['message'] = "Request approved.";
    $_SESSION['status']  = "200";

    header("Location: ../edit_request.php");
    exit;
}


if (isset($_POST['rejectEditBtn'])) {
    $request_id = $_POST['request_id'];

    $editRequestObj->updateStatus($request_id, 'rejected');

    $req     = $editRequestObj->getRequestById($request_id);
    $article = $articleObj->getArticles($req['article_id']);

    $message = "Your edit request for '" . $article['title'] . "' was rejected.";
    $notificationObj->createNotification($req['requester_id'], $message);

    $_SESSION['message'] = "Request rejected.";
    $_SESSION['status']  = "400";

    header("Location: ../edit_request.php");
    exit;
}