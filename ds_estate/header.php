<header>
    <nav>
        <div class="logo">DS Estate</div>
        <ul class="nav-links">
            <li><a href="feed.php">Feed</a></li>
            <li><a href="create_listing.php">Create Listing</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
        <div class="hamburger-menu">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </nav>
</header>

