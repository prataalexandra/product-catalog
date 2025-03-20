<?php
///function for cleaning the data
///eliminate the spaces and the slashes
///converting special characters to html entities
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

///verify the request method to be POST and make the conection to db
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"]; ///determinate the action- add, delete, update
    $con = mysqli_connect("localhost", "root", "", "bd");

    if (!$con) {
        die('Could not connect: ' . mysqli_error($con)); ///handle errors
    }

    ///add a new category- insert a name
    if ($action == "add") {
        $category_name = test_input($_POST["category_name"]);
        $sql = "INSERT INTO categories (name) VALUES ('$category_name')";
        if (mysqli_query($con, $sql)) {
            echo "New category added successfully";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
        ///delete an existing category by its id
    } elseif ($action == "delete") {
        $category_id = test_input($_POST["category_id"]);
        $sql = "DELETE FROM categories WHERE id='$category_id'";
        if (mysqli_query($con, $sql)) {
            echo "Category deleted successfully";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
        ///editing an existing category- inserting a new name
    } elseif ($action == "edit") {
        $category_id = test_input($_POST["category_id"]);
        $new_category_name = test_input($_POST["new_category_name"]);
        $sql = "UPDATE categories SET name='$new_category_name' WHERE id='$category_id'";
        if (mysqli_query($con, $sql)) {
            echo "Category updated successfully";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    }

    mysqli_close($con); ///close connection
    header("Location: categorii.php"); ///redirect to the page
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories</title>
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <style>
        .form-container {
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse; /*collapse borders for table */
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Categories</h1>
        <hr>
        <!-- add category -->
        <form id="add-category-form" class="form-container" action="categorii.php" method="POST"> 
            <label>Category Name</label><br>
            <input type="text" name="category_name" placeholder="Enter Category Name" required><br><br>
            <button type="submit" name="action" value="add">Add Category</button>
        </form>
        <hr>

        <!-- existing categories table -->
        <h2>Existing Categories</h2>
        <table>
            <tr>
                <th>Category Name</th>
                <th>Actions</th>
            </tr>
            <?php
            // connection to db
            $con = mysqli_connect("localhost", "root", "", "bd");

            if (!$con) {
                die('Could not connect: ' . mysqli_error($con));
            }

            // query- fetch all categories
            $sql = "SELECT * FROM categories";
            $result = mysqli_query($con, $sql);

            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['name'] . "</td>"; ///using hidden for id, we don't want to display it 
                    ///every category has a delete button,an edit button, a view products and a new name for editing it
                    ///view products button redirects to products page
                    echo "<td>
                            <form style='display:inline;' action='categorii.php' method='POST'>
                                <input type='hidden' name='category_id' value='" . $row['id'] . "'>
                                <button type='submit' name='action' value='delete'>Delete</button>
                            </form>
                            <form style='display:inline;' action='categorii.php' method='POST'>
                                <input type='hidden' name='category_id' value='" . $row['id'] . "'>
                                <input type='text' name='new_category_name' placeholder='New Name' required>
                                <button type='submit' name='action' value='edit'>Edit</button>
                            </form>
                            <form style='display:inline;' action='products.php' method='GET'>
                                <input type='hidden' name='category_id' value='" . $row['id'] . "'>
                                <button type='submit'>View Products</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No categories found</td></tr>";
            }

            mysqli_close($con);
            ?>
        </table>
    </div>
</body>
</html>
