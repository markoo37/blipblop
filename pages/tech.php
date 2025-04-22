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

<div class="video-grid">
    <div class="video-card">
        <a href="index.php?page=watch&id=41" class="page-link" data-page="watch" data-id="41">
            <div class="thumbnail">
                <img src="images/placeholder-41.jpg" alt="Video thumbnail">
                <div class="video-duration">9:15</div>
            </div>
            <div class="video-info">
                <h3 class="video-title">Az 5 legjobb telefon 2025-ben</h3>
                <div class="video-meta">
                    <span class="channel-name">TechTalk</span>
                    <span>• 102K views</span>
                    <span>• 2 napja</span>
                </div>
            </div>
        </a>
    </div>
    <div class="video-card">
        <a href="index.php?page=watch&id=42" class="page-link" data-page="watch" data-id="42">
            <div class="thumbnail">
                <img src="images/placeholder-42.jpg" alt="Video thumbnail">
                <div class="video-duration">12:00</div>
            </div>
            <div class="video-info">
                <h3 class="video-title">Mesterséges intelligencia: Mi jön ezután?</h3>
                <div class="video-meta">
                    <span class="channel-name">FutureTech</span>
                    <span>• 74K views</span>
                    <span>• 5 napja</span>
                </div>
            </div>
        </a>
    </div>
    <div class="video-card">
        <a href="index.php?page=watch&id=43" class="page-link" data-page="watch" data-id="43">
            <div class="thumbnail">
                <img src="images/placeholder-43.jpg" alt="Video thumbnail">
                <div class="video-duration">4:05</div>
            </div>
            <div class="video-info">
                <h3 class="video-title">Laptop ajánló egyetemistáknak</h3>
                <div class="video-meta">
                    <span class="channel-name">PCWorld</span>
                    <span>• 19K views</span>
                    <span>• 1 napja</span>
                </div>
            </div>
        </a>
    </div>

    <!-- More videos would go here -->
</div>
