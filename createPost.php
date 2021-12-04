<?php
    /*
     * Author: Cameron Foy
     * Purpose: Post creation form
     * Last Updated: 11/21/2021
     */
    session_start();
    if (!isset($_SESSION['userId'])) {
        header("Location: LogIn.php");
    }
    require('DBConnect.php');
    require '\xampp\htdocs\Smorgasbord-Trail\php-image-resize-master\lib\ImageResize.php';
    require '\xampp\htdocs\Smorgasbord-Trail\php-image-resize-master\lib\ImageResizeException.php';
    use Gumlet\ImageResize;

    var_dump($_FILES['image']);



    if ($_POST) {
        $error_message = null;
        $error_flag = false;
        $image_path = "";

        /*
         * PARAMETERS{
         *     filename: The original name of the file
         *     file_suffix(''): File suffix for renaming resized files, as it is already obtained in the if statement below
         *     modifier_index(0): The index of the extension for resized images, (1 for medium, 2 for thumbnail)
         *     subfolder_name('uploads'): Easily changable subfolder name
         * }
         *
         * RETURNS{
         *     (STRING)the new file upload path
         * }
        */
        function file_upload_path($filename, $file_suffix = '', $modifier_index = 0, $subfolder_name = 'images')
        {
            $folder = dirname(__FILE__);
            $full_file_suffix = substr(basename($filename), strrpos(basename($filename), '.'));

            //Creates Subfolder if it does not exist
            if (!file_exists($folder.DIRECTORY_SEPARATOR.$subfolder_name)) {
                mkdir($folder.DIRECTORY_SEPARATOR.$subfolder_name);
            }

            if (!file_exists($folder.DIRECTORY_SEPARATOR.$subfolder_name.DIRECTORY_SEPARATOR.basename($filename, $full_file_suffix))) {
                mkdir($folder.DIRECTORY_SEPARATOR.$subfolder_name.DIRECTORY_SEPARATOR.basename($filename, $full_file_suffix));
            }

            $image_path = $folder.DIRECTORY_SEPARATOR.$subfolder_name.DIRECTORY_SEPARATOR.basename($filename, $full_file_suffix);

            if ($modifier_index > 0) {
                $modifiers = ['', 'Post', 'Thumbnail'];
                $path = [$folder, $subfolder_name, basename($filename, $full_file_suffix), $modifiers[$modifier_index].$full_file_suffix];
            } else {
                $path = [$folder, $subfolder_name, basename($filename, $full_file_suffix), "Base".$full_file_suffix];
            }

            return join(DIRECTORY_SEPARATOR, $path);
        }

        /*
         * PARAMETERS{
         *    temp_path: The temporary path of the uploaded file
         *    new_path: The new file upload path
         * }
         *
         * RETURNS{
         *     (BOOL)True if the file is an image of specified types
         * }
         */
        function is_image($temp_path, $new_path)
        {
            $allowed_mime_type = ['image/gif', 'image/jpeg', 'image/png'];
            $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

            $actual_file_extension = pathinfo($new_path, PATHINFO_EXTENSION);
            $actual_mime_type = $_FILES['image']['type'];

            $extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
            $mime_is_valid = in_array($actual_mime_type, $allowed_mime_type);

            return $extension_is_valid and $mime_is_valid;
        }

        $file_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);

        $image = null;

        if ($file_upload_detected) {
            $filename = $_FILES['image']['name'];
            $temporary_path = $_FILES['image']['tmp_name'];
            $new_path = file_upload_path($filename);
            $file_suffix = pathinfo($new_path, PATHINFO_EXTENSION);
            $image = $new_path;

            var_dump($new_path);

            if (is_image($temporary_path, $new_path)) {
                move_uploaded_file($temporary_path, $new_path);

                try {
                    $resize = new ImageResize($new_path);
                    $resize->resizeToLongSide(400);
                    $resize->save(file_upload_path($filename, $file_suffix, 1));
                    $resize->resizeToLongSide(90);
                    $resize->save(file_upload_path($filename, $file_suffix, 2));
                } catch (ImageResizeException $e) {
                    $error_message = "Something went wrong" . $e->getMessage();
                    $error_flag = true;
                }
            }
            else{
                $error_flag = true;
                $error_message = "Uploaded file was not an image(jpg, png, gif)";
            }
        }

        //4.3 sanitized POST
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $title = $post['title'];
        $content = $post['description'];
        $price = $post['price'];

        if (!isset($post['check'])) {
            $location = $post['location'];
        } else {
            $location = null;
        }


        if (!filter_var($price, FILTER_VALIDATE_FLOAT)) {
            preg_replace("/[^0-9.]/", "", $price);
            if (!filter_var($price, FILTER_VALIDATE_FLOAT)) {
                $error_flag = true;
                $error_message = "Price is Invalid";
            }
        }

        if (empty($title) ||
            empty($content) ||
            ctype_space($title) ||
            ctype_space($content)) {
            $error_flag = true;
            $error_message = "Title and Description must have at least one character.";
        }

        if (!$error_flag) {
            $query = "INSERT INTO items (UserId, Title, Description, Price, Location, Image) VALUES (:userid, :title, :description, :price, :location, :image)";

            $statement = $db->prepare($query);

            $statement->bindvalue(':userid', $_SESSION['userId']);
            $statement->bindvalue(':title', $title);
            $statement->bindvalue(':description', $content);
            $statement->bindvalue(':price', $price);
            $statement->bindvalue(':location', $location);
            $statement->bindvalue(':image', $image);

            $statement->execute();

            if ($post['category'] != "") {
                $category_query = "SELECT ItemId FROM items WHERE Created_on IN (SELECT MAX(Created_on) FROM items)";
                $category_statement = $db->prepare($category_query);
                $category_statement->execute();

                $itemId = $category_statement->fetch();

                $item_category_query = "INSERT INTO itemcategories (CategoryId, ItemId) VALUES (:categoryid, :itemid)";

                $item_category_statement = $db->prepare($item_category_query);

                $item_category_statement->bindvalue(":categoryid", $post['category']);
                $item_category_statement->bindvalue(":itemid", $itemId[0]);

                $item_category_statement->execute();
            }

            header('Location: index.php');
        }
    }

    $query = "SELECT * FROM categories ORDER BY Category_Name";
    $statement = $db->prepare($query);
    $statement->execute();
?>
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
            <form action="createPost.php" enctype='multipart/form-data' method="POST" id="create">
                <ul id="createList">
                    <?php if ($_POST): ?>
                        <?php if ($error_flag): ?>
                            <li><h3 id="error_message"><?= $error_message ?></h3></li>
                        <?php endif ?>
                    <?php endif ?>
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
                        <textarea name="description" id="description" cols="50" rows="5"></textarea>
                    </li>
                    <li>
                        <Label for="category">Category:</Label>
                        <select name="category" id="category">
                            <option value=''> -- None --</option>
                        <?php while ($row = $statement->fetch()): ?>
                            <option value="<?= $row[0] ?>"><?= $row[1] ?></option>
                        <?php endwhile ?>
                        </select>
                    </li>
                    <li>
                        <label for="price">Price:</label>
                        <input type="text" id="price" name="price" placeholder=" Ex: 50">
                    </li>
                    <li>
                        <label for="location">Location:</label>
                        <input type="text" id="location" name="location" placeholder="Ex: 555 Sample St, Winnipeg">
                    </li>
                    <li>
                        <label for="location_toggle">Hide Location:</label>
                        <input type="checkbox" id="check" name="check" value="hideLocation">
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