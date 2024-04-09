<?php
include 'config.php'; // Uključivanje konekcije sa bazom podataka
session_start();

$user_id = $_SESSION['user_id'];
if (!isset($user_id)) {
    header('location:login.php');
}

// Izmjena količine proizvoda u korpi
if (isset($_POST['update_cart'])) {
    $cart_id = $_POST['cart_id'];
    $cart_quantity = $_POST['cart_quantity'];
    mysqli_query($conn, "UPDATE `cart` SET quantity = '$cart_quantity' WHERE id = '$cart_id'") or die('query failed');
    $message[] = 'Količina proizvoda u korpi je izmijenjena!';
}

// Brisanje proizvoda iz korpe
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$delete_id'") or die('query failed');
    header('location:cart.php');
}

// Brisanje svih proizvoda iz korpe
if (isset($_GET['delete_all'])) {
    mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    header('location:cart.php');
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Korpa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<section class="shopping-cart">
    <h1 class="title">Proizvodi u vašoj korpi</h1>
    <div class="box-container">
        <?php
        $grand_total = 0;
        $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
        if (mysqli_num_rows($select_cart) > 0) {
            while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
                $sub_total = ($fetch_cart['quantity'] * $fetch_cart['price']);
                $grand_total += $sub_total;
        ?>
                <div class="box">
                    <a href="cart.php?delete=<?php echo $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('Ukloniti ovaj proizvod iz korpe?');"></a>
                    <img src="uploaded_img/<?php echo $fetch_cart['image']; ?>" alt="">
                    <div class="name"><?php echo $fetch_cart['name']; ?></div>
                    <div class="price"><?php echo $fetch_cart['price']; ?>/-KM</div>
                    <form action="" method="post">
                        <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                        <input type="number" min="1" name="cart_quantity" value="<?php echo $fetch_cart['quantity']; ?>">
                        <input type="submit" name="update_cart" value="Izmijeni" class="option-btn">
                    </form>
                    <div class="sub-total"> Ukupno: <span><?php echo $sub_total; ?>/-KM</span></div>
                </div>
        <?php
            }
        } else {
            echo '<p class="empty">Vaša korpa je prazna!</p>';
        }
        ?>
    </div>

    <div style="margin-top: 2rem; text-align:center;">
        <a href="cart.php?delete_all" class="delete-btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>" onclick="return confirm('Da li želite ukloniti sve proizvode iz korpe?');">Ukloniti sve</a>
    </div>

    <div class="cart-total">
        <p>Ukupna cijena svih proizvoda u korpi: <span><?php echo $grand_total; ?>/-KM</span></p>
        <div class="flex">
            <a href="shop.php" class="option-btn">Nastaviti sa kupovinom</a>
            <a href="checkout.php" class="btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>">Plaćanje</a>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
