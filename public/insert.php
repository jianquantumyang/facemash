<?php
require_once __DIR__ . "/../private/db.php";
$password = "yourpassword";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the provided password matches the expected password
    if (isset($_POST["password"]) && $_POST["password"] === $password) {
        // Continue with image upload if the password is correct

        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/upload/';
            
            // Generate a random name for the uploaded file
            $randomName = uniqid('image_') . '_' . time();
            $uploadFile = $uploadDir . $randomName . '.' . pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);

            // Move the uploaded file to the upload directory
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadFile)) {
                $imageUrl = mysqli_real_escape_string($db, '/upload/' . $randomName . '.' . pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));

                // Insert the image URL into the database
                $query = "INSERT INTO photo (img) VALUES ('$imageUrl')";
                $result = mysqli_query($db, $query);

                if (!$result) {
                    die("Error inserting image: " . mysqli_error($db));
                }
            } else {
                echo "Error uploading file.";
            }
        }
    } else {
        echo "Incorrect password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FACEMASH - Add Image</title>
    <link rel="stylesheet" type="text/css" href="/style.css">
</head>
<body>
    <div id="header">
        <h1><a href="/">FACEMASH</a></h1>
    </div>

    <div class="main">
        <h2>Add Image</h2>
        <form method="post" action="/insert.php" enctype="multipart/form-data">
            <label for="image_file">Image File:</label>
            <input type="file" id="image_file" name="image_file" accept="image/*" required>
            
            <!-- Add the password input field -->
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Upload Image">
        </form>
    </div>

    <div id="footer">
        <a href="/rankings.php">Rankings</a>
    </div>
</body>
</html>
