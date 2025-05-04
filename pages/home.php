<?php
require_once "includes/dbh-inc.php";
$conn = getConnection();

$userid = $_SESSION['user_id'] ?? "";
// melyik „kategória” van kiválasztva?
$category = $_GET['category'] ?? 'all';

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
?>

<div class="categories">
    <a href="index.php?page=home&category=all">
        <div class="category <?= $category==='all' ? 'active' : '' ?>">All</div>
    </a>
    <?php
    // meglévő kategóriák
    $stmt = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($categories as $cat): ?>
        <a href="index.php?page=home&category=<?= $cat['CATEGORY_ID'] ?>">
            <div class="category <?= $category===(string)$cat['CATEGORY_ID'] ? 'active' : '' ?>">
                <?= htmlspecialchars($cat['CATEGORY_NAME']) ?>
            </div>
        </a>
    <?php endforeach; ?>

</div>

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


<?php
// ha a „Lejátszási listák” van kiválasztva, akkor playlist-eket listázunk
if ($category === 'playlists'):
    // pl. csak a bejelentkezett user saját listáit vagy globálisan mindet
    $stmt = $conn->prepare("SELECT playlist_id, user_id, list_name FROM playlists WHERE user_id = :userid ORDER BY list_name ASC");
    $stmt->bindParam(':userid', $userid);
    $stmt->execute();
    $playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="playlist-grid">
        <?php if (empty($playlists)): ?>
            <p>Még nincs lejátszási listád.</p>
        <?php else: ?>
            <?php foreach ($playlists as $pl): ?>
                <div class="playlist-card">
                    <a href="index.php?page=view_playlist&id=<?= $pl['PLAYLIST_ID'] ?>">
                        <div class="playlist-name"><?= htmlspecialchars($pl['LIST_NAME']) ?></div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

<?php
// különben pedig eredeti videó-lista
else:
    // itt beilleszthető a meglévő WHERE filter, ha category != all
    $sql = "SELECT 
                v.video_id,
                v.uploader_user_id,
                v.title,
                TO_CHAR(v.upload_time, 'YYYY-MM-DD HH24:MI:SS') AS upload_time,
                v.views,
                u.username,
                (SELECT COUNT(*) FROM likes l WHERE l.video_id = v.video_id) AS like_count
            FROM videos v
            JOIN app_users u ON v.uploader_user_id = u.user_id";
    if ($category !== 'all') {
        $sql .= " WHERE v.category_id = :cat";
    }
    $sql .= " ORDER BY $orderBy";

    $stmt = $conn->prepare($sql);
    if ($category !== 'all') {
        $stmt->bindParam(':cat', $category, PDO::PARAM_INT);
    }
    $stmt->execute();
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="video-grid">
        <?php foreach ($videos as $video):
            $dt = new DateTime($video['UPLOAD_TIME']);
            ?>
            <div class="video-card">
                <a href="index.php?page=watch&id=<?= $video['VIDEO_ID'] ?>">
                    <div class="thumbnail">
                        <img src="/images/elementor-placeholder-image.jpg" alt="Video thumbnail">
                        <div class="video-duration">2:30</div>
                    </div>
                    <div class="video-info">
                        <h3 class="video-title"><?= htmlspecialchars($video['TITLE']) ?></h3>
                        <div class="video-meta">
                            <span class="channel-name"><?= htmlspecialchars($video['USERNAME']) ?></span>
                            <span>• <?= number_format($video['VIEWS']) ?></span>
                            <span>• <?= $dt->format('Y-m-d') ?></span>
                            <span>• <?= number_format($video['LIKE_COUNT']) ?> lájk</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>

