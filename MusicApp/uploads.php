<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ){
        header("location: login.php");
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $username = $_SESSION["username"];
    
        $songName = $_POST["songName"];
        $songFile = $_FILES["songFile"]["name"];
        $fileTmp = $_FILES["songFile"]["tmp_name"];

        $destination =  "songs/" . $songFile;
        if (move_uploaded_file($fileTmp, $destination)) {

            require_once "config.php";
    
            $stmt = $link->prepare("INSERT INTO songs (username, songName, songFile) VALUES (?, ?, ?)");
    
            $stmt->bind_param("sss", $username, $songName, $songFile);
            $stmt->execute();
    
            if ($stmt->affected_rows > 0) {
                //echo "Song inserted successfully!";
            } else {
                //echo "Error inserting song.";
            }
    
            // Close the statement
            $stmt->close();
        } else {
            //echo "Error moving file.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MusicApp</title>
        <link rel="shortcut icon" href="logo.png" type="image/x-icon">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php require_once 'divtop.php' ?>

        <div id="songs">

            <div class='feed'>
                <div class='info'>
                    <img src='images/<?php echo htmlspecialchars($_SESSION['username']); ?>.jpg' alt='profile picture' id='user'>
                    <h1><b><?php echo htmlspecialchars($_SESSION['username']); ?></b></h1>
                </div>
                <br>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data"> 
                    <input type="text" placeholder="Song Title..." name="songName" id="songtitle">
                    <br>
                    <br>
                    <label for="audio-upload" id="audio" style="text-align: center">
                      Upload Audio
                    </label>
                    <input type="file" id="audio-upload" name="songFile" accept="audio/*" style="display:none">
                    <p id="file-name"></p>
                    <br>
                    <input type="submit" value="Submit" id="song-adder">
                </form>
            </div>

            <?php
                require_once 'config.php';
                
                $sql = "SELECT s.id, s.username, s.songName, s.songFile, s.likes, COUNT(c.comm_id) AS comms, s.created_at
                FROM songs s
                LEFT JOIN comments c ON s.songName = c.songname AND s.username = c.uploader AND s.songFile = c.songfile
                GROUP BY s.id
                ORDER BY created_at DESC";

                $result = $link->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      echo '<div class="feed">
                                <div class="info">
                                  <img src="images/' . $row["username"] . '.jpg" alt="MusicApp" id="user">
                                  <h1><b>' . $row["username"] . '</b></h1>
                                </div>
                                <h1 id="name">' . htmlspecialchars($row["songName"]) . '</h1>
                                <audio controls>
                                  <source src="songs/' . htmlspecialchars($row["songFile"]) . '" type="audio/ogg">
                                  <source src="songs/' . htmlspecialchars($row["songFile"]) . '" type="audio/mpeg">
                                  Your browser does not support the audio element.
                                </audio>
                                <p>' . $row["created_at"] . '</p>
                                <div class="interaction">
                                <i class="fa fa-heart-o"><span> ' . $row["likes"] . '</span></i>
                                <i class="fa fa-comment-o reveal" tabindex="0"><span>' . $row["comms"] . '</span></i>
                                  <div class="comment-section hidden">
                                    <h2 style="margin:20px">Comments</h2><i class="fa fa-times close"></i>';
            
                  $comments_query = "SELECT commenter, comment FROM comments WHERE songname='" . $row["songName"] . "' AND uploader='" . $row["username"] . "' AND songfile='" . $row["songFile"] . "'";
                  $comments_result = $link->query($comments_query);
                  if ($comments_result->num_rows > 0) {
                    echo '<div id="section">';
                    while ($comment_row = $comments_result->fetch_assoc()) {
                        echo '<div class="info">
                                <img src="images/' . htmlspecialchars($comment_row["commenter"]) . '.jpg" alt="MusicApp" id="user">
                                <h1><b>' . $comment_row["commenter"] . '</b></h1><h2>: ' . htmlspecialchars($comment_row["comment"]) . '</h2>
                              </div>';
                    }
                    echo '</div>';
                  } else {
                      echo "<p style='position:absolute;top:50%;left:50%;transform: translate(-50%,-50%);'>No comments yet</p>";
                  }
            
                  echo '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">
                            <input type="hidden" name="songname" value="' . htmlspecialchars($row["songName"]) . '">
                            <input type="hidden" name="uploader" value="' . htmlspecialchars($row["username"]) . '">
                            <input type="hidden" name="songfile" value="' . htmlspecialchars($row["songFile"]) . '">
                            <input type="hidden" name="commenter" value="' . $_SESSION["username"] . '">
                            <div id="comment-add">
                                <input type="text" id="songtitle" name="comment" placeholder="Add a comment..." required><br>
                                <input type="submit" name="submit-comment" value="Submit">
                            </div>
                            </form>
                        </div>
                      </div>
                    </div>';
                }
            }
        
            if ($result->num_rows == 0) {
                echo "<h1 style='position:absolute;top:50%;left:50%;transform: translate(-50%,-50%);'>0 results</h1>";
            }

            $link->close();
        ?>

        </div>
        <script>
            const fileUpload = document.getElementById("audio-upload");
            const fileName = document.getElementById("file-name");

            fileUpload.addEventListener("change", function() {
              fileName.textContent = this.files[0].name;
            });

            let commentSections = document.querySelectorAll(".comment-section");
            let commentButtons = document.querySelectorAll(".reveal");
            let closeButtons = document.querySelectorAll(".comment-section .close");

            for (let i = 0; i < commentButtons.length; i++) {
              commentButtons[i].addEventListener("click", function () {
                commentSections[i].classList.remove("hidden");
              });
            }

            for (let i = 0; i < closeButtons.length; i++) {
              closeButtons[i].addEventListener("click", function () {
                commentSections[i].classList.add("hidden");
              });
            }
            
            document.addEventListener("click", function(event) {
              for (let i = 0; i < commentSections.length; i++) {
                if (!commentSections[i].contains(event.target) && !commentButtons[i].contains(event.target)) {
                  commentSections[i].classList.add("hidden");
                }
              }
            });
        </script>
    </body>
</html>