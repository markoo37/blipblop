
<div class="categories">
    <a href="index.php?page=home&category=all"><div class="category">All</div></a>
    <?php
    require_once "includes/dbh-inc.php";
    $conn = getConnection();

    $stmt = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($categories as $category): ?>
        <?php if ($category['CATEGORY_ID'] == '5'): ?>
            <a href="index.php?page=home&category=<?= $category['CATEGORY_ID'] ?>"><div class="category active"><?= htmlspecialchars($category['CATEGORY_NAME']) ?></div></a>
        <?php else: ?>
            <a href="index.php?page=home&category=<?= $category['CATEGORY_ID'] ?>"><div class="category"><?= htmlspecialchars($category['CATEGORY_NAME']) ?></div></a>
        <?php endif; ?>
    <?php endforeach; ?>

</div>

<?php
require_once "includes/dbh-inc.php";
$conn = getConnection();
$category = $_GET['category'];

$allowedSorts = ['date','name','views','likes'];
$sort = $_GET['sort'] ?? 'date';
if (!in_array($sort, $allowedSorts, true)) {
    $sort = 'date';
}

$orderBy = match ($sort) {
    'name' => 'v.title ASC',
    'views' => 'v.views DESC',
    'likes' => 'like_count DESC',
    default => 'v.upload_time DESC',
};

$sql = "SELECT 
            v.video_id,
            v.uploader_user_id,
            v.title,
            v.thumbnail_path,
            TO_CHAR(v.upload_time, 'YYYY-MM-DD HH24:MI:SS') AS upload_time,
            v.views,
            u.username,
            c.category_name,
            (SELECT COUNT(*) FROM likes l WHERE l.video_id = v.video_id) AS like_count
        FROM videos v
        JOIN app_users u ON v.uploader_user_id = u.user_id
        JOIN categories c ON v.category_id = c.category_id";

$sql .= " ORDER BY $orderBy";

$stmt = $conn->query($sql);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>

<div class="filter-bar">
    <form method="GET" class="sort-form">
        <!-- Átörököljük a page és category paramétereket -->
        <input type="hidden" name="page"     value="home">
        <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">

        <label for="sort">Rendezés:</label>
        <select name="sort" id="sort" onchange="this.form.submit()">
            <option value="date"  <?= $sort==='date'  ? 'selected' : '' ?>>Legújabb</option>
            <option value="name"  <?= $sort==='name'  ? 'selected' : '' ?>>Név (A–Z)</option>
            <option value="views" <?= $sort==='views' ? 'selected' : '' ?>>Nézettség</option>
            <option value="likes" <?= $sort==='likes' ? 'selected' : '' ?>>Kedvelések</option>
        </select>
    </form>
</div>

<div class="video-grid">
    <?php if (!$videos) : ?>
        <p>Ebben a kategóriában még nincs feltöltött videó!</p>
    <?php else: ?>
    <?php foreach ($videos as $video): ?>
        <?php
        $rawTimestamp = $video['UPLOAD_TIME'];// pl. "2025-04-22 16:38:57.123456"
        $datetime = new DateTime($rawTimestamp);
        ?>
        <?php if($video["CATEGORY_NAME"] == "Podcastok"): ?>
            <div class="video-card">
                <a href="index.php?page=watch&id=<?= $video['VIDEO_ID'] ?>" class="page-link" data-page="watch" data-id="<?= $video['VIDEO_ID'] ?>">
                    <div class="thumbnail">
                        <img src="<?= htmlspecialchars($video['THUMBNAIL_PATH'] ?: "/images/elementor-placeholder-image.jpg") ?>"   alt="Video thumbnail">
                        <div class="video-duration">2:30</div>
                    </div>
                    <div class="video-info">
                        <h3 class="video-title"><?= htmlspecialchars($video['TITLE']) ?></h3>
                        <div class="video-meta">
                            <span class="channel-name"><?= $video['USERNAME'] ?> </span>
                            <span>• <?= number_format($video['VIEWS']) ?> </span>
                            <span>• <?= $datetime->format('Y-m-d'); ?></span>
                            <span>• <?= number_format($video['LIKE_COUNT']) ?> lájk</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php endif; ?>

</div>
