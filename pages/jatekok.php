<h1>Játékok</h1>

<div class="categories">
    <a href="index.php?page=home&category=all"><div class="category">All</div></a>
    <?php
    require_once "includes/dbh-inc.php";
    $conn = getConnection();

    $stmt = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($categories as $category): ?>
        <?php if ($category['CATEGORY_ID'] == '2'): ?>
            <a href="index.php?page=home&category=<?= $category['CATEGORY_ID'] ?>"><div class="category active"><?= htmlspecialchars($category['CATEGORY_NAME']) ?></div></a>
        <?php else: ?>
            <a href="index.php?page=home&category=<?= $category['CATEGORY_ID'] ?>"><div class="category"><?= htmlspecialchars($category['CATEGORY_NAME']) ?></div></a>
        <?php endif; ?>
    <?php endforeach; ?>

</div>

<div class="video-grid">
    <div class="video-card">
        <a href="index.php?page=watch&id=21" class="page-link" data-page="watch" data-id="21">
            <div class="thumbnail">
                <img src="images/placeholder-21.jpg" alt="Video thumbnail">
                <div class="video-duration">8:20</div>
            </div>
            <div class="video-info">
                <h3 class="video-title">Top 10 indie játék 2025-ben</h3>
                <div class="video-meta">
                    <span class="channel-name">GameGuru</span>
                    <span>• 67K views</span>
                    <span>• 5 napja</span>
                </div>
            </div>
        </a>
    </div>
    <div class="video-card">
        <a href="index.php?page=watch&id=22" class="page-link" data-page="watch" data-id="22">
            <div class="thumbnail">
                <img src="images/placeholder-22.jpg" alt="Video thumbnail">
                <div class="video-duration">10:45</div>
            </div>
            <div class="video-info">
                <h3 class="video-title">Fortnite új szezon bemutató</h3>
                <div class="video-meta">
                    <span class="channel-name">EpicPlayer</span>
                    <span>• 105K views</span>
                    <span>• 2 napja</span>
                </div>
            </div>
        </a>
    </div>
    <div class="video-card">
        <a href="index.php?page=watch&id=23" class="page-link" data-page="watch" data-id="23">
            <div class="thumbnail">
                <img src="images/placeholder-23.jpg" alt="Video thumbnail">
                <div class="video-duration">6:50</div>
            </div>
            <div class="video-info">
                <h3 class="video-title">Hogyan nyerd meg az első ranked meccsed</h3>
                <div class="video-meta">
                    <span class="channel-name">NoobToPro</span>
                    <span>• 8K views</span>
                    <span>• 3 órája</span>
                </div>
            </div>
        </a>
    </div>

    <!-- More videos would go here -->
</div>
