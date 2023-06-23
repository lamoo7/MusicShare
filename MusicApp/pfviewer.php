<?php
    session_start();

    if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
        header('location: login.php');
        exit;
    }

    $username = $_GET['user'];

    require_once 'config.php';

    $sql = "SELECT fname, lname FROM users WHERE username = '$username'";
    $result = $link->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $fname = $row["fname"];
            $lname = $row["lname"];
        }
    } else {
        echo "0 results";
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset='UTF-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>MusicApp</title>
        <link rel='shortcut icon' href='logo.png' type='image/x-icon'>
        <link rel='preconnect' href='https://fonts.googleapis.com'>
        <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
        <link href='https://fonts.googleapis.com/css2?family=Inter&display=swap' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
        <link rel='stylesheet' href='style.css'>
    </head>
    <body>
        <?php require_once 'divtop.php' ?>

        <div class='profile'>
            <img src='images/<?php echo htmlspecialchars($username); ?>.jpg' alt='User' id='profile-picture'>
            <h1><?php echo htmlspecialchars($username); ?></h1>
            <h3 id='name'><?php echo htmlspecialchars($fname); ?>&nbsp<?php echo htmlspecialchars($lname); ?></h3>
            <h3 id='uploads'>
                <?php 
                    $sql = "SELECT COUNT(*) as count FROM songs WHERE username = '{$username}'";
                    $result = $link->query($sql);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $count = $row['count'];
                        echo "" . $count;
                    } else {
                        echo "0";
                    }
                ?>
            </h3>
            <h3 id='likes'>
                <?php
                    $sql = "SELECT COALESCE(SUM(likes), 0) AS total_likes
                            FROM songs
                            WHERE username = '$username'";
                    $result = $link->query($sql);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $total_likes = $row["total_likes"];
                        echo "$total_likes";
                    } else {
                        echo "0";
                    }
                ?>
            </h3>
            <h3 id='comments'>
                <?php
                    $result = mysqli_query($link, "SELECT COUNT(*) as comment_count FROM comments WHERE uploader = '$username'");
                    $row = mysqli_fetch_assoc($result);
                    $comment_count = $row['comment_count'];
                    echo "$comment_count";
                ?>
            </h3>
        </div>

        <div class="content" id="songs" style="padding:10px;">

            <?php
                
                $sql = "SELECT username, songName, songFile, likes, comms, created_at
                        FROM songs 
                        WHERE username = '$username'
                        ORDER BY created_at DESC";
                $result = $link->query($sql);
                if ($result->num_rows > 0) {
                
                    while($row = $result->fetch_assoc()) {
                        echo 
                            "<div class='feed' style='scale: 0.85'>
                                <div class='info'>
                                    <img src='images/" . $row["username"] . ".jpg' alt='MusicApp' id='user'>
                                    <h1><b>" . $row["username"] . "</b></h1>
                                </div>
                                <h1 id='name'>" . htmlspecialchars($row["songName"]) . "</h1>
                                <audio controls>
                                    <source src='songs/" . htmlspecialchars($row["songFile"]) . "' type='audio/ogg'>
                                    <source src='songs/" . htmlspecialchars($row["songFile"]) . "' type='audio/mpeg'>
                                    Your browser does not support the audio element.
                                </audio>
                                <p>" . $row["created_at"] . "</p>
                                <div class='interaction'>
                                    <i class='fa fa-heart-o'><span> " . $row["likes"] . "</span></i>
                                    <i class='fa fa-comment-o'><span> " . $row["comms"] . "</span></i>
                                </div>
                            </div>";
                    }
                    
                } else { echo "<h1 style='margin-left:140px; position:absolute;top:50%;left:50%;transform: translate(-50%,-50%);'>0 results</h1>"; }
                $link->close();

            ?>

        </div>

        <script>
            const fileUpload = document.getElementById("audio-upload");
            const fileName = document.getElementById("file-name");

            fileUpload.addEventListener("change", function() {
              fileName.textContent = this.files[0].name;
            });
        </script>
    </body>
</html>