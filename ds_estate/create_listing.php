<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $area = $_POST['area'];
    $number_of_rooms = $_POST['number_of_rooms'];
    $price_per_night = $_POST['price_per_night'];
    $user_id = $_SESSION['user_id'];
    
    // Handle the image upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["photo"]["name"]);
    move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);

    $query = "INSERT INTO listings (photo, title, area, number_of_rooms, price_per_night, user_id) VALUES ('$target_file', '$title', '$area', '$number_of_rooms', '$price_per_night', '$user_id')";

    if (mysqli_query($conn, $query)) {
        header('Location: feed.php');
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DS Estate - Create Listing</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function validateForm() {
            var title = document.getElementById('title').value;
            var area = document.getElementById('area').value;
            var titleErrorElement = document.getElementById('titleError');
            var areaErrorElement = document.getElementById('areaError');
            var regex = /\d/; // Regular expression to check for digits

            var valid = true;

            if (regex.test(title)) {
                titleErrorElement.textContent = 'Title should not contain numbers.';
                valid = false;
            } else {
                titleErrorElement.textContent = '';
            }

            if (regex.test(area)) {
                areaErrorElement.textContent = 'Area should not contain numbers.';
                valid = false;
            } else {
                areaErrorElement.textContent = '';
            }

            return valid;
        }
    </script>
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <div class="create-listing-container">
            <h2>Create Listing</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="create_listing.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                <label for="photo">Photo:</label>
                <input type="file" name="photo" required>
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required>
                <span id="titleError" class="error"></span>
                <label for="area">Area:</label>
                <input type="text" name="area" id="area" required>
                <span id="areaError" class="error"></span>
                <label for="number_of_rooms">Number of rooms:</label>
                <input type="number" name="number_of_rooms" required>
                <label for="price_per_night">Price per night:</label>
                <input type="number" name="price_per_night" required>
                <button type="submit">Create Listing</button>
            </form>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>







