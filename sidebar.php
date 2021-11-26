<?php
    $loggedin = isset($_SESSION['userId']);
?>
<!DOCTYPE html>
<html>
    <body>
        <div id="sidebarBox">
            <a href="./index.php"><img src="./Images/STLogo.png" alt="ST Logo" height=150></a>
            <nav> 
                <ul id="sidebarList">
                    <li><a href="./index.php">Home</a></li>
                    <li><a href="./search.php">Search</a></li>
                    <li><a href="./categories.php">Categories</a></li>
                    <li><a href="./postList.php?all">All Posts</a></li>
                    <hr>
                    <?php if(!$loggedin): ?>
                        <li><a href="./login.php">Log In</a></li>
                        <li><a href="./register.php">Register</a></li>
                    <?php else: ?>
                        <li><a href="./viewUser.php">Your Profile</a></li>
                        <li><a href="./createPost.php">Post</a></li>
                        <li><a href="./messages.php">Messages</a></li>
                        <li><a href="./logout.php">Logout</a></li>
                    <?php 
                        endif;
                        if (isset($_SESSION['title'])):
                        if ($_SESSION['title'] == "Admin"):?>
                        <li><a href="./ModerateComments.php">All Comments</a></li>
                        <li><a href="./ModeratePosts.php">Moderate Posts</a></li>
                        <li><a href="./AdminUserView.php">View All Users</a></li>
                    <?php endif; endif?>
                </ul>
            </nav>
        </div>
    </body> 
</html>