<?php
require_once 'functions/config.php';
require_once 'functions/db_connection.php';
require_once 'functions/functions.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$currentUser = $isLoggedIn ? getUserById($_SESSION['user_id']) : null;

// Fetch featured videos for homepage
$featuredVideos = getFeaturedVideos(6); // Get 6 featured videos

// Fetch upcoming events
$upcomingEvents = getUpcomingEvents(3); // Get 3 upcoming events
// Debug: Check if events are being fetched
if (empty($upcomingEvents)) {
    echo "<!-- Debug: No upcoming events found. -->";
} else {
    echo "<!-- Debug: Upcoming events found: " . count($upcomingEvents) . " -->";
}

// Fetch gallery images
$galleryImages = getGalleryImages(8); // Get 8 gallery images
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReachOut World Media Network - Connect, Create, Collaborate</title>
    <link rel="stylesheet" href="styles/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <!-- Header/Navigation -->
    <?php require_once 'includes/header.php'; ?>

    <!-- Main Banner Section -->
    <section class="banner-section">
        <div class="container login-container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="banner-title fade-in">ReachOut World Media Network</h1>
                    <p class="banner-subtitle fade-in" style="animation-delay: 0.3s;">Empowering Media Professionals to Connect, Create, and Collaborate</p>
                    <p class="banner-text fade-in" style="animation-delay: 0.6s;">Join our global network of media professionals creating impactful content that reaches the world.</p>
                    <div class="banner-cta fade-in" style="animation-delay: 0.9s;">
                        <?php if (!$isLoggedIn): ?>
                            <a href="signup.php" class="btn btn-primary btn-lg btn-rounded me-3">Join the Network</a>
                            <a href="about.php" class="btn btn-outline-primary btn-lg btn-rounded">Learn More</a>
                        <?php else: ?>
                            <a href="dashboard.php" class="btn btn-primary btn-lg btn-rounded me-3">My Dashboard</a>
                            <a href="projects.php" class="btn btn-primary btn-lg btn-rounded">Explore Projects</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="banner-image hover-zoom">
                        <img src="assets/images/banner-image.jpg" alt="ReachOut World Media Network" class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Network Section -->
    <section class="about-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="info-card">
                        <h3><i class="fas fa-globe"></i> ReachOut World</h3>
                        <p>A global initiative dedicated to sharing impactful media content through various channels and platforms.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card">
                        <h3><i class="fas fa-network-wired"></i> The Network</h3>
                        <p>A community of media professionals, creators, and enthusiasts collaborating to produce high-quality content.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card">
                        <h3><i class="fas fa-bullseye"></i> Key Objective</h3>
                        <p>To create and distribute transformative media content that reaches and impacts audiences worldwide.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Eligibility Section -->
    <section class="eligibility-section py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center mb-4">Who Can Join Our Network?</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="eligibility-card">
                        <div class="icon-wrapper"><i class="fas fa-video"></i></div>
                        <h4>Videographers</h4>
                        <p>Professional and aspiring video content creators with a passion for storytelling.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="eligibility-card">
                        <div class="icon-wrapper"><i class="fas fa-pen-fancy"></i></div>
                        <h4>Content Writers</h4>
                        <p>Skilled writers who can create compelling scripts, articles, and media content.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="eligibility-card">
                        <div class="icon-wrapper"><i class="fas fa-photo-video"></i></div>
                        <h4>Editors</h4>
                        <p>Video and content editors who can refine and perfect media productions.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="eligibility-card">
                        <div class="icon-wrapper"><i class="fas fa-paint-brush"></i></div>
                        <h4>Graphic Designers</h4>
                        <p>Creative designers who can produce visually stunning graphics and media assets.</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="eligibility.php" class="btn btn-outline-primary">View All Eligible Roles</a>
            </div>
        </div>
    </section>

    <!-- Live Stream Section -->
    <section class="livestream-section py-5">
        <div class="container login-container">
            <h2 class="section-title fade-in">Rhapsody TV Live</h2>
            <div class="row">
                <div class="col-12">
                    <div class="video-teaser-container">
                        <a href="https://rhapsodytv.live/" target="_blank">
                            <video autoplay muted loop class="img-fluid rounded shadow" style="width: 100%;">
                                <source src="assets/images/videos/rhapsody-tv-teaser.mp4" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            <div class="play-overlay">
                                <i class="fas fa-play fa-3x" style="color: #fff;"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Videos Section -->
    <section class="featured-videos-section py-5 bg-light">
        <div class="container">
            <h2 class="section-title">Featured Videos</h2>
            <div class="row">
                <?php foreach ($featuredVideos as $video): ?>
                    <div class="col-md-4 mb-4">
                        <div class="video-card">
                            <div class="video-thumbnail">
                                <img src="<?php echo htmlspecialchars($video['thumbnail_url']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>" class="img-fluid rounded">
                                <span class="video-duration"><?php echo htmlspecialchars($video['duration']); ?></span>
                                <a href="video.php?id=<?php echo $video['id']; ?>" class="video-play-button">
                                    <i class="fas fa-play"></i>
                                </a>
                            </div>
                            <div class="video-info">
                                <h4><a href="video.php?id=<?php echo $video['id']; ?>"><?php echo htmlspecialchars($video['title']); ?></a></h4>
                                <p class="video-meta">
                                    <span><i class="fas fa-eye"></i> <?php echo number_format($video['views']); ?></span>
                                    <span><i class="fas fa-comment"></i> <?php echo number_format($video['comment_count']); ?></span>
                                </p>
                                <div class="video-comments-preview">
                                    <?php if (count($video['recent_comments']) > 0): ?>
                                        <p class="recent-comment">
                                            <strong><?php echo htmlspecialchars($video['recent_comments'][0]['username']); ?>:</strong>
                                            <?php echo htmlspecialchars(substr($video['recent_comments'][0]['comment'], 0, 60)); ?>...
                                        </p>
                                    <?php else: ?>
                                        <p class="no-comments">Be the first to comment</p>
                                    <?php endif; ?>
                                    <a href="video.php?id=<?php echo $video['id']; ?>#comments" class="view-comments">View all comments</a>
                                </div>
                                <?php if (!$isLoggedIn): ?>
                                    <div class="comment-login-prompt">
                                        <a href="login.php?redirect=video.php?id=<?php echo $video['id']; ?>">Login to comment</a>
                                    </div>
                                <?php else: ?>
                                    <form class="quick-comment-form" action="process_comment.php" method="post">
                                        <input type="hidden" name="video_id" value="<?php echo $video['id']; ?>">
                                        <div class="input-group">
                                            <input type="text" name="comment" class="form-control form-control-sm" placeholder="Add a comment...">
                                            <button type="submit" class="btn btn-sm btn-primary">Post</button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="videos.php" class="btn btn-primary">View All Videos</a>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery-section py-5">
        <div class="container">
            <h2 class="section-title">Production Gallery</h2>
            <div class="row gallery-grid">
                <?php foreach ($galleryImages as $image): ?>
                    <div class="col-md-3 col-6 mb-4">
                        <div class="gallery-item">
                            <a href="gallery.php?id=<?php echo $image['id']; ?>" class="gallery-link">
                                <img src="<?php echo htmlspecialchars($image['thumbnail_url']); ?>" alt="<?php echo htmlspecialchars($image['title']); ?>" class="img-fluid rounded">
                                <div class="gallery-overlay">
                                    <div class="gallery-caption"><?php echo htmlspecialchars($image['title']); ?></div>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="gallery.php" class="btn btn-outline-primary">Explore Full Gallery</a>
            </div>
        </div>
    </section>

    <!-- Upcoming Events Section -->
    <section class="events-section py-5">
        <div class="container login-container">
            <h2 class="section-title fade-in">Upcoming Events</h2>
            <div class="row">
                <?php if (empty($upcomingEvents)): ?>
                    <div class="col-12">
                        <div class="no-events-message text-center p-5 bg-light rounded shadow-sm fade-in">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h4>No Upcoming Events</h4>
                            <p class="text-muted">There are currently no upcoming events scheduled. Check back soon for exciting updates!</p>
                            <a href="events.php" class="btn btn-outline-primary btn-rounded">View Past Events</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($upcomingEvents as $index => $event): ?>
                        <div class="col-md-4 col-12 mb-4">
                            <div class="event-card shadow-hover fade-in" style="animation-delay: <?php echo ($index % 3) * 0.3; ?>s;">
                                <div class="event-date">
                                    <span class="event-day"><?php echo date('d', strtotime($event['event_date'])); ?></span>
                                    <span class="event-month"><?php echo date('M', strtotime($event['event_date'])); ?></span>
                                </div>
                                <div class="event-details p-3">
                                    <h4 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h4>
                                    <p class="event-time"><i class="far fa-clock"></i> <?php echo date('h:i A', strtotime($event['event_time'])); ?></p>
                                    <p class="event-location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                                    <p class="event-description"><?php echo htmlspecialchars(substr($event['description'], 0, 100)); ?>...</p>
                                    <a href="event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-primary btn-rounded">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="text-center mt-4 fade-in" style="animation-delay: 1.5s;">
                <a href="events.php" class="btn btn-primary btn-rounded">View All Events</a>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="mb-4">Ready to Join the ReachOut World Media Network?</h2>
            <p class="mb-4">Connect with like-minded media professionals and contribute to impactful projects</p>
            <?php if (!$isLoggedIn): ?>
                <a href="signup.php" class="btn btn-light btn-lg me-3">Sign Up Now</a>
                <a href="login.php" class="btn btn-outline-light btn-lg">Already a Member? Log In</a>
            <?php else: ?>
                <a href="projects.php" class="btn btn-light btn-lg me-3">Explore Projects</a>
                <a href="refer.php" class="btn btn-outline-light btn-lg">Refer a Colleague</a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <?php require_once 'includes/footer.php'; ?>

    <!-- JavaScript Dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Comment form submission
        document.addEventListener('DOMContentLoaded', function() {
            $('.quick-comment-form').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const videoId = form.find('input[name="video_id"]').val();
                const comment = form.find('input[name="comment"]').val();

                if (comment.trim() === '') return;

                $.ajax({
                    url: 'ajax/post_comment.php',
                    type: 'POST',
                    data: { video_id: videoId, comment: comment },
                    success: function(response) {
                        if (response.success) {
                            form.find('input[name="comment"]').val('');
                            const videoCard = form.closest('.video-card');
                            videoCard.find('.video-comments-preview .recent-comment').html(
                                `<strong>${<?php echo json_encode($currentUser ? $currentUser['username'] : ''); ?>}:</strong> ${comment.substring(0, 60)}...`
                            );
                            const commentCountEl = videoCard.find('.video-meta .fas.fa-comment').parent();
                            const currentCount = parseInt(commentCountEl.text().replace(/,/g, ''));
                            commentCountEl.html(`<i class="fas fa-comment"></i> ${currentCount + 1}`);
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error posting comment. Please try again.',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            });

            // Add fade-in animation on scroll
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

        // Helper function for formatting numbers with commas
        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
</body>
</html>