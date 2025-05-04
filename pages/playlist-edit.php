<?php
require_once 'includes/dbh-inc.php';
$conn = getConnection();

// Lekérjük a playlist ID-t és a bejelentkezett user ID-t
$playlist_id = $_GET['id'] ?? null;
$user_id     = $_SESSION['user_id'] ?? null;

// Jogosultság ellenőrzése: csak saját listákat nézhet meg
$stmt = $conn->prepare(
    "SELECT list_name FROM playlists WHERE playlist_id = :playlist_id AND user_id = :user_id"
);
$stmt->bindParam(':playlist_id', $playlist_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $user_id,     PDO::PARAM_INT);
$stmt->execute();
$list_name = $stmt->fetchColumn();
if (!$list_name) {
    die('Nincs jogosultságod ehhez a lejátszási listához.');
}

// POST-kezelés: új videó hozzáadása
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ─────────────── Lejátszási lista törlése ───────────────
    if (isset($_POST['delete_playlist'])) {
        // 1) Minden kapcsolt videó törlése
        $delVideos = $conn->prepare(
            "DELETE FROM video_playlist_con WHERE playlist_id = :playlist_id"
        );
        $delVideos->bindParam(':playlist_id', $playlist_id, PDO::PARAM_INT);
        $delVideos->execute();

        // 2) Maga a playlist törlése (csak a sajátodat!)
        $delList = $conn->prepare(
            "DELETE FROM playlists WHERE playlist_id = :playlist_id AND user_id = :user_id"
        );
        $delList->bindParam(':playlist_id', $playlist_id, PDO::PARAM_INT);
        $delList->bindParam(':user_id',     $user_id,     PDO::PARAM_INT);
        $delList->execute();

        // 3) Visszairányítás a saját listák oldalára
        header("Location: /blipblop/index.php?page=playlists");
        exit;
    }

    // Videó eltávolítása a listából
    if (isset($_POST['remove_video'])) {
        $video_to_remove = (int)$_POST['remove_video'];
        $del = $conn->prepare(
            "DELETE FROM video_playlist_con WHERE playlist_id = :playlist_id AND video_id = :video_id"
        );
        $del->bindParam(':playlist_id', $playlist_id,      PDO::PARAM_INT);
        $del->bindParam(':video_id',    $video_to_remove, PDO::PARAM_INT);
        $del->execute();
        header("Location: /blipblop/index.php?page=view_playlist&id={$playlist_id}");
        exit;
    }
    // Új videó hozzáadása
    if (isset($_POST['add_video']) && !empty($_POST['video_id'])) {
        $video_id_to_add = (int)$_POST['video_id'];
        // Ellenőrizzük, hogy még nincs-e benne
        $check = $conn->prepare(
            "SELECT COUNT(*) FROM video_playlist_con WHERE playlist_id = :playlist_id AND video_id = :video_id"
        );
        $check->bindParam(':playlist_id', $playlist_id,       PDO::PARAM_INT);
        $check->bindParam(':video_id',    $video_id_to_add,   PDO::PARAM_INT);
        $check->execute();
        if ((int)$check->fetchColumn() === 0) {
            $insert = $conn->prepare(
                "INSERT INTO video_playlist_con (playlist_id, video_id) VALUES (:playlist_id, :video_id)"
            );
            $insert->bindParam(':playlist_id', $playlist_id,     PDO::PARAM_INT);
            $insert->bindParam(':video_id',    $video_id_to_add, PDO::PARAM_INT);
            $insert->execute();
        }
        header("Location: /blipblop/index.php?page=view_playlist&id={$playlist_id}");
        exit;
    }
}

// A playlist videóinak lekérése
$stmtVideos = $conn->prepare(
    "SELECT v.video_id, v.title, v.thumbnail_path, v.duration_secs, TO_CHAR(v.upload_time, 'YYYY-MM-DD HH24:MI:SS') AS upload_time,
       v.views, u.username, (SELECT COUNT(*) FROM likes l WHERE l.video_id = v.video_id) AS like_count
     FROM videos v
     JOIN video_playlist_con vpc ON v.video_id = vpc.video_id
     JOIN app_users u ON v.uploader_user_id = u.user_id
     WHERE vpc.playlist_id = :playlist_id
     ORDER BY v.upload_time DESC"
);
$stmtVideos->bindParam(':playlist_id', $playlist_id, PDO::PARAM_INT);
$stmtVideos->execute();
$videos = $stmtVideos->fetchAll(PDO::FETCH_ASSOC);

