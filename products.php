<?php
///db connection
$con = mysqli_connect("localhost", "root", "", "bd");

if (!$con) {
    die('Could not connect: ' . mysqli_error($con)); ///error
}

/// get the category id from the query- we only need products for that category
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$error_message = "";

if ($category_id > 0) {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        /// add new product- name:string, price:integer, description:string, inStock:boolean
        if (isset($_POST['add_product'])) {
            $name = $_POST['name'];
            $price = $_POST['price'];
            $description = $_POST['description'];
            $inStock = isset($_POST['in_stock']) ? 1 : 0;
            if($price > 0){
                ///we insert it only if the price is >0

            $sql = "INSERT INTO products (category_id, name, price, description, InStock) VALUES ('$category_id', '$name', '$price', '$description', '$inStock')";
            mysqli_query($con, $sql);}
            else{
                $error_message="Price must be >0 !";
            }
        }

        /// update product
        if (isset($_POST['update_product'])) {
            $product_id = $_POST['product_id'];
            $name = $_POST['name'];
            $price = $_POST['price'];
            $description = $_POST['description'];
            $inStock = isset($_POST['in_stock']) ? 1 : 0;
            if($price > 0){
                ///we update the product only if the price is >0

            $sql = "UPDATE products SET name='$name', price='$price', description='$description', InStock='$inStock' WHERE id='$product_id'";
            mysqli_query($con, $sql);
            }
            else {
                $error_message="Price must be >0 !";
            }
        }

        /// delete product by its id
        if (isset($_POST['delete_product'])) {
            $product_id = $_POST['product_id'];

            $sql = "DELETE FROM products WHERE id='$product_id'";
            mysqli_query($con, $sql);
        }
    }

    /// fetch products for the category
    $sql = "SELECT * FROM products WHERE category_id='$category_id'";
    $result = mysqli_query($con, $sql);
} else {
    echo "Invalid category ID.";
    mysqli_close($con);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products in Category</title>
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <style>
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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
        form {
            margin-bottom: 20px;
        }
    </style>
    <script>
        ///function to show message alerts
        function showAlert(message) {
            alert(message);
        }
    </script>
</head>
<body>
    <?php if ($error_message != ""): ?>
        <script>
            showAlert("<?php echo $error_message; ?>");
        </script>
    <?php endif; ?>
</head>
<body>
    <div class="container">
        <h1>Products in Category</h1>
        <hr>
        <!-- add product -->
        <h2>Add New Product</h2>
        <form method="POST">
            <label for="name">Product Name:</label><br>
            <input type="text" id="name" name="name" required><br>
            <label for="price">Price:</label><br>
            <input type="text" id="price" name="price" required><br>
            <label for="description">Description:</label><br>
            <textarea id="description" name="description" required></textarea><br>
            <label for="in_stock">In Stock:</label>
            <input type="checkbox" id="in_stock" name="in_stock"><br>
            <input type="hidden" name="add_product" value="1">
            <input type="submit" value="Add Product">
        </form>

        <!-- table with product details -->
        <table>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Description</th>
                <th>In Stock</th>
                <th>Actions</th>
            </tr>
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    $inStock = ($row['InStock'] == 1) ? 'yes' : 'no';
                    ///InStock is boolean, transforming it in yes or no
                    echo "<td>" . htmlspecialchars($inStock) . "</td>";
                    echo "<td>";
                    echo "<form method='POST' style='display:inline;'>";
                    ///id must be hidden, we don't need to see it
                    ///prefills the form with current details
                    ///update form is hidden
                    echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($row['id']) . "'>";
                    echo "<input type='text' name='name' value='" . htmlspecialchars($row['name']) . "' required>";
                    echo "<input type='text' name='price' value='" . htmlspecialchars($row['price']) . "' required>";
                    echo "<textarea name='description' required>" . htmlspecialchars($row['description']) . "</textarea>";
                    echo "<label for='in_stock_" . htmlspecialchars($row['id']) . "'>In Stock:</label>";
                    $checked = $row['InStock'] == 1 ? "checked" : "";
                    echo "<input type='checkbox' id='in_stock_" . htmlspecialchars($row['id']) . "' name='in_stock' $checked>";
                    echo "<input type='hidden' name='update_product' value='1'>";
                    echo "<input type='submit' value='Update'>";
                    echo "</form>";
                    echo " ";
                    ///delete form is hidden
                    ///id is hidden
                    echo "<form method='POST' style='display:inline;'>";
                    echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($row['id']) . "'>";
                    echo "<input type='hidden' name='delete_product' value='1'>";
                    echo "<input type='submit' value='Delete'>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No products found</td></tr>";
            }
            mysqli_close($con);
            ?>
        </table>
    </div>
</body>
</html>
