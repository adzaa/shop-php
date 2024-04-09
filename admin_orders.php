<?php
include 'config.php'; // Importovanje config.php dokumenta koji sadrži konekciju sa bazom podataka

session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:login.php'); // Preusmjeravanje na stranicu za prijavljivanje ako admin_id nije postavljen u sesiji
}

// Izmjena statusa plaćanja narudžbe
if (isset($_POST['update_order'])) {
    $order_update_id = $_POST['order_id'];
    $update_payment = $_POST['update_payment'];
    mysqli_query($conn, "UPDATE `orders` SET payment_status = '$update_payment' WHERE id = '$order_update_id'") or die('query failed');
    $message[] = 'Status plaćanja je izmjenjen!';
}

// Brisanje narudžbe iz baze
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `orders` WHERE id = '$delete_id'") or die('query failed');
    header('location:admin_orders.php'); // Preusmjeravanje nazad na stranicu za administraciju narudžbi
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Narudžbe</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css"> <!-- Importovanje CSS-a za stilizaciju stranice -->
</head>
<body>
<?php include 'admin_header.php'; ?> <!-- Uključivanje zaglavlja administratorske stranice -->

<section class="orders">
    <h1 class="title">Primljene narudžbe</h1>

    <div class="box-container"><!-- Kreiranje box-containera koji prikazuje podatke iz tabele orders -->
        <?php
        $select_orders = mysqli_query($conn, "SELECT * FROM `orders`") or die('query failed');
        if (mysqli_num_rows($select_orders) > 0) {
            while ($fetch_orders = mysqli_fetch_assoc($select_orders)) {
        ?>
                <div class="box">   <!-- Kreiranje kartice narudžbe -->
                    <p> User id : <span><?php echo $fetch_orders['user_id']; ?></span></p>
                    <p> Dostavljeno : <span><?php echo $fetch_orders['placed_on']; ?></span></p>
                    <p> Ime : <span><?php echo $fetch_orders['name']; ?></span></p>
                    <p> Broj : <span><?php echo $fetch_orders['number']; ?></span></p>
                    <p> Email : <span><?php echo $fetch_orders['email']; ?></span></p>
                    <p> Adresa : <span><?php echo $fetch_orders['address']; ?></span></p>
                    <p> Ukupno proizvoda : <span><?php echo $fetch_orders['total_products']; ?></span></p>
                    <p> Ukupna cijena : <span><?php echo $fetch_orders['total_price']; ?>/-KM</span></p>
                    <p> Način plaćanja : <span><?php echo $fetch_orders['method']; ?></span></p>
                    <form action="" method="post">
                        <input type="hidden" name="order_id" value="<?php echo $fetch_orders['id']; ?>">
                        <select name="update_payment">
                            <!-- Dropdown za izmjenu statusa plaćanja -->
                            <option value="" selected disabled><?php echo $fetch_orders['payment_status']; ?></option>
                            <option value="pending">Traje</option>
                            <option value="completed">Završeno</option>
                        </select>
                        <input type="submit" value="Izmjeni" name="update_order" class="option-btn">
                        <a href="admin_orders.php?delete=<?php echo $fetch_orders['id']; ?>" onclick="return confirm('Da li želite obrisati ovu narudžbu?');" class="delete-btn">Obriši</a>
                    </form>
                </div>
        <?php
            }
        } else {
            echo '<p class="empty">Još nema primljenih narudžbi!</p>';
        }
        ?>
    </div>
</section>
<script src="js/admin_script.js"></script> <!-- Importovanje JavaScript-a za funkcionalnosti administratorske stranice -->
</body>
</html>
