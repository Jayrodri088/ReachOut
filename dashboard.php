<?php
require_once 'functions/config.php';
require_once 'functions/db_connection.php';
require_once 'functions/functions.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=dashboard.php");
    exit;
}

// Fetch the current user
$currentUser = getUserById($_SESSION['user_id']);

// Fetch user-specific data (example functions)
$userVideos = getUserVideos($_SESSION['user_id'], 3); // Fetch 3 recent videos
$userProjects = getUserProjects($_SESSION['user_id'], 3); // Fetch 3 recent projects
$recentActivity = getUserRecentActivity($_SESSION['user_id'], 5); // Fetch 5 recent activities
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ReachOut World Media Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="styles/dashboard.css">
    <!-- <link rel="stylesheet" href="styles/index.css"> -->
</head>
<body>
    <!-- Header/Navigation -->
    <?php require_once 'includes/header.php'; ?>

    <!-- Main Wrapper for Sticky Footer -->
    <div class="d-flex flex-column min-vh-100">
        <!-- Dashboard Section -->
        <section class="py-5 flex-grow-1">
            <div class="container login-container">
                <div class="row">
                    <!-- Left Sidebar (User Profile) -->
                    <div class="col-md-3 mb-4">
                        <div class="shadow-hover" style="padding: 20px; background-color: #fff; border-radius: 10px;">
                            <div class="text-center mb-4">
                                <img src="<?php echo htmlspecialchars($currentUser['avatar'] ?? 'assets/images/default-avatar.png'); ?>" alt="User Avatar" class="rounded-circle mb-2" style="width: 100px; height: 100px;">
                                <p class="text-muted"><?php echo htmlspecialchars($currentUser['email']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="col-md-9">
                        <!-- Welcome Panel -->
                        <div class="dashboard-welcome fade-in">
                            <h3 class="mb-3">Here's a quick overview of your activities on ReachOut World Media Network.</h3>
                            <a href="#recent-activity" class="btn btn-light btn-sm btn-rounded">View Recent Activity</a>
                        </div>

                        <!-- Stats Overview -->
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="stats-card fade-in">
                                    <i class="fas fa-video"></i>
                                    <h4><?php echo count($userVideos); ?> Videos</h4>
                                    <p>Total videos uploaded</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="stats-card fade-in">
                                    <i class="fas fa-users"></i>
                                    <h4><?php echo count($userProjects); ?> Projects</h4>
                                    <p>Active projects</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="stats-card fade-in">
                                    <i class="fas fa-eye"></i>
                                    <h4><?php echo number_format($currentUser['total_views'] ?? 0); ?> Views</h4>
                                    <p>Total views on your content</p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="mb-4">
                            <h4 class="section-title">Quick Actions</h4>
                            <div class="quick-actions">
                                <a href="upload-video.php" class="btn btn-light btn-rounded">Upload Video</a>
                                <a href="create-project.php" class="btn btn-light btn-rounded">Create Project</a>
                                <a href="events.php" class="btn btn-light btn-rounded">Join Event</a>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="mb-4" id="recent-activity">
                            <h4 class="section-title">Recent Activity</h4>
                            <div class="video-comments-preview">
                                <?php if (empty($recentActivity)): ?>
                                    <p class="no-comments">No recent activity to display.</p>
                                <?php else: ?>
                                    <?php foreach ($recentActivity as $activity): ?>
                                        <p class="recent-comment">
                                            <strong><?php echo htmlspecialchars($activity['action']); ?>:</strong>
                                            <?php echo htmlspecialchars($activity['description']); ?>
                                            <small class="text-muted"><?php echo date('M d, Y h:i A', strtotime($activity['created_at'])); ?></small>
                                        </p>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- My Recent Videos -->
                        <div class="mb-4">
                            <h4 class="section-title">My Recent Videos</h4>
                            <div class="row">
                                <?php if (empty($userVideos)): ?>
                                    <p class="no-comments">You haven’t uploaded any videos yet. <a href="upload-video.php" class="text-primary">Upload your first video!</a></p>
                                <?php else: ?>
                                    <?php foreach ($userVideos as $video): ?>
                                        <div class="col-md-12 mb-4">
                                            <div class="video-card shadow-hover fade-in">
                                                <div class="video-thumbnail hover-zoom">
                                                    <img src="<?php echo htmlspecialchars($video['thumbnail_url']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>" class="img-fluid rounded">
                                                    <span class="video-duration"><?php echo htmlspecialchars($video['duration']); ?></span>
                                                    <a href="video.php?id=<?php echo $video['id']; ?>" class="video-play-button">
                                                        <i class="fas fa-play"></i>
                                                    </a>
                                                </div>
                                                <div class="video-info p-3">
                                                    <h4><a href="video.php?id=<?php echo $video['id']; ?>" class="text-dark text-decoration-none"><?php echo htmlspecialchars($video['title']); ?></a></h4>
                                                    <p class="video-meta">
                                                        <span><i class="fas fa-eye"></i> <?php echo number_format($video['views']); ?></span>
                                                        <span><i class="fas fa-comment"></i> <?php echo number_format($video['comment_count']); ?></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- My Projects -->
                        <div>
                            <h4 class="section-title">My Projects</h4>
                            <div class="row">
                                <?php if (empty($userProjects)): ?>
                                    <p class="no-comments">You aren’t part of any projects yet. <a href="create-project.php" class="text-primary">Create a project!</a></p>
                                <?php else: ?>
                                    <?php foreach ($userProjects as $project): ?>
                                        <div class="col-md-12 mb-4">
                                            <div class="event-card shadow-hover fade-in">
                                                <div class="event-date">
                                                    <span class="event-day"><?php echo date('d', strtotime($project['created_at'])); ?></span>
                                                    <span class="event-month"><?php echo date('M', strtotime($project['created_at'])); ?></span>
                                                </div>
                                                <div class="event-details p-3">
                                                    <h4 class="event-title"><?php echo htmlspecialchars($project['title']); ?></h4>
                                                    <p class="event-description"><?php echo htmlspecialchars(substr($project['description'], 0, 100)); ?>...</p>
                                                    <a href="project.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-outline-primary btn-rounded">View Project</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <?php require_once 'includes/footer.php'; ?>
    </div>

    <!-- JavaScript Dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            function revealOnScroll() {
                $('.fade-in').each(function() {
                    const elementTop = $(this).offset().top;
                    const windowBottom = $(window).scrollTop() + $(window).height();
                    if (elementTop < windowBottom - 50) {
                        $(this).addClass('slide-up');
                    }
                });
            }

            $(window).on('scroll', revealOnScroll);
            revealOnScroll(); // Trigger on page load
        });
    </script>
</body>
</html>