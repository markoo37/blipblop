<h1>Tech</h1>

<div class="categories">
    <a href="index.php?page=home&category=all"><div class="category">All</div></a>
    <?php
    require_once "includes/dbh-inc.php";
    $conn = getConnection();

    $stmt = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($categories as $category): ?>
        <?php if ($category['CATEGORY_ID'] == '4'): ?>
            <a href="index.php?page=home&category=<?= $category['CATEGORY_ID'] ?>"><div class="category active"><?= htmlspecialchars($category['CATEGORY_NAME']) ?></div></a>
        <?php else: ?>
            <a href="index.php?page=home&category=<?= $category['CATEGORY_ID'] ?>"><div class="category"><?= htmlspecialchars($category['CATEGORY_NAME']) ?></div></a>
        <?php endif; ?>
    <?php endforeach; ?>

</div>


<?php
require_once "includes/dbh-inc.php";
$conn = getConnection();

$sql = "SELECT 
            v.video_id,
            v.uploader_user_id,
            v.title,
            TO_CHAR(v.upload_time, 'YYYY-MM-DD HH24:MI:SS') AS upload_time,
            v.views,
            u.username,
            c.category_name
        FROM videos v
        JOIN app_users u ON v.uploader_user_id = u.user_id
        JOIN categories c ON v.category_id = c.category_id";

$stmt = $conn->query($sql);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<div class="video-grid">

    <?php foreach ($videos as $video): ?>
        <?php
        $rawTimestamp = $video['UPLOAD_TIME'];// pl. "2025-04-22 16:38:57.123456"
        $datetime = new DateTime($rawTimestamp);
        ?>
        <?php if($video["CATEGORY_NAME"] == "Tech"): ?>
            <div class="video-card">
                <a href="index.php?page=watch&id=<?= $video['VIDEO_ID'] ?>" class="page-link" data-page="watch" data-id="<?= $video['VIDEO_ID'] ?>">
                    <!--<div class="thumbnail">
                    <img src="images/placeholder.jpg" alt="Video thumbnail">
                    <div class="video-duration">2:30</div> ezt később ki lehet számolni vagy lekérni is
                    </div>-->
                    <div class="video-info">
                        <h3 class="video-title"><?= htmlspecialchars($video['TITLE']) ?></h3>
                        <div class="video-meta">
                            <span class="channel-name"><?= $video['USERNAME'] ?> </span>
                            <span>• <?= number_format($video['VIEWS']) ?>  </span>
                            <span>• <?= $datetime->format('Y-m-d'); ?></span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

</div>
