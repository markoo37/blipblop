<h1>Podcastok</h1>

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

<div class="video-grid">
    <div class="video-card">
        <a href="index.php?page=watch&id=51" class="page-link" data-page="watch" data-id="51">
            <div class="thumbnail">
                <img src="images/placeholder-51.jpg" alt="Video thumbnail">
                <div class="video-duration">30:00</div>
            </div>
            <div class="video-info">
                <h3 class="video-title">TechTalk Podcast – AI jövője</h3>
                <div class="video-meta">
                    <span class="channel-name">PodZone</span>
                    <span>• 9K views</span>
                    <span>• 3 napja</span>
                </div>
            </div>
        </a>
    </div>
    <div class="video-card">
        <a href="index.php?page=watch&id=52" class="page-link" data-page="watch" data-id=52>
            <div class="thumbnail">
                <img src="images/placeholder-52.jpg" alt="Video thumbnail">
                <div class="video-duration">42:17</div>
            </div>
            <div class="video-info">
                <h3 class="video-title">Vállalkozás a nulláról – Siker sztorik</h3>
                <div class="video-meta">
                    <span class="channel-name">BizCast</span>
                    <span>• 15K views</span>
                    <span>• 1 hete</span>
                </div>
            </div>
        </a>
    </div>

    <!-- More videos would go here -->
</div>
