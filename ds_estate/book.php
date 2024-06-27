<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Μεταφορά στη σελίδα σύνδεσης εάν ο χρήστης δεν έχει συνδεθεί
    exit();
}

$listing_id = isset($_GET['listing_id']) ? $_GET['listing_id'] : 0;

if ($listing_id == 0) {
    header('Location: feed.php'); // Μεταφορά στη σελίδα feed εάν δεν παρέχεται έγκυρο ID καταχώρησης
    exit();
}

// Ανάκτηση λεπτομερειών καταχώρησης με βάση το καθορισμένο listing_id
$query = "SELECT * FROM listings WHERE id = $listing_id";
$result = mysqli_query($conn, $query);
$listing = mysqli_fetch_assoc($result);

if (!$listing) {
    header('Location: feed.php');
    exit();
}

$step = isset($_POST['step']) ? (int)$_POST['step'] : 1;
$error = '';
$final_amount = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 1) {
        // Step 1: Έλεγχος διαθεσιμότητας
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        
        $query = "SELECT * FROM reservations WHERE listing_id = $listing_id AND 
                  ((start_date <= '$start_date' AND end_date >= '$start_date') OR 
                   (start_date <= '$end_date' AND end_date >= '$end_date') OR 
                   (start_date >= '$start_date' AND end_date <= '$end_date'))";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $error = "The property is not available for the selected dates. Please choose different dates.";
        } else {
            $datetime1 = new DateTime($start_date);
            $datetime2 = new DateTime($end_date);
            $interval = $datetime1->diff($datetime2);
            $nights = $interval->format('%a');
            $initial_amount = $listing['price_per_night'] * $nights;
            $discount_rate = rand(10, 30) / 100;
            $final_amount = $initial_amount - ($initial_amount * $discount_rate);
            $step = 2;
        }
    } elseif ($step == 2) {
        // Step 2: Επιβεβαίωση κράτησης
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $surname = isset($_POST['surname']) ? $_POST['surname'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $final_amount = $_POST['final_amount'];

        $query = "INSERT INTO reservations (listing_id, user_id, start_date, end_date, amount, name, surname, email)
                  VALUES ('$listing_id', '{$_SESSION['user_id']}', '$start_date', '$end_date', '$final_amount', '$name', '$surname', '$email')";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Booking successful!";
            header('Location: feed.php');
            exit();
        } else {
            $error = "There was an error processing your booking. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DS Estate - Book</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <div class="book-container">
            <h2><?php echo htmlspecialchars($listing['title']); ?></h2>
            <img src="<?php echo htmlspecialchars($listing['photo']); ?>" alt="Property Photo">
            <p>Area: <?php echo htmlspecialchars($listing['area']); ?></p>
            <p>Number of rooms: <?php echo htmlspecialchars($listing['number_of_rooms']); ?></p>
            <p>Price per night: $<?php echo htmlspecialchars($listing['price_per_night']); ?></p>
            
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <?php if ($step == 1): ?>
                <form action="book.php?listing_id=<?php echo $listing_id; ?>" method="POST">
                    <input type="hidden" name="step" value="1">
                    <label for="start_date">Start Date:</label>
                    <input type="date" name="start_date" required>
                    <label for="end_date">End Date:</label>
                    <input type="date" name="end_date" required>
                    <button type="submit">Continue</button>
                </form>
            <?php elseif ($step == 2): ?>
                <form action="book.php?listing_id=<?php echo $listing_id; ?>" method="POST">
                    <input type="hidden" name="step" value="2">
                    <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($_POST['start_date']); ?>">
                    <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($_POST['end_date']); ?>">
                    <input type="hidden" name="final_amount" value="<?php echo $final_amount; ?>">
                    <label for="name">Name:</label>
                    <input type="text" name="name" value="<?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : ''; ?>" required>
                    <label for="surname">Surname:</label>
                    <input type="text" name="surname" value="<?php echo isset($_SESSION['surname']) ? htmlspecialchars($_SESSION['surname']) : ''; ?>" required>
                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" required>
                    <p class="final-amount">Final Amount to be Paid: $<?php echo number_format($final_amount, 2); ?></p>
                    <button type="submit">Confirm Booking</button>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>




