<?php
require_once "includes/dbh-inc.php";
$conn = getConnection();
$userid = $_SESSION['user_id'] ?? "";

// ─────────── Új playlist létrehozás POST-kezelése ───────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_playlist'])) {
    $name = trim($_POST['list_name']);
    if ($name !== '') {
        $st = $conn->prepare(
            "INSERT INTO playlists (user_id, list_name) VALUES (:user_id, :list_name)"
        );
        $st->bindParam(':user_id', $userid, PDO::PARAM_INT);
        $st->bindParam(':list_name', $name, PDO::PARAM_STR);
        $st->execute();
        // oldal újratöltése, hogy az új lista is megjelenjen
        header("Location: /blipblop/index.php?page=playlists");
        exit;
    }
}

// ─────────── Saját lejátszási listák lekérdezése ───────────
$stmt = $conn->prepare(
    "SELECT playlist_id, user_id, list_name
       FROM playlists
      WHERE user_id = :userid
   ORDER BY list_name ASC"
);
$stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
$stmt->execute();
$playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="playlist-management">

    <!-- ─────────── Új lista űrlap ─────────── -->
    <form method="POST" class="create-playlist-form">
        <label for="list_name">Új lejátszási lista neve:</label>
        <input
                type="text"
                id="list_name"
                name="list_name"
                placeholder="Írd be a lista nevét"
                required
        >
        <button type="submit" name="create_playlist" class="btn">
            Létrehozás
        </button>
    </form>
    <h1>Lejátszási listáid:</h1>
    <!-- ─────────── Létező listák megjelenítése ─────────── -->
    <div class="playlist-grid">
        <?php if (!$playlists): ?>
            <p>Még nincs lejátszási listád.</p>
        <?php else: ?>
            <?php foreach ($playlists as $pl): ?>
                <div class="playlist-card">
                    <a href="index.php?page=view_playlist&id=<?= $pl['PLAYLIST_ID'] ?>">
                        <div class="playlist-header">
                            <svg class="playlist-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M3 6h18M3 10h18M3 14h12M3 18h12" stroke="currentColor" stroke-width="1" stroke-linecap="round"/>
                            </svg>
                            <span class="playlist-name"><?= htmlspecialchars($pl['LIST_NAME']) ?></span>
                        </div>

                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    .create-playlist-form {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        align-items: center;
    }
    .create-playlist-form input {
        flex: 1;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .create-playlist-form .btn {
        padding: 0.5rem 1rem;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .create-playlist-form .btn:hover {
        background: #218838;
    }
</style>
