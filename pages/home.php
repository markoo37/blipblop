<h1>Kezdőlap</h1>

<div class="categories">
    <a href="index.php?page=home&category=all"><div class="category active">All</div></a>
    <?php
    require_once "includes/dbh-inc.php";
    $conn = getConnection();

    $stmt = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($categories as $category): ?>
        <a href="index.php?page=home&category=<?= $category['CATEGORY_ID'] ?>"><div class="category"><?= htmlspecialchars($category['CATEGORY_NAME']) ?></div></a>
    <?php endforeach; ?>

</div>

<div class="video-grid">
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
