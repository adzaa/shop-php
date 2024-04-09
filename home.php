<?php 
// Povezivanje sa bazom podataka
include 'config.php';

// Provjera sesije
session_start();
if(!isset($_SESSION['user_id'])){
    header('location:login.php');
}

// Poruka za dodavanje proizvoda u korpu
if(isset($_POST['add_to_cart'])){
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];

    $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '{$_SESSION['user_id']}'") or die('query failed');

    if(mysqli_num_rows($check_cart_numbers) > 0){
        $message[] = 'Proizvod je već dodan u korpu';
    }else{
        mysqli_query($conn, "INSERT INTO `cart` (user_id, name, price, quantity, image) VALUES ('{$_SESSION['user_id']}', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
        $message[] = 'Proizvod je dodan u korpu!';
    }
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Početna</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css"/>
</head>
<body>

<?php include 'header.php'; ?>

<section class="home">
    <div class="content" style="width: 100% !important">
        <h3>
            Dobrodošli  <?php echo $_SESSION['user_name']; ?>! <br>
            Ovo je početna strana trgovine Raw Technology. <br>
        </h3>
    </div>
</section>

<section class="products">
    <h1 class="title">U ponudi</h1>
    <div class="box-container">
        <?php
        // Prikaz proizvoda
        $select_products = mysqli_query($conn, "SELECT * FROM `products` LIMIT 6") or die('query failed');
        if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
                ?>
                <!-- Forma za dodavanje proizvoda u korpu -->
                <form action="" method="post" class="box">
                    <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
                    <div class="name"><?php echo $fetch_products['name']; ?></div>
                    <div class="price"><?php echo $fetch_products['price']; ?>/-KM</div>
                    <input type="number" min="1" name="product_quantity" value="1" class="qty">
                    <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                    <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
                    <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
                    <input type="submit" value="Dodaj u korpu" name="add_to_cart" class="btn">
                </form>
                <?php
            }
        }else{
            echo '<p class="empty">Još nema dodanih proizvoda!</p>';
        }
        ?>
    </div>

    <div class="load-more" style="margin-top: 2rem; text-align:center">
        <a href="shop.php" class="option-btn">Još proizvoda</a>
    </div>
</section>

<?php include 'footer.php';?>
<script src="js/script.js"></script>   
</body>
</html>
