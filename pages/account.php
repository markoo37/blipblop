<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /blipblop/index.php?page=login");
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

require_once "includes/dbh-inc.php";
$conn = getConnection();

$sql = "SELECT 
            v.video_id,
            v.uploader_user_id,
            v.title,
            TO_CHAR(v.upload_time, 'YYYY-MM-DD HH24:MI:SS') AS upload_time,
            v.views,
            u.username
        FROM videos v
        JOIN app_users u ON v.uploader_user_id = u.user_id";

$stmt = $conn->query($sql);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once 'includes/new_password_view-inc.php';

try{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_password = htmlspecialchars($_POST['new_password']);
        $confirm_new_password = htmlspecialchars($_POST['confirm_new_password']);

        require_once "includes/register_contr-inc.php";
        $sql_user = "SELECT username, pword FROM app_users WHERE user_id = :user_id";
        $stmt_password = $conn->prepare($sql_user);
        $stmt_password->execute([':user_id' => $user_id]);
        $user = $stmt_password->fetch(PDO::FETCH_ASSOC);

        $errors = [];

        if (empty($new_password) || empty($confirm_new_password)) {
            $errors["empty_inputs"] = "Nem lehet üres egyik mező sem!";
        }

        if (passwords_dont_match($new_password, $confirm_new_password)) {
            $errors["not_matching"] = "A két jelszó nem egyezik!";
        }

        if (is_password_tooShort($new_password)) {
            $errors["password_too_short"] = "A megadott jelszó túl rövid!";
        }

        if (password_verify($new_password, $user['PWORD'])) {
            $errors["samepass"] = "A megadott jelszó egyezik az eddigi jelszóval!";
        }

        if ($errors){
            $_SESSION['error_new_password'] = $errors;
            header("Location: /blipblop/index.php?page=account");

            die();
        }

        $query_of_pword_change = "UPDATE app_users SET pword = :password WHERE user_id = :user_id";
        $stmt_password = $conn->prepare($query_of_pword_change);
        $stmt_password->execute([':password' => password_hash($new_password, PASSWORD_DEFAULT), ':user_id' => $user_id]);


        echo '<p class="success-text">A jelszó sikeresen megváltoztatva! Átirányítás a kezdőlapra...</p>';
        echo "<script>
            setTimeout(function() {
                window.location.href = '/blipblop/index.php?page=home';
                }, 1500);
            </script>";

        exit();

    }
}
catch(PDOException $e){
    echo "DB error: " . $e->getMessage();
}

?>

<div class="account-container">
    <h1>Profilom</h1>

    <div class="user-profile">
        <div class="profile-header">
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($username); ?></h2>
            </div>
        </div>

        <div class="account-tabs">
            <!--<ul class="tab-links">
                <li class="active"><a href="#my-videos">My Videos</a></li>
                <li><a href="#liked-videos">Liked Videos</a></li>
                <li><a href="#settings">Account Settings</a></li>
            </ul>-->

            <div class="tab-content">
                <div id="my-videos" class="tab-pane active">
                    <div class="section-header">
                        <h3>Videóim</h3>
                    </div>

                    <?php if ($videos): // Replace with actual check for user videos ?>
                        <div class="profile-videos">

                            <?php foreach ($videos as $video): ?>
                                <?php
                                $rawTimestamp = $video['UPLOAD_TIME'];// pl. "2025-04-22 16:38:57.123456"
                                $datetime = new DateTime($rawTimestamp);
                                ?>
                                <?php if($video["USERNAME"] == $username): ?>
                                    <div class="video-card small">
                                        <a href="index.php?page=watch&id=<?= $video['VIDEO_ID'] ?>" class="page-link" data-page="watch" data-id="<?= $video['VIDEO_ID'] ?>">
                                            <!--<div class="thumbnail">
                                                <img src="images/placeholder.jpg" alt="Video thumbnail">
                                                <div class="video-duration">2:30</div> ezt később ki lehet számolni vagy lekérni is
                                            </div>-->
                                            <div class="video-info">
                                                <h3 class="video-title"><?= htmlspecialchars($video['TITLE']) ?></h3>
                                                <div class="video-meta">
                                                    <span class="channel-name"><?= $video['USERNAME'] ?> </span>
                                                    <span>• <?= number_format($video['VIEWS']) ?> megtekintés </span>
                                                    <span>• <?= $datetime->format('Y-m-d'); ?></span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <!-- … -->
                        </div>
                    <?php else: ?>
                        <p>Nincs még feltöltött videód.</p>
                        <a href="index.php?page=upload" class="btn page-link" data-page="upload">Feltöltés</a>
                    <?php endif; ?>
                </div>

                <div id="liked-videos" class="tab-pane">
                    <h3>Ezek tetszettek Neked</h3>

                    <?php if (true): // Replace with actual check for liked videos ?>
                        <p class="no-content">Nincs még kedvelt videód</p>
                    <?php else: ?>
                        <div class="video-grid">
                            <!-- Liked videos would go here -->
                        </div>
                    <?php endif; ?>
                </div>

                <div id="settings" class="tab-pane">
                    <h3>Jelszó megváltoztatása</h3>

                    <form action="" method="post" class="settings-form" onsubmit="return confirm('Biztosan megváltoztatod a jelszavad?');">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="new_password">Új jelszó</label>
                                <input type="password" id="new_password" name="new_password" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="confirm_new_password">Új jelszó megerősítése</label>
                                <input type="password" id="confirm_new_password" name="confirm_new_password" class="form-control">
                            </div>
                        </div>
                        <button type="submit" class="btn">Mentés</button>
                    </form>
                    <?php check_new_password_errors(); ?>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .account-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .user-profile {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .profile-header {
        display: flex;
        align-items: center;
        padding: 20px;
        background: #f5f5f5;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 20px;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-info h2 {
        margin-bottom: 5px;
    }

    .account-tabs {
        padding: 20px;
    }

    .tab-links {
        display: flex;
        list-style: none;
        border-bottom:
    }

    h3 {
        margin-bottom: 15px;
        margin-top: 15px;
    }

</style>