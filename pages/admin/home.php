<?php

//echo 'This is the admin home page.';
require_once 'includes/dbh-inc.php';
$conn = getConnection();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['type'], $_POST['id'])) {
    $action = $_POST['action'];
    $type   = $_POST['type'];
    $id     = (int)$_POST['id'];

    if ($action === 'delete') {
        if ($type === 'user') {
            $stmt = $conn->prepare('DELETE FROM app_users WHERE user_id = :id');
        } elseif ($type === 'video') {
            $stmt = $conn->prepare('DELETE FROM videos WHERE video_id = :id');
        }
        $stmt->execute([':id' => $id]);
        header("Location: /blipblop/index.php?page=home");
        exit;
    }
}
// Fetch all users
$userStmt = $conn->prepare('SELECT user_id, username, ut.type_name FROM app_users a JOIN USER_TYPES ut ON a.type_id = ut.type_id WHERE a.user_id <> :id');
$userStmt->execute([':id' => $_SESSION['user_id']]);
$users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all videos
$videosql = "SELECT v.video_id, a.username, v.title, TO_CHAR(v.upload_time, 'YYYY-MM-DD HH24:MI:SS') AS upload_time, v.views FROM videos v JOIN app_users a ON a.user_id = v.uploader_user_id";
$videoStmt = $conn->query($videosql);
$videos = $videoStmt->fetchAll(PDO::FETCH_ASSOC);
?>


<div class="container">
    <h1>Admin Panel</h1>

    <section>
        <h2>Felhasználók</h2>
        <table>
            <thead>
            <tr>
                <th>USERID</th>
                <th>Felhasználónév</th>
                <th>Fiók típus</th>
                <th>Műveletek</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['USER_ID']) ?></td>
                    <td><?= htmlspecialchars($user['USERNAME']) ?></td>
                    <td><?= htmlspecialchars($user['TYPE_NAME']) ?></td>
                    <td class="actions">
                        <a href="index.php?page=edituser&id=<?= $user['USER_ID'] ?>" class="btn">Szerkesztés</a>
                        <form method="POST" onsubmit="return confirm('Biztos törölni akarod?');">
                            <input type="hidden" name="type" value="user">
                            <input type="hidden" name="id" value="<?= $user['USER_ID'] ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn">Törlés</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section>
        <h2>Videók</h2>
            <table>
                <thead>
                <tr>
                    <th>VIDEOID</th>
                    <th>Feltöltő</th>
                    <th>Cím</th>
                    <th>Feltöltve</th>
                    <th>Megtekintések</th>
                    <th>Műveletek</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($videos as $video): ?>
                    <?php
                    $rawTimestamp = $video['UPLOAD_TIME'];// pl. "2025-04-22 16:38:57.123456"
                    $datetime = new DateTime($rawTimestamp);
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($video['VIDEO_ID']) ?></td>
                        <td><?= htmlspecialchars($video['USERNAME']) ?></td>
                        <td><?= htmlspecialchars($video['TITLE']) ?></td>
                        <td><?= $datetime->format('Y-m-d h:m'); ?></td>
                        <td><?= htmlspecialchars($video['VIEWS']) ?></td>
                        <td class="actions">
                            <a href="index.php?page=editvideo&id=<?= $video['VIDEO_ID'] ?>" class="btn">Szerkesztés</a>
                            <form method="POST" onsubmit="return confirm('Biztos törölni akarod?');">
                                <input type="hidden" name="type" value="video">
                                <input type="hidden" name="id" value="<?= $video['VIDEO_ID'] ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn">Törlés</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
    </section>
</div>
