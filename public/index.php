<?php
require_once __DIR__ . "/../private/db.php";

function getRandomImagePair() {
    global $db;
    
    $query = "SELECT * FROM photo ORDER BY RAND() LIMIT 2";
    $result = mysqli_query($db, $query);

    if ($result) {
        $imagePair = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $imagePair[] = $row;
        }
        return $imagePair;
    } else {
        die("Error: " . mysqli_error($db));
    }
}

function updateRatings($winnerId, $loserId) {
    global $db;

    // Retrieve the ratings of the winner and loser
    $query = "SELECT rating FROM photo WHERE id IN ($winnerId, $loserId)";
    $result = mysqli_query($db, $query);

    if ($result) {
        $ratings = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $ratings[] = $row['rating'];
        }

        // Elo rating system parameters
        $kFactor = 32;
        $expectedWinner = 1 / (1 + 10 ** (($ratings[1] - $ratings[0]) / 400));
        $expectedLoser = 1 / (1 + 10 ** (($ratings[0] - $ratings[1]) / 400));

        // Calculate new ratings
        $newRatingWinner = $ratings[0] + $kFactor * (1 - $expectedWinner);
        $newRatingLoser = $ratings[1] + $kFactor * (0 - $expectedLoser);

        // Update ratings in the database
        $queryUpdate = "UPDATE photo SET rating = CASE
            WHEN id = $winnerId THEN $newRatingWinner
            WHEN id = $loserId THEN $newRatingLoser
            END
            WHERE id IN ($winnerId, $loserId)";
        
        $resultUpdate = mysqli_query($db, $queryUpdate);

        if (!$resultUpdate) {
            die("Error updating ratings: " . mysqli_error($db));
        }
    } else {
        die("Error retrieving ratings: " . mysqli_error($db));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['winner']) && isset($_POST['loser'])) {
        $winnerId = (int)$_POST['winner'];
        $loserId = (int)$_POST['loser'];

        // Update ratings based on the Elo rating system
        updateRatings($winnerId, $loserId);

        // Redirect to get a new random image pair
        header("Location: /");
        exit();
    }
}

$imagePair = getRandomImagePair();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FACEMASH</title>
    <link rel="stylesheet" type="text/css" href="/style.css">
    <style>
        .selected-photo {
            border: 2px solid #007BFF; /* Set the border color for the selected photo */
        }
    </style>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var images = document.querySelectorAll(".selected-photo");
        var winnerInput = document.querySelector("input[name='winner']");
        var loserInput = document.querySelector("input[name='loser']");

        images.forEach(function (image) {
            image.addEventListener("click", function () {
                // Remove the border from all images
                images.forEach(function (img) {
                    img.classList.remove("selected-photo");
                });

                // Add the border to the clicked image
                image.classList.add("selected-photo");

                // Update the hidden input values
                winnerInput.value = image.dataset.photoId;
                loserInput.value = getOtherPhotoId(images, image.dataset.photoId);
            });
        });

        function getOtherPhotoId(images, currentId) {
            for (var i = 0; i < images.length; i++) {
                if (images[i].dataset.photoId !== currentId) {
                    return images[i].dataset.photoId;
                }
            }
            return null;
        }
    });
</script>


</head>
<body>
    <div id="header">
        <h1><a href="/">FACEMASH</a></h1>
    </div>

    <div class="main">
        <h3>Were we let in for our looks? No. Will we be judged on them? Yes.</h3>
        <h2>Who's Better? Click to Choose.</h2>

        <form method="post" action="/">
            <table>
                <tr>
                    <td><img src="<?php echo $imagePair[0]['img']; ?>" class="selected-photo" data-photo-id="<?php echo $imagePair[0]['id']; ?>" /></td>
<td>OR</td>
<td><img src="<?php echo $imagePair[1]['img']; ?>" class="selected-photo" data-photo-id="<?php echo $imagePair[1]['id']; ?>" /></td>

                </tr>
            </table>
            <input type="hidden" name="winner" value="<?php echo $imagePair[0]['id']; ?>">
            <input type="hidden" name="loser" value="<?php echo $imagePair[1]['id']; ?>">
            <input type="submit" value="Vote">
        </form>
    </div>

    <div id="footer">
        <a href="/rankings.php">Rankings</a>
    </div>
</body>
</html>
