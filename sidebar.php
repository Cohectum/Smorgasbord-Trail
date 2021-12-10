<?php
    $loggedin = isset($_SESSION['userId']);
?>
<div class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 240px;">
    <div id="sidebar">
        <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link dark text-decoration-none image-box" href="./Index.php"><img src="./Images/STLogo.png" alt="ST Logo" height=150></a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li><a class="nav-link link-dark link-hoverable" href="./index.php">Home</a></li>
            <li><a class="nav-link link-dark link-hoverable" href="./search.php">Search</a></li>
            <li><a class="nav-link link-dark link-hoverable" href="./categories.php">Categories</a></li>
            <li><a class="nav-link link-dark link-hoverable" href="./postList.php?all">All Posts</a></li>
            <li><hr></li>
            <?php if (!$loggedin): ?>
                <li><a class="nav-link link-dark link-hoverable" href="./login.php">Log In</a></li>
                <li><a class="nav-link link-dark link-hoverable" href="./register.php">Register</a></li>
            <?php else: ?>
                <li><a class="nav-link link-dark link-hoverable" href="./viewUser.php">Your Profile</a></li>
                <li><a class="nav-link link-dark link-hoverable" href="./createPost.php">Post</a></li>
                <li><a class="nav-link link-dark link-hoverable" href="">Messages <span class="badge bg-info text-dark">WIP</span></a></li>
                <li><a class="nav-link link-dark link-hoverable" href="./logout.php">Logout</a></li>
            <?php
                endif;
                if (isset($_SESSION['title'])):
                if ($_SESSION['title'] == "Admin"):?>
                <li><hr></li>
                <li id="sidebar-heading"><p class="h5">Moderation</p></li>
                <li><a class="nav-link link-dark link-hoverable" href="./ModerateComments.php">Comments</a></li>
                <li><a class="nav-link link-dark link-hoverable" href="">Posts <span class="badge bg-info text-dark">WIP</span></a></li>
                <li><a class="nav-link link-dark link-hoverable" href="./AdminUserView.php">Users</a></li>
            <?php endif; endif?>
        </ul>
    </div>
</div>