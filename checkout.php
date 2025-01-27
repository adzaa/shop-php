<?php
include 'config.php'; // Uključivanje konekcije sa bazom podataka
session_start();

$user_id = $_SESSION['user_id'];
if (!isset($user_id)) {
    header('location:login.php');
}

// Funkcija za provjeru narudžbe
if (isset($_POST['order_btn'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $number = $_POST['number'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $method = mysqli_real_escape_string($conn, $_POST['method']);
    $address = mysqli_real_escape_string($conn, 'flat no. ' . $_POST['flat'] . ', ' . $_POST['street'] . ', ' . $_POST['city'] . ', ' . $_POST['country']);
    $placed_on = date('d-M-Y');

    $cart_total = 0;
    $cart_products[] = '';

    $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($cart_query) > 0) {
        while ($cart_item = mysqli_fetch_assoc($cart_query)) {
            $cart_products[] = $cart_item['name'] . ' (' . $cart_item['quantity'] . ') ';
            $sub_total = ($cart_item['price'] * $cart_item['quantity']);
            $cart_total += $sub_total;
        }
    }

    $total_products = implode(', ', $cart_products);

    $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE name = '$name' AND number = '$number' AND email = '$email' AND method = '$method' AND address = '$address' AND total_products = '$total_products' AND total_price = '$cart_total'") or die('query failed');

    if ($cart_total == 0) {
        $message[] = 'Vaša korpa je prazna';
    } else {
        if (mysqli_num_rows($order_query) > 0) {
            $message[] = 'Narudžba je već primljena!';
        } else {
            mysqli_query($conn, "INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES('$user_id', '$name', '$number', '$email', '$method', '$address', '$total_products', '$cart_total', '$placed_on')") or die('query failed');
            $message[] = 'Narudžba je uspješno primljena!';
            mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provjera</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<section class="display-order">
    <?php
    $grand_total = 0;
    $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($select_cart) > 0) {
        while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
            $total_price = ($fetch_cart['price'] * $fetch_cart['quantity']);
            $grand_total += $total_price;
    ?>
            <p> <?php echo $fetch_cart['name']; ?> <span>(<?php echo '' . $fetch_cart['price'] . '/-KM' . ' x ' . $fetch_cart['quantity']; ?>)</span> </p>
    <?php
        }
    } else {
        echo '<p class="empty">Vaša korpa je prazna</p>';
    }
    ?>
    <div class="grand-total"> Ukupna cijena: <span><?php echo $grand_total; ?>/-KM</span></div>
</section>

<section class="checkout">
    <!-- Forma za naručivanje -->
    <form action="" method="post">
        <h3>Napravite narudžbu</h3>
        <div class="flex">
            <div class="inputBox">
                <span>Vaše ime:</span>
                <input type="text" name="name" required placeholder="Upišite Vaše ime">
            </div>
            <div class="inputBox">
                <span>Vaš broj telefona:</span>
                <input type="number" name="number" required placeholder="Upišite Vaš broj telefona">
            </div>
            <div class="inputBox">
                <span>Vaša email adresa:</span>
                <input type="email" name="email" required placeholder="Upišite Vašu email adresu">
            </div>
            <div class="inputBox">
                <span>Način plaćanja:</span>
                <select name="method">
                    <option value="cash on delivery">Pouzećem</option>
                    <option value="credit card">Kreditna kartica</option>
                    <option value="paypal">PayPal</option>
                </select>
            </div>
            <div class="inputBox">
                <span>Adresa 01:</span>
                <input type="number" min="0" name="flat" required placeholder="Broj">
            </div>
            <div class="inputBox">
                <span>Adresa 02:</span>
                <input type="text" name="street" required placeholder="Naziv ulice">
            </div>
            <div class="inputBox">
                <span>Grad:</span>
                <input type="text" name="city" required placeholder="npr. Sarajevo">
            </div>
            <div class="inputBox">
                <span>Država:</span>
                <input type="text" name="country" required placeholder="npr. Bosna i Hercegovina">
            </div>
        </div>
        <input type="submit" value="Naruči" class="btn" name="order_btn">
    </form>
</section>

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
