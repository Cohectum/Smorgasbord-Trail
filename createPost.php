<!DOCTYPE html>
<html>
    <head>
        <title>Smorgasbord Trail - Post</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="./main.css">
    </head>
    <body>
        <div id="wrapper">
            <?php include("sidebar.php")?>
            <form action="./DBCreateUpdate.php" method="POST" id="create">
                <ul id="createList">
                    <li>
                        <label for="image">Image:</label>
                        <input type="file" id="image" name="image">
                    </li>
                    <li>
                        <label for="title">Title:</label>
                        <input type="text" id="title" name="title">
                    </li>
                    <li>
                        <label for="description">Description:</label>
                        <textarea name="Description" id="description" cols="50" rows="5"></textarea>
                    </li>
                    <li>
                        <label for="price">Price:</label>
                        <input type="text" id="price" name="price" placeholder=" Ex: $50">
                    </li>
                    <li>
                        <label for="location">Location:</label>
                        <input type="text" id="location" name="location" placeholder="Ex: 555 Sample St, Winnipeg, Manitoba">
                    </li>
                    <li>
                        <input type="submit" value="Post Listing">
                    </li>
                </ul>
            </form>
            <?php include("footer.php")?>
        </div> <!-- Close Wrapper div -->
    </body>
</html>