document.addEventListener('DOMContentLoaded', () => {
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const navLinks = document.querySelector('.nav-links');

    hamburgerMenu.addEventListener('click', () => {
        navLinks.classList.toggle('mobile-active');
    });

    const authLink = document.getElementById('auth-link');
    const isLoggedIn = localStorage.getItem('isLoggedIn');

    if (isLoggedIn === 'true') {
        authLink.innerHTML = '<a href="logout.php">Logout</a>';
    } else {
        authLink.innerHTML = '<a href="login.php">Login</a>';
    }
});