// Elérhető videók a hozzáadáshoz (saját videók, amik még nincsenek benne)
$stmtAvail = $conn->prepare(
    "SELECT video_id, title
     FROM videos
       WHERE video_id NOT IN (
           SELECT video_id FROM video_playlist_con WHERE playlist_id = :playlist_id
       )
     ORDER BY title ASC"
);
$stmtAvail->bindParam(':playlist_id', $playlist_id, PDO::PARAM_INT);
$stmtAvail->execute();
$availableVideos = $stmtAvail->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container playlist-view">
    <h1><?= htmlspecialchars($list_name) ?></h1>

    <?php if (empty($videos)): ?>
        <p>Ez a lejátszási lista jelenleg üres.</p>
    <?php else: ?>
        <div class="video-grid">
            <?php foreach ($videos as $video):
                $dt = new DateTime($video['UPLOAD_TIME']);
                $secs = $video['DURATION_SECS'];
                $m = floor($secs / 60);
                $s = $secs % 60;
                $formatted = sprintf('%d:%02d', $m, $s);
                ?>
                <div class="video-card">
                    <a href="index.php?page=watch&id=<?= $video['VIDEO_ID'] ?>">
                        <div class="thumbnail">
                            <img src="<?= htmlspecialchars($video['THUMBNAIL_PATH'] ?: "/images/elementor-placeholder-image.jpg") ?>"   alt="Video thumbnail">
                            <div class="video-duration"><?= htmlspecialchars($formatted) ?></div>
                        </div>
                        <div class="video-info">
                            <h3 class="video-title"><?= htmlspecialchars($video['TITLE']) ?></h3>
                            <div class="video-meta">
                                <span class="channel-name"><?= htmlspecialchars($video['USERNAME']) ?></span>
                                <span>• <?= number_format($video['VIEWS']) ?></span>
                                <span>• <?= $dt->format('Y-m-d') ?></span>
                            </div>
                        </div>
                    </a>
                    <!-- Törlés gomb -->
                    <form method="POST" class="remove-video-form">
                        <input type="hidden" name="remove_video" value="<?= $video['VIDEO_ID'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger" title="Eltávolítás">
                            ✖
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <section class="playlist-add-video">
        <h2>Videó hozzáadása a listához</h2>
        <?php if (empty($availableVideos)): ?>
            <p>Nincsenek olyan videók, amelyeket hozzáadhatnál.</p>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label for="video_id">Válassz videót:</label>
                    <select name="video_id" id="video_id" required>
                        <?php foreach ($availableVideos as $av): ?>
                            <option value="<?= $av['VIDEO_ID'] ?>"><?= htmlspecialchars($av['TITLE']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="add_video" class="gr-btn btn">Hozzáad</button>
            </form>
        <?php endif; ?>
    </section>
    <!-- ───────── Törlés gomb ───────── -->
    <form method="POST" class="delete-playlist-form" onsubmit="return confirm('Biztosan törölni akarod ezt a lejátszási listát?');">
        <button
            type="submit"
            name="delete_playlist"
            class="btn btn-danger"
            style="margin-bottom:1rem;"
        >
            Lejátszási lista törlése
        </button>
    </form>
</div>

<style>
    .container.playlist-view { padding: 1rem; }
    .video-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1rem;
        margin: 1.5rem 0;
    }
    .video-card {
        position: relative;
    }
    .remove-video-form {
        position: absolute;
        top: 8px;
        right: 8px;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
        line-height: 1;
    }

    .gr-btn{
        background: #2e7d32;
    }

    .btn-danger:hover {
        background: #c82333;
    }
    .playlist-add-video { margin-top: 2rem; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; }
    .form-group select { width: 100%; padding: 0.5rem; }
    .btn:hover { background: black; }

    .delete-playlist-form {
        margin-top: 2rem;   /* tetszés szerint állítsd 1rem–3rem között */
    }
</style>
