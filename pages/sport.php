<h1>Sport</h1>

<div class="categories">
    <a href="index.php?page=home&category=all"><div class="category">All</div></a>
    <?php
    require_once "includes/dbh-inc.php";
    $conn = getConnection();

    $stmt = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($categories as $category): ?>
        <?php if ($category['CATEGORY_ID'] == '3'): ?>
            <a href="index.php?page=home&category=<?= $category['CATEGORY_ID'] ?>"><div class="category active"><?= htmlspecialchars($category['CATEGORY_NAME']) ?></div></a>
        <?php else: ?>
            <a href="index.php?page=home&category=<?= $category['CATEGORY_ID'] ?>"><div class="category"><?= htmlspecialchars($category['CATEGORY_NAME']) ?></div></a>
        <?php endif; ?>
    <?php endforeach; ?>

</div>

<div class="video-grid">
    <div class="video-card">
        <a href="index.php?page=watch&id=31" class="page-link" data-page="watch" data-id="31">
            <div class="thumbnail">
                <img src="images/placeholder-31.jpg" alt="Video thumbnail">
                <div class="video-duration">7:00</div>
            </div>
            <div class="video-info">
                <h3 class="video-title">Foci összefoglaló: Barca vs Real</h3>
                <div class="video-meta">
                    <span class="channel-name">SportTV</span>
                    <span>• 89K views</span>
                    <span>• 1 napja</span>
                </div>
            </div>
        </a>
    </div>
    <div class="video-card">
        <a href="index.php?page=watch&id=32" class="page-link" data-page="watch" data-id="32">
            <div class="thumbnail">
                <img src="images/placeholder-32.jpg" alt="Video thumbnail">
                <div class="video-duration">3:22</div>
            </div>
            <div class="video-info">
                <h3 class="video-title">TOP 5 gól a héten</h3>
                <div class="video-meta">
                    <span class="channel-name">GoalZone</span>
                    <span>• 25K views</span>
                    <span>• 14 órája</span>
                </div>
            </div>
        </a>
    </div>

    <!-- More videos would go here -->
</div>
