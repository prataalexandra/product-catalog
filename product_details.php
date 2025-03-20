<?php
///conection tp db
$con = mysqli_connect("localhost", "root", "", "bd");
///error
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

///get product id from query
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
///check if the id is valid
if ($product_id > 0) {
    ///get product's details
    $sql = "SELECT * FROM products WHERE id='$product_id'";
    $result = mysqli_query($con, $sql);
    ///transform the product details as an associative array(key-value)
    $product = mysqli_fetch_assoc($result);
} else {
    ///if the id isn't valid
    echo "Invalid product ID.";
    mysqli_close($con);
    exit();
}
?>

<?php if ($product) { ?>
    <table>
        <tr>
            <!-- display the product details in a table -->
            <th>Product Name</th>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
        </tr>
        <tr>
            <th>Price</th>
            <td><?php echo htmlspecialchars($product['price']); ?></td>
        </tr>
        <tr>
            <th>Description</th>
            <td><?php echo htmlspecialchars($product['description']); ?></td>
        </tr>
        <tr>
            <th>In Stock</th>
            <!-- 1-Yes, 0-No -->
            <td><?php echo ($product['InStock'] == 1) ? 'yes' : 'no'; ?></td>
        </tr>
    </table>
<?php } else { ?>
    <p>Product not found.</p>
<?php } ?>

<?php
mysqli_close($con);
?>
