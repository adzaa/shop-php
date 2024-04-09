<?php
include 'config.php'; // Importovanje config.php dokumenta, koji sadrži konekciju sa bazom podataka

session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:login.php'); // Ako admin_id nije postavljen u sesiji, preusmjeri korisnika na stranicu za prijavljivanje
}

// Brisanje korisnika iz baze
if (isset($_GET['delete'])) { // Provjera da li je proslijeđen GET parametar 'delete' koji označava brisanje korisnika
    $delete_id = $_GET['delete']; // Dobijanje ID-ja korisnika za brisanje iz GET parametra
    mysqli_query($conn, "DELETE FROM `users` WHERE id = '$delete_id'") or die('query failed'); // Brisanje korisnika sa datim ID-jem iz baze
    header('location:admin_users.php'); // Preusmjeravanje nazad na stranicu za administraciju korisnika nakon brisanja
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Korisnici</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css"> <!-- Importovanje CSS-a za stilizaciju stranice -->
</head>
<body>
<?php include 'admin_header.php'; ?> <!-- Uključivanje zaglavlja administratorske stranice -->

<section class="users"> <!-- Sekcija za prikaz korisnika iz baze -->
    <div class="box-container">
        <?php
        $select_users = mysqli_query($conn, "SELECT * FROM `users`") or die('query failed'); // Izvršavanje upita za dohvat svih korisnika iz baze
        while ($fetch_users = mysqli_fetch_assoc($select_users)) { // Prolazak kroz rezultate upita
        ?>
            <div class="box">
                <p> Korisničko ime : <span><?php echo $fetch_users['name']; ?></span></p>
                <p> Email : <span><?php echo $fetch_users['email']; ?></span></p>
                <p>Tip korisnika : <span style="color:<?php if ($fetch_users['user_type'] == 'admin') {
                                                            echo 'var(--orange)'; // Ako je korisnik administrator, boja teksta će biti narandžasta
                                                        } ?>"><?php echo $fetch_users['user_type']; ?></span></p>
                <a href="admin_users.php?delete=<?php echo $fetch_users['id']; ?>" onclick="return confirm('Da li želite obrisati ovog korisnika?');" class="delete-btn">Obriši</a>
                <!-- Link za brisanje korisnika sa odgovarajućim ID-jem, uz JavaScript potvrdu brisanja -->
            </div>
        <?php
        }
        ?>
    </div>
</section>

<script src="js/admin_script.js"></script> <!-- Importovanje JavaScript-a za funkcionalnosti administratorske stranice -->
</body>
</html>
