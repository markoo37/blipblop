<h1>Hírek</h1>

<div class="categories">
    <a href="index.php?page=home&category=all"><div class="category">All</div></a>
    <?php
    require_once "includes/dbh-inc.php";
    $conn = getConnection();

    $stmt = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($categories as $category): ?>
        <?php if ($category['CATEGORY_ID'] == '1'): ?>
            <a href="index.php?page=home&category=<?= $category['CATEGORY_ID'] ?>"><div class="category active"><?= htmlspecialchars($category['CATEGORY_NAME']) ?></div></a>
        <?php else: ?>
            <a href="index.php?page=home&category=<?= $category['CATEGORY_ID'] ?>"><div class="category"><?= htmlspecialchars($category['CATEGORY_NAME']) ?></div></a>
        <?php endif; ?>
    <?php endforeach; ?>

</div>

<div class="video-grid">
    <!-- Video 1 -->
    <div class="video-card">
        <a href="index.php?page=watch&id=11" class="page-link" data-page="watch" data-id="11">
            <div class="thumbnail">
                <img src="images/placeholder-11.jpg" alt="Video thumbnail">
                <div class="video-duration">2:30</div>
            </div>
            <div class="video-info">
                <h3 class="video-title">Napi hírösszefoglaló – Röviden</h3>
                <div class="video-meta">
                    <span class="channel-name">HírTV</span>
                    <span>• 12K views</span>
                    <span>• 3 órája</span>
                </div>
            </div>
        </a>
    </div>
    <div class="video-card">
        <a href="index.php?page=watch&id=12" class="page-link" data-page="watch" data-id="12">
            <div class="thumbnail">
                <img src="images/placeholder-12.jpg" alt="Video thumbnail">
                <div class="video-duration">4:10</div>
            </div>
            <div class="video-info">
                <h3 class="video-title">Politikai vitaműsor – Teljes adás</h3>
                <div class="video-meta">
                    <span class="channel-name">Esti Híradó</span>
                    <span>• 32K views</span>
                    <span>• 1 napja</span>
                </div>
            </div>
        </a>
    </div>

    <!-- More videos would go here -->
</div>
