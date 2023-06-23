<div id="top">
    <div class="top-flex">
        <a href="index.php"><img src="logo.png" alt="MusicApp" id="logo" style="max-width:50px"></a>
        <form>
          <input type="text" id="top-input" name="search_query" placeholder="Search...">
          <div id="search-results"></div>
        </form>
    </div>
    <div class="top-flex" style="justify-content: end;">
        <div class="hamburger" id="user" tabindex='0'>
          <span></span>
          <span style="margin: 10px 0;"></span>
          <span></span>
        </div>
        <div id="account">
          <a class="top-a" style="display: block;" href="uploads.php"><span>Your Uploads</span></a>
          <br>
          <a class="top-a" style="display: block;" href="profile.php"><span>Your Account</span></a>
        </div>
        <a class="top-a" href="uploads.php"><span>Your Uploads</span></a>
        <a class="top-a" href="profile.php"><span>Your Account</span></a>
        <img src="images/<?php echo htmlspecialchars($_SESSION["username"]); ?>.jpg" alt="User" id="user" tabindex='0'>
        <div id="account"> 
            <h1><?php echo htmlspecialchars($_SESSION["username"]); ?></h1>
            <a href="logout.php" class="btn logout">Sign Out of Your Account</a>
            <br>
            <br>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    $(document).ready(function() {
      $('#top-input').on('input', function() {
        var searchQuery = $(this).val();
    
        $.ajax({
          type: 'POST',
          url: 'search.php',
          data: { search_query: searchQuery },
          success: function(data) {
            $('#search-results').html(data);
          }
        });
      });
    });

</script>