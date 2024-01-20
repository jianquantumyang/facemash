<?php
require_once __DIR__ . "/../private/db.php";

$query = "SELECT * FROM photo ORDER BY rating DESC";
$result = mysqli_query($db, $query);

if ($result) {
    $rankings = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rankings[] = $row;
    }
} else {
    die("Error: " . mysqli_error($db));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FACEMASH - Rankings</title>
    <link rel="stylesheet" type="text/css" href="/style.css">
</head>
<body>
        <div id="header">
        <h1><a href="/">FACEMASH</a></h1>
    </div>
    <div class="main">
        <h2>Rankings</h2>
        <ul>
            <?php foreach ($rankings as $rank => $photo): ?>
                <li><?php echo $rank + 1; ?>. <img src="<?php echo $photo['img']; ?>"> - Rating: <?php echo $photo['rating']; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
