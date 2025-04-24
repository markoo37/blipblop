<?php

require_once 'includes/dbh-inc.php';
$conn = getConnection();

$search_string = $_GET['q'];

$sql = "SELECT 
            v.video_id,
            v.uploader_user_id,
            v.title,
            TO_CHAR(v.upload_time, 'YYYY-MM-DD HH24:MI:SS') AS upload_time,
            v.views,
            u.username
        FROM videos v
        JOIN app_users u ON v.uploader_user_id = u.user_id
        WHERE LOWER(v.title) LIKE '%' || LOWER(:search_string) || '%'";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':search_string', $search_string);
$stmt->execute();
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<div class="container">
    <div class="form-group">
    <?php foreach ($videos as $video): ?>
        <?php
        $rawTimestamp = $video['UPLOAD_TIME'];// pl. "2025-04-22 16:38:57.123456"
        $datetime = new DateTime($rawTimestamp);
        ?>
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
                        <span>• <?= number_format($video['VIEWS']) ?> </span>
                        <span>• <?= $datetime->format('Y-m-d'); ?></span>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
    </div>

    <div class="form-group">
        <a href="index.php?page=home"><button type="button" class="btn">Kezdőlapra vissza</button></a>
    </div>


</div>


