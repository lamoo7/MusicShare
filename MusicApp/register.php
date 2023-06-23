<?php

require_once "config.php";

$username = $password = $confirm_password = $fname = $lname = $email = "";
$username_err = $password_err = $confirm_password_err = $fname_err = $lname_err = $email_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{

        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){

            mysqli_stmt_bind_param($stmt, "s", $param_username);

            $param_username = trim($_POST["username"]);

            if(mysqli_stmt_execute($stmt)){

                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }

    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    if(empty(trim($_POST["fname"]))){
        $fname_err = "Please enter your first name.";     
    } else{
        $fname = trim($_POST["fname"]);
    }
    if(empty(trim($_POST["lname"]))){
        $lname_err = "Please enter your last name.";     
    } else{
        $lname = trim($_POST["lname"]);
    }
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter your email.";     
    } else{
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $email_err = "Invalid email format"; 
        }
}

if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($fname_err) && empty($lname_err) && empty($email_err)) {
    $sql = "INSERT INTO users (username, password, fname, lname, email) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssss", $param_username, $param_password, $param_fname, $param_lname, $param_email);
        $param_username = $username;
        $param_password = password_hash($password, PASSWORD_DEFAULT);
        $param_fname = trim($_POST["fname"]);
        $param_lname = trim($_POST["lname"]);
        $param_email = trim($_POST["email"]);

        mysqli_stmt_execute($stmt);

        $src_file = "default.jpg";
        $dst_file = "images/{$username}.jpg";
        if (!copy($src_file, $dst_file)) {
            // Error copying file
        }

        mysqli_stmt_close($stmt);
        header("location: login.php");
    }
}

mysqli_close($link);
}

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="shortcut icon" href="logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
            <div class="form-group">
                <label>First and Last Name</label>
                <div style="display: flex">
                    <input style="width: 50%" type="text" name="fname" class="form-control <?php echo (!empty($fname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $fname; ?>">
                    <input style="width: 50%; margin-left: 10px;" type="text" name="lname" class="form-control <?php echo (!empty($lname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $lname; ?>">
                </div>
                <span class="invalid-feedback"><?php echo $lname_err; echo $fname_err;?></span>
            </div>
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>
            <div style="display: flex"> 
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group" style="margin-left: 10px">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                    <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                </div>
            </div>
            <div class="form-group">
                <input type="submit" value="Submit">
                <input type="reset" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>    
</body>
</html>