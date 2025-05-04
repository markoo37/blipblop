<?php
require_once 'includes/dbh-inc.php';
$conn = getConnection();

// Csak bejelentkezett felhasználó tölthet fel
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    // 1) Validáld az inputot
    if (empty($_FILES['video_file']) || $_FILES['video_file']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Hiba a fájl feltöltésekor.';
    } else {
        $file = $_FILES['video_file'];
        // 2) Ellenőrizd a MIME-t és a méretet
        $allowed = ['video/mp4','video/webm','video/ogg', 'video/mkv'];
        if (!in_array($file['type'], $allowed)) {
            $errors[] = 'Csak MP4, WEBM vagy OGG videók engedélyezettek.';
        }
        if ($file['size'] > 100 * 1024 * 1024) {
            $errors[] = 'A fájl túl nagy (max 100MB).';
        }
    }

    if (!$errors) {
        // 3) Fájlnév generálás és mentés
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = bin2hex(random_bytes(16)) . ".$ext";
        $dest = __DIR__ . '/../uploads/videos/' . $newName;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            $errors[] = 'A fájl mentése sikertelen.';
        } else {

            $cmd = sprintf(
                'ffprobe -v error -select_streams v:0 -show_entries stream=duration '.
                '-of default=nw=1:nk=1 %s',
                escapeshellarg($dest)
            );
            $duration = trim(shell_exec($cmd));    // pl. "123.456789"

            $durationSecs = (int)round(floatval($duration));

            // 5) DB-be beszúrás
            $stmt = $conn->prepare("
                INSERT INTO videos 
                  (uploader_user_id, title, upload_time, views, category_id, file_path, duration_secs)
                VALUES
                  (:user_id, :title, SYSTIMESTAMP, 0, :cat, :path, :dur)
            ");
            $stmt->execute([
                ':user_id'   => $_SESSION['user_id'],
                ':title' => $_POST['title'],
                ':cat'   => $_POST['category_id'],
                ':path'  => "uploads/videos/$newName",
                ':dur'  => $durationSecs
            ]);

            $stmt = $conn->prepare("SELECT video_id FROM videos WHERE file_path = :file_path");
            $ppp = "uploads/videos/$newName";
            $stmt->bindParam(":file_path", $ppp);
            $stmt->execute();
            $video_temp = $stmt->fetch(PDO::FETCH_ASSOC);

            $baseName = pathinfo($newName, PATHINFO_FILENAME);
            $thumbName = $baseName . '.jpg';
            $thumbDir  = __DIR__ . '/../uploads/thumbnails/';
            $thumbPath = $thumbDir . $thumbName;

            if (!is_dir($thumbDir)) {
                mkdir($thumbDir, 0755, true);
            }

            $cmd = sprintf(
                'ffmpeg -i %s -ss 00:00:02 -vframes 1 %s',
                escapeshellarg($dest),
                escapeshellarg($thumbPath)
            );
            exec($cmd, $output, $returnVar);
            if ($returnVar !== 0) {
                // hiba kezelése, de ne akadjon meg a feltöltés
            }

            $stmt = $conn->prepare("UPDATE videos SET thumbnail_path = :thumbnail_path WHERE video_id = :video_id");
            $tp = "uploads/thumbnails/$thumbName";
            $stmt->bindParam(':thumbnail_path', $tp);
            $last = $video_temp['VIDEO_ID'];
            $stmt->bindParam(':video_id', $last);
            $stmt->execute();

            header('Location: index.php?page=home');
            exit;
        }
    }
}
?>

<div class="container upload-video">
    <h2>Videó feltöltése</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="form-upload">
        <div class="form-group">
            <label for="title">Videó címe:</label>
            <input
                    type="text"
                    id="title"
                    name="title"
                    class="form-control"
                    placeholder="Add meg a videó címét"
                    required
            >
        </div>

        <div class="form-group">
            <label for="category_id">Kategória:</label>
            <select
                    id="category_id"
                    name="category_id"
                    class="form-control"
                    required
            >
                <?php
                $cats = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name")->fetchAll();
                foreach ($cats as $c): ?>
                    <option value="<?= $c['CATEGORY_ID'] ?>">
                        <?= htmlspecialchars($c['CATEGORY_NAME']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="video_file">Fájl:</label>
            <input
                    type="file"
                    id="video_file"
                    name="video_file"
                    class="form-control-file"
                    accept="video/*"
                    required
            >
        </div>

        <button type="submit" class="btn btn-primary">
            Feltöltés
        </button>
    </form>
</div>

<style>
    .upload-video {
        max-width: 600px;
        margin: 2rem auto;
        background: #fff;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    .upload-video h2 {
        margin-bottom: 1rem;
        font-size: 1.5rem;
        color: #333;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .form-control, .form-control-file {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .form-control-file {
        border: none;
        padding: 0.25rem 0;
    }
    .btn-primary {
        background: #007bff;
        border: none;
        padding: 0.5rem 1rem;
        color: #fff;
        border-radius: 4px;
        cursor: pointer;
    }
    .btn-primary:hover {
        background: #0056b3;
    }
    .alert {
        background: #f8d7da;
        color: #721c24;
        border-radius: 4px;
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
    }
    .alert ul {
        margin: 0;
        padding-left: 1.25rem;
    }
</style>
