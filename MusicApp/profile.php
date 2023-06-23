<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $songName = $_POST['songName'];
        $songFile = $_FILES['songFile']['name'];
        $uploadPath = 'songs/' . basename($songFile);

        if (isset($_FILES['songFile']) && is_uploaded_file($_FILES['songFile']['tmp_name'])) {

            if (empty($songName)) {
                echo "Please fill in all the fields";
            } else {

                require_once 'config.php';

                if (move_uploaded_file($_FILES['songFile']['tmp_name'], $uploadPath)) {

                    $sql = "INSERT INTO songs (username, songName, songFile) VALUES ('$username', '$songName', '$songFile')";
                    mysqli_query($link, $sql);

                    echo "New record created successfully";
                    mysqli_close($link);

                    header("location: login.php");
                } else {
                    echo "Error moving file to the 'songs' folder";
                }
            }

        } else {
            echo "Please select a file to upload";
        }

    } else {
        echo "You must be logged in to upload a song";
    }
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
            <img src='images/<?php echo htmlspecialchars($_SESSION['username']); ?>.jpg' alt='User' id='profile-picture'>
            <h1><?php echo htmlspecialchars($_SESSION['username']); ?></h1>
            <h3 id='name'><?php echo htmlspecialchars($_SESSION['fname']); ?>&nbsp<?php echo htmlspecialchars($_SESSION['lname']); ?></h3>
            <h3 id='uploads'>
                <?php 
                    require_once 'config.php';
                    $username = $_SESSION['username'];
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
                    $username = $_SESSION['username'];
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
                    $username = $_SESSION['username'];
                    $result = mysqli_query($link, "SELECT COUNT(*) as comment_count FROM comments WHERE uploader = '$username'");
                    $row = mysqli_fetch_assoc($result);
                    $comment_count = $row['comment_count'];
                    echo "$comment_count";
                ?>
            </h3>
        </div>

        <div class="content">

            <a href="reset-password.php">
                <div class='feed' style="display: flex; place-items: center; flex-direction: column; min-height: 316px; text-align: center; color: #DCDDDE; text-decoration: none; ">
                    <h1>Reset Your<br>Password</h1>
                    <img src="passreset.png" alt="settings" style="width:50%;">
                </div>
            </a>

            <form action="imgupload.php" method="post" enctype="multipart/form-data" class="form-reset" style="width:100%">
                <div class='feed' style="display: flex; place-items: center; min-height: 316px; text-align: center; cursor: pointer">
                    <label for="file-upload">

                        <h1>Change Your Profile Picture</h1>
                        <img src="imagereset.png" alt="settings" style="width:50%;">
                    
                    </label>
                    <input id="file-upload" type="file" name="file" accept="image/*">
                    <input type="submit" id="submitBtn" style="display:none;">
                </div>
            </form>


        </div>

        <script>

            document.querySelector('#file-upload').addEventListener('change', function() {
                var file = this.files[0];
                var formData = new FormData();
                formData.append('file', file);
                    
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'imgupload.php');
                xhr.onreadystatechange = function() {
                    if (this.readyState === 4 && this.status === 200) {
                        location.reload();
                    }
                };
                xhr.send(formData);
            });

        </script>
    </body>
</html>