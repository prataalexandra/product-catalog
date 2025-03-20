<?php
///connect to db
$con = mysqli_connect("localhost", "root", "", "bd");
///error
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

///query for all categories
$categories_sql = "SELECT * FROM categories";
$categories_result = mysqli_query($con, $categories_sql);

///get all products for an id
function getProducts($con, $category_id) {
    $products_sql = "SELECT * FROM products WHERE category_id='$category_id'";
    return mysqli_query($con, $products_sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Catalog</title>
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e8f5e9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: 60px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #388e3c;
            margin-bottom: 20px;
        }
        hr {
            width: 50%;
            border: 1px solid #81c784;
            margin: 20px auto;
        }
        .category {
            cursor: pointer;
            margin: 10px 0;
            padding: 15px;
            background: #4caf50;
            color: #fff;
            border-radius: 8px;
            text-align: center;
        }
        .category:hover {
            background: #45a049;
        }
        .hidden {
            display: none;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            padding: 10px;
            background: #fff;
            border: 1px solid #ddd;
            margin-bottom: 5px;
            border-radius: 5px;
            cursor: pointer;
        }
        li:hover {
            background: #f1f1f1;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            width: 80%;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            cursor: pointer;
        }
        .close:hover {
            color: #333;
        }
    </style>
    <script>
        ///toggle the display of products in a category
        function toggleProducts(categoryId) {
            var productsDiv = document.getElementById('products-' + categoryId);
            if (productsDiv.style.display === 'none' || productsDiv.style.display === '') {
                productsDiv.style.display = 'block';
            } else {
                productsDiv.style.display = 'none';
            }
        }
        
        ///show product details in a modal
        ///using product_details page
        function showProductDetails(productId) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'product_details.php?product_id=' + productId, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('modal-content').innerHTML = xhr.responseText + '<span class="close" onclick="closeModal()">&times;</span>';
                    document.getElementById('modal').style.display = 'block';
                }
            };
            xhr.send();
        }
        ///closing the modal
        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Product Catalog</h1>
        <hr>

        <?php
        if ($categories_result && mysqli_num_rows($categories_result) > 0) {
            while ($category = mysqli_fetch_assoc($categories_result)) {
                $category_id = htmlspecialchars($category['id']);
                echo "<div class='category' onclick='toggleProducts($category_id)'>" . htmlspecialchars($category['name']) . "</div>";
                echo "<div id='products-$category_id' class='hidden'>";
                ///get products from current category
                $products_result = getProducts($con, $category_id);
                if ($products_result && mysqli_num_rows($products_result) > 0) {
                    echo "<ul>";
                    while ($product = mysqli_fetch_assoc($products_result)) {
                        $product_id = htmlspecialchars($product['id']);
                        ///get product's details
                        echo "<li onclick='showProductDetails($product_id)'>" . htmlspecialchars($product['name']) . "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No products available.</p>";
                }
                echo "</div>";
            }
        } else {
            echo "<p>No categories found.</p>";
        }
        mysqli_close($con);
        ?>
    </div>

    <div id="modal" class="modal">
        <div class="modal-content" id="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
    </div>
</body>
</html>
