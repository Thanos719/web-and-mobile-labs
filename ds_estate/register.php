<?php
session_start();
require 'db.php';

$error = '';
$name = $surname = $username = $password = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    // Debugging lines
    error_log("Submitted username: " . $username);

    // Έλεγχος εγκυρότητας δεδομένων
    if (!ctype_alpha($name)) {
        $error .= 'Name must contain only letters.<br>';
    }
    if (!ctype_alpha($surname)) {
        $error .= 'Surname must contain only letters.<br>';
    }
    if (strlen($password) < 4 || strlen($password) > 10 || !preg_match('/[0-9]/', $password)) {
        $error .= 'Password must be between 4 and 10 characters and include at least one number.<br>';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error .= 'Invalid email format.<br>';
    }

    // Έλεγχος για το αν είναι μοναδικό το όναμα του χρήστη και το email
    $user_check_query = "SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1";
    $stmt = $conn->prepare($user_check_query);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if ($user['username'] === $username) {
            $error .= 'Username already exists.<br>';
        }
        if ($user['email'] === $email) {
            $error .= 'Email already exists.<br>';
        }
    }

    if (empty($error)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO users (name, surname, username, password, email) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $name, $surname, $username, $password_hash, $email);

        if ($stmt->execute()) {
            // Αποθήκευση των πληροφοριών του χρήστη
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['username'] = $username;
            header('Location: feed.php');
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DS Estate - Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <div class="auth-container">
            <div class="register-form">
                <h2>Register</h2>
                <?php if (!empty($error)): ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endif; ?>
                <form action="register.php" method="POST">
                    <input type="text" name="name" placeholder="Name" required value="<?php echo htmlspecialchars($name); ?>">
                    <input type="text" name="surname" placeholder="Surname" required value="<?php echo htmlspecialchars($surname); ?>">
                    <input type="text" name="username" placeholder="Username" required value="<?php echo htmlspecialchars($username); ?>">
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($email); ?>">
                    <button type="submit">Register</button>
                </form>
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>





