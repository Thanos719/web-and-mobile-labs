<?php
session_start();
require 'db.php';

$query = "SELECT * FROM listings";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DS Estate - Feed</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <div class="feed-container">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="property">
                    <img src="<?php echo htmlspecialchars($row['photo']); ?>" alt="Property Photo">
                    <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                    <p>Area: <?php echo htmlspecialchars($row['area']); ?></p>
                    <p>Number of rooms: <?php echo htmlspecialchars($row['number_of_rooms']); ?></p>
                    <p>Price per night: $<?php echo htmlspecialchars($row['price_per_night']); ?></p>
                    <form action="book.php" method="GET">
                        <input type="hidden" name="listing_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                        <button type="submit">Reserve Property</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>


