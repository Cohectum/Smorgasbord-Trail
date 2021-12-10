<?php
    require __DIR__.'/vendor/autoload.php';
    /*
     * Author: Cameron Foy
     * Purpose: Post edit/delete form
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

    // 4.3 SANITIZE GET and 4.2 VALIDATE id
    $get = filter_input(INPUT_GET, "id", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $id = filter_var($get, FILTER_VALIDATE_INT);

    if ($_POST) {
        if ($_POST['submit'] == "Update") {
            $error_message = null;
            $error_flag = false;
            $image_path = "";
            $success_flag = false;


            /*
             * PARAMETERS{
             *     filename: The original name of the file
             *     file_suffix(''): File suffix for renaming resized files, as it is already obtained in the if statement below
             *     modifier_index(0): The index of the extension for resized images, (1 for post, 2 for thumbnail)
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
            $upload_error_detected = isset($_FILES['image']) && ($_FILES['image']['error'] > 0);

            //Regular path if file upload is good
            if ($file_upload_detected) {
                $filename = $_FILES['image']['name'];
                $temporary_path = $_FILES['image']['tmp_name'];
                $new_path = file_upload_path($filename);
                $file_suffix = pathinfo($new_path, PATHINFO_EXTENSION);

                //Saves variations of images if image is correct
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
            }

            //4.3 Sanitize Post
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $title = $post['title'];
            $content = $post['description'];
            $price = $post['price'];

            //Checks if location should be hidden
            if (!isset($post['check'])) {
                $location = $post['location'];
            } else {
                $location = null;
            }

            //Checks for valid price
            if (!filter_var($price, FILTER_VALIDATE_FLOAT)) {
                preg_replace("/[^0-9.]/", "", $price);
                if (!filter_var($price, FILTER_VALIDATE_FLOAT)) {
                    $error_flag = true;
                    $error_message = "Price is Invalid";
                }
            }

            //Checks for empty title or content
            if (empty($title) ||
                empty($content) ||
                ctype_space($title) ||
                ctype_space($content)) {
                $error_flag = true;
                $error_message = "Title and Description must have at least one character.";
            }

            if (!$error_flag) {
                $updateString = "";

                $counter = 0;

                if (isset($post['title'])) {
                    $updateString = $updateString."Title = :title";
                    $counter++;
                }
                if (isset($post['description'])) {
                    $updateString = $updateString.($counter = 0 ? "" : ", ")."Description = :description";
                    $counter++;
                }
                if (isset($post['price'])) {
                    $updateString = $updateString.($counter = 0 ? "" : ", ")."Price = :price";
                    $counter++;
                }

                $updateString = $updateString.($counter = 0 ? "" : ", ")."Location = :location";
                $counter++;

                if ($file_upload_detected || isset($post['remove_image'])) {
                    $updateString = $updateString.($counter = 0 ? "" : ", ")."Image = :image";
                    $counter++;
                }

                $queryString = "UPDATE items SET ".$updateString." WHERE ItemId = {$id}";
                $query = $queryString;
                $statement = $db->prepare($queryString);

                if (isset($post['title'])) {
                    $statement->bindValue(':title', $title);
                }
                if (isset($post['description'])) {
                    $statement->bindValue(':description', $content);
                }
                if (isset($post['price'])) {
                    $statement->bindValue(':price', $price);
                }

                $statement->bindValue(':location', $location);

                if ($file_upload_detected) {
                    $statement->bindValue(':image', file_upload_path($_FILES['image']['name']));
                } elseif (isset($post['remove_image'])) {
                    $statement->bindValue(':image', null);
                }

                $statement->execute();


                if (isset($post['category'])) {
                    $category_query = "SELECT ItemId FROM items WHERE Created_on IN (SELECT MAX(Created_on) FROM items)";
                    $category_statement = $db->prepare($category_query);
                    $category_statement->execute();
                    $itemId = $category_statement->fetch();

                    $remove_previous_category = "DELETE FROM itemcategories WHERE ItemId = {$id}";
                    $remove_statement = $db->prepare($remove_previous_category);
                    $remove_statement->execute();

                    $item_category_query = "INSERT INTO itemcategories (CategoryId, ItemId) VALUES (:categoryid, :itemid)";
                    $item_category_statement = $db->prepare($item_category_query);
                    $item_category_statement->bindvalue(":categoryid", $post['category']);
                    $item_category_statement->bindvalue(":itemid", $get);
                    $item_category_statement->execute();
                }

                $success_flag = true;
            }
        } elseif ($_POST['submit'] == "Delete") {
            $query = "DELETE FROM items WHERE ItemId = {$id}";
            $statement = $db->prepare($query);
            $statement->execute();

            $categoryQuery = "DELETE FROM itemcategories WHERE ItemId = {$id}";
            $categorystatement = $db->prepare($categoryQuery);
            $categorystatement->execute();

            header('Location: viewUser.php?delete=success');
        }
    }


    if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        $query = "SELECT * FROM items WHERE ItemId = {$id}";
        $statement = $db->prepare($query);
        $statement->execute();
        $item = $statement->fetch();

        if ($statement->rowCount() == 0) {
            header('Location: index.php?404');
        }

        $categoryquery = "SELECT CategoryId FROM itemcategories WHERE ItemId = {$id}";
        $categorystatement = $db->prepare($categoryquery);
        $categorystatement->execute();
        $itemCategory = $categorystatement->fetch();

        $query = "SELECT * FROM categories ORDER BY Category_Name";
        $statement = $db->prepare($query);
        $statement->execute();

        if ($item['UserId'] != $_SESSION['userId']) {
            header('Location: index.php?404');
        }
    } else {
        header('Location: index.php?404');
    }




?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Smorgasbord Trail - <?= $item['Title']?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="./main.css">
    </head>
    <body>
        <div id="wrapper">
            <?php include('sidebar.php') ?>
            <div class="container">
                <form class="row" action="editPost.php?id=<?= $id ?>" method="post" enctype='multipart/form-data' id="create">
                    <?php if ($_POST): ?>
                        <?php if ($success_flag): ?>
                            <li><h1>Your Edits were successful, post updated.</h1></li>
                        <?php endif ?>
                    <?php endif ?>
                    <?php if (isset($item['Image'])): ?>
                        <img src="<?= str_replace("Base", "Post", ".".substr($item['Image'], strpos($item['Image'], "images") - 1)) ?>" alt="Post Image">
                        <hr>
                    <?php endif ?>
                    <ul id="createList">
                        <?php if ($_POST): ?>
                            <?php if ($error_flag): ?>
                                <li><h3 id="error_message"><?= $error_message ?></h3></li>
                            <?php endif ?>
                        <?php endif ?>

                        <li>
                            <label for="image">New Image:</label>
                            <input class="form-control" type="file" id="image" name="image">
                        </li>
                        <li>
                            <label for="remove_image">Remove Image:</label>
                            <input type="checkbox" name="remove_image" id="remove_image">
                        </li>
                        <li>
                            <label for="title">Title:</label>
                            <input class="form-control" type="text" id="title" name="title" value="<?= $item['Title'] ?>">
                        </li>
                        <li>
                            <label for="description">Description:</label>
                            <textarea class="form-control" name="description" id="description" cols="50" rows="5"><?= $item['Description']?></textarea>
                        </li>
                        <li>
                            <Label for="category">Category:</Label>
                            <select class="form-select" name="category" id="category">
                                <option value=""> -- None -- </option>
                            <?php while ($row = $statement->fetch()): ?>
                                    <?php if ($row[0] == $itemCategory[0]): ?>
                                        <option selected value="<?= $row[0] ?>"><?= $row[1] ?></option>
                                    <?php else: ?>
                                        <option value="<?= $row[0] ?>"><?= $row[1] ?></option>
                                    <?php endif ?>
                            <?php endwhile ?>
                            </select>
                        </li>
                        <li>
                            <label for="price">Price:</label>
                            <input class="form-control" type="text" id="price" name="price" value="<?= $item['Price'] ?>">
                        </li>
                        <li>
                            <label for="location">Location:</label>
                            <input class="form-control" type="text" id="location" name="location" placeholder="Ex: 555 Sample St, Winnipeg" value="<?= $item['Location'] ?>">
                        </li>
                        <li>
                            <label for="check">Hide Location:</label>
                            <?php if ($item['Location'] == null): ?>
                                <input type="checkbox" id="check" name="check" value="hideLocation" checked>
                            <?php else: ?>
                                <input type="checkbox" id="check" name="check" value="hideLocation">
                            <?php endif ?>
                        </li>
                        <li>
                            <input class="btn btn-primary" type="submit" name="submit" value="Update">
                            <input class="btn btn-secondary" type="submit" name="submit" value="Delete" onclick="return confirm('Are you sure you wish to delete this post?')">
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </body>
</html>