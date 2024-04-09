<?php
// Uključivanje konfiguracionog fajla
include 'config.php';
// Pokretanje sesije
session_start();

// Provjera da li je korisnik prijavljen, ako nije, preusmjeri na login stranicu
$user_id = $_SESSION['user_id'];
if (!isset($user_id)) {
    header('location:login.php');
    exit(); // Zaustavi izvršavanje skripte nakon preusmjeravanja
}

// Dodavanje proizvoda u korpu ako je korisnik poslao formu
if (isset($_POST['add_to_cart'])) {
    // Podaci o proizvodu
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];

    // Provjera da li je proizvod već dodan u korpu
    $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($check_cart_numbers) > 0) {
        $message[] = 'Proizvod je već dodan u korpu';
    } else {
        // Ako proizvod nije već dodan u korpu, dodaj ga
        mysqli_query($conn, "INSERT INTO `cart` (user_id, name, price, quantity, image) VALUES ('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
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
    <title>Trgovina</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<!-- Kreiranje slideshow slika na stranici trgovina -->
<div class="slideshow-container">
    <!-- Slajdovi -->
</div>
<!-- Kontrole slajdova -->
<div style="text-align:center">
    <!-- Indikatori tačaka -->
</div>

<section class="products">
    <h1 class="title">Svi proizvodi</h1>
    <div class="box-container">
        <?php
        // Prikaz svih proizvoda ako postoje
        $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
        if (mysqli_num_rows($select_products) > 0) {
            while ($fetch_products = mysqli_fetch_assoc($select_products)) {
        ?>
                <!-- Forma za prikaz proizvoda -->
                <form action="" method="post" class="box">
                    <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
                    <div class="name"><?php echo $fetch_products['name']; ?></div>
                    <div class="price"><?php echo $fetch_products['price']; ?>/-KM</div>
                    <input type="number" min="1" name="product_quantity" value="1" class="qty">
                    <!-- Skrivena polja za slanje podataka o proizvodu -->
                    <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                    <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
                    <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
                    <!-- Dugme za dodavanje u korpu -->
                    <input type="submit" value="Dodaj u korpu" name="add_to_cart" class="btn">
                </form>
        <?php
            }
        } else {
            echo '<p class="empty">Još nema dodanih proizvoda!</p>';
        }
        ?>
    </div>
</section>

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
<script src="js/slideshow.js"></script>
</body>
</html>
