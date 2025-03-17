<?php
// Get current page
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!--- Header/Navigation --->
<header class="header">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="assets/images/logo.png" alt="ReachOut World Media Network" height="40" class="logo-hover">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage == 'index.php') ? 'active' : '' ?>" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage == 'about.php') ? 'active' : '' ?>" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage == 'videos.php') ? 'active' : '' ?>" href="videos.php">Videos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage == 'gallery.php') ? 'active' : '' ?>" href="gallery.php">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage == 'events.php') ? 'active' : '' ?>" href="events.php">Events</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle user-menu" href="#" role="button" data-bs-toggle="dropdown">
                                <img src="<?= !empty($currentUser['avatar']) ? htmlspecialchars($currentUser['avatar']) : 'assets/images/default-avatar.jpg' ?>"
                                    alt="<?= htmlspecialchars($currentUser['username']) ?>"
                                    class="avatar">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="projects.php"><i class="fas fa-project-diagram me-2"></i>Projects</a></li>
                                <li><a class="dropdown-item" href="refer.php"><i class="fas fa-users me-2"></i>Refer</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger logout-btn" href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?= ($currentPage == 'login.php') ? 'active' : '' ?>" href="login.php">Log In</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary glow-on-hover px-3 mx-2 <?= ($currentPage == 'signup.php') ? 'active' : '' ?>" href="signup.php">Sign Up</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <form id="logoutForm" method="post" action="logout.php" style="display: none;">
        <input type="hidden" name="confirm_logout" value="1">
    </form>
</header>

<style>
    /* Header Styles */
    .header {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        transition: all 0.3s ease;
    }

    .navbar {
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        padding: 0.2rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .navbar.scrolled {
        background: rgba(0, 0, 0, 0.95) !important;
        padding: 0.4rem 0;
    }

    .logo-hover {
        transition: transform 0.3s ease;
    }

    .logo-hover:hover {
        transform: scale(1.05);
    }

    .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
        margin: 0 0.5rem;
        padding: 0.8rem 1.2rem !important;
        border-radius: 8px;
        transition: all 0.3s ease;
        position: relative;
    }

    .nav-link.active,
    .nav-link:hover {
        color: #fff !important;
        background: rgba(255, 255, 255, 0.1);
    }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 2px;
        background: #00b4d8;
        transition: all 0.3s ease;
    }

    .nav-link.active::after,
    .nav-link:hover::after {
        width: 100%;
        left: 0;
    }

    .dropdown-menu {
        background: rgba(0, 0, 0, 0.9);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .dropdown-item {
        color: rgba(255, 255, 255, 0.8);
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
    }

    .logout-btn:hover {
        background: rgba(255, 0, 0, 0.1) !important;
    }

    .glow-on-hover {
        box-shadow: 0 0 15px rgba(0, 180, 216, 0.3);
        transition: all 0.3s ease;
    }

    .glow-on-hover:hover {
        box-shadow: 0 0 25px rgba(0, 180, 216, 0.5);
    }

    /* Add these styles to the existing header styles */
    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        margin-right: 0.5rem;
    }

    .avatar:hover {
        transform: none;
        border-color: rgba(255, 255, 255, 0.2);
    }

    /* Adjust dropdown toggle padding */
    .user-menu {
        padding: 0.25rem 0.8rem !important;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .user-menu::after {
        content: "\f107";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        border: none;
        color: rgba(255, 255, 255, 0.8);
        transition: all 0.3s ease;
        margin-left: 0;
        vertical-align: middle;
    }

    .user-menu:hover::after {
        color: #fff;
        transform: translateY(2px);
    }

    .dropdown-toggle::after {
        display: none !important;
    }

    @media (max-width: 575px){
        .navbar{
            padding: 0.1rem 0;
        }
    }
</style>

<script>
    // Scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Logout confirmation
    function confirmLogout(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Logout Confirmation',
            text: "Are you sure you want to log out?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Logout!',
            background: 'rgba(0, 0, 0, 0.9)',
            customClass: {
                confirmButton: 'btn-danger',
                cancelButton: 'btn-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logoutForm').submit();
            }
        });
    }
</script>