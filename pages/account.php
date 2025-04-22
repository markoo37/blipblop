<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /untitled/index.php?page=login");
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// In a real application, you would fetch user details from database
// For simplicity, we'll use session data
?>

<div class="account-container">
    <h1>My Account</h1>

    <div class="user-profile">
        <div class="profile-header">
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($username); ?></h2>
            </div>
        </div>

        <div class="account-tabs">
            <!--<ul class="tab-links">
                <li class="active"><a href="#my-videos">My Videos</a></li>
                <li><a href="#liked-videos">Liked Videos</a></li>
                <li><a href="#settings">Account Settings</a></li>
            </ul>-->

            <div class="tab-content">
                <div id="my-videos" class="tab-pane active">
                    <div class="section-header">
                        <h3>My Videos</h3>
                        <a href="index.php?page=upload" class="btn page-link" data-page="upload">Upload New Video</a>
                    </div>

                    <?php if (true): // Replace with actual check for user videos ?>
                        <p class="no-content">You haven't uploaded any videos yet.</p>
                    <?php else: ?>
                        <div class="video-grid">
                            <!-- User videos would go here -->
                        </div>
                    <?php endif; ?>
                </div>

                <div id="liked-videos" class="tab-pane">
                    <h3>Liked Videos</h3>

                    <?php if (true): // Replace with actual check for liked videos ?>
                        <p class="no-content">You haven't liked any videos yet.</p>
                    <?php else: ?>
                        <div class="video-grid">
                            <!-- Liked videos would go here -->
                        </div>
                    <?php endif; ?>
                </div>

                <div id="settings" class="tab-pane">
                    <h3>Account Settings</h3>

                    <form action="process/update_account.php" method="post" class="settings-form">
                        <div class="form-group">
                            <label for="update_username">Username</label>
                            <input type="text" id="update_username" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" id="new_password" name="new_password" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="confirm_new_password">Confirm New Password</label>
                                <input type="password" id="confirm_new_password" name="confirm_new_password" class="form-control">
                            </div>
                        </div>

                        <button type="submit" class="btn">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .account-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .user-profile {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .profile-header {
        display: flex;
        align-items: center;
        padding: 20px;
        background: #f5f5f5;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 20px;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-info h2 {
        margin-bottom: 5px;
    }

    .account-tabs {
        padding: 20px;
    }

    .tab-links {
        display: flex;
        list-style: none;
        border-bottom: