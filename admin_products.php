<?php
include 'config.php'; // Importovanje config.php dokumenta koji sadrži konekciju sa bazom podataka

session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:login.php'); // Preusmjeravanje na stranicu za prijavljivanje ako admin_id nije postavljen u sesiji
}

// Dodavanje proizvoda u bazu
if (isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;

    // Provjera da li već postoji proizvod sa istim imenom
    $select_product_name = mysqli_query($conn, "SELECT name FROM `products` WHERE name = '$name'") or die('query failed');

    if (mysqli_num_rows($select_product_name) > 0) {
        $message[] = 'Naziv proizvoda već dodan';
    } else {
        // Dodavanje informacija o proizvodu u bazu
        $add_product_query = mysqli_query($conn, "INSERT INTO `products` (name, price, image) VALUES ('$name', '$price', '$image')") or die('query failed');

        if ($add_product_query) {
            // Provjera veličine slike i njeno prebacivanje u odgovarajući folder
            if ($image_size > 2000000) {
                $message[] = 'Veličina slike je prevelika';
            } else {
                move_uploaded_file($image_tmp_name, $image_folder);
                $message[] = 'Proizvod je uspješno dodan u bazu!';
            }
        } else {
            $message[] = 'Proizvod ne može biti dodan u bazu!';
        }
    }
}

// Brisanje proizvoda iz baze
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_image_query = mysqli_query($conn, "SELECT image FROM `products` WHERE id = '$delete_id'") or die('query failed');
    $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
    unlink('uploaded_img/' . $fetch_delete_image['image']); // Brisanje slike iz foldera
    mysqli_query($conn, "DELETE FROM `products` WHERE id = '$delete_id'") or die('query failed');
    header('location:admin_products.php'); // Preusmjeravanje nazad na stranicu za administraciju proizvoda
}

// Izmena proizvoda u bazi
if (isset($_POST['update_product'])) {
    $update_p_id = $_POST['update_p_id'];
    $update_name = $_POST['update_name'];
    $update_price = $_POST['update_price'];

    // Izmena naziva i cene proizvoda
    mysqli_query($conn, "UPDATE `products` SET name = '$update_name', price ='$update_price' WHERE id = '$update_p_id'") or die('query failed');

    $update_image = $_FILES['update_image']['name'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_folder = 'uploaded_img/' . $update_image;
    $update_old_image = $_POST['update_old_image'];

    // Ažuriranje slike proizvoda ako je izabrana nova slika
    if (!empty($update_image)) {
        if ($update_image_size > 2000000) {
            $message[] = 'Veličina slike je prevelika';
        } else {
            mysqli_query($conn, "UPDATE `products` SET image = '$update_image' WHERE id = '$update_p_id'") or die('query failed');
            move_uploaded_file($update_image_tmp_name, $update_folder);
            unlink('uploaded_img/' . $update_old_image); // Brisanje stare slike iz foldera
        }
    }
    header('location:admin_products.php'); // Preusmjeravanje nazad na stranicu za administraciju proizvoda
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proizvodi</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css"> <!-- Importovanje CSS-a za stilizaciju stranice -->
</head>
<body>
<?php include 'admin_header.php'; ?> <!-- Uključivanje zaglavlja administratorske stranice -->

<section class="add-products">
    <h1 class="title">proizvodi</h1>

    <form action="" method="post" enctype="multipart/form-data">
        <!-- Forma za dodavanje proizvoda -->
        <h3>dodaj proizvod</h3>
        <input type="text" name="name" class="box" placeholder="Unesi naziv proizvoda" required>
        <input type="number" min="0" name="price" class="box" placeholder="Unesi cijenu proizvoda" required>
        <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
        <input type="submit" value="Dodaj proizvod" name="add_product" class="btn">
    </form>
</section>

<section class="show-products">
    <!-- Prikaz svih proizvoda iz baze -->
    <div class="box-container">
        <?php
        $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
        if (mysqli_num_rows($select_products) > 0) {
            while ($fetch_products = mysqli_fetch_assoc($select_products)) {
        ?>
                <div class="box">
                    <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
                    <div class="name"><?php echo $fetch_products['name']; ?></div>
                    <div class="price"><?php echo $fetch_products['price']; ?>/-KM</div>
                    <a href="admin_products.php?update=<?php echo $fetch_products['id']; ?>" class="option-btn">Izmeni</a>
                    <a href="admin_products.php?delete=<?php echo $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Da li želite obrisati ovaj proizvod?');">Obriši</a>
                </div>
        <?php
            }
        } else {
            echo '<p class="empty">Nema dodanih proizvoda!</p>';
        }
        ?>
    </div>
</section>

<section class="edit-product-form">
    <!-- Forma za izmenu proizvoda -->
    <?php
    if (isset($_GET['update'])) {
        $update_id = $_GET['update'];
        $update_query = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$update_id'") or die('query failed');
        if (mysqli_num_rows($update_query) > 0) {
            while ($fetch_update = mysqli_fetch_assoc($update_query)) {
    ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
                    <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['image']; ?>">
                    <img src="uploaded_img/<?php echo $fetch_update['image']; ?>" alt="">
                    <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required placeholder="Unesi naziv proizvoda">
                    <input type="number" name="update_price" value="<?php echo $fetch_update['price']; ?>" min="0" class="box" required placeholder="Unesi cenu proizvoda">
                    <input type="file" class="box" name="update_image" accept="image/jpg, image/jpeg, image/png">
                    <input type="submit" value="Izmeni" name="update_product" class="btn">
                    <input type="reset" value="Poništi" id="close-update" class="option-btn">
                </form>
    <?php
            }
        }
    } else {
        echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
    }
    ?>
</section>

<script src="js/admin_script.js"></script> <!-- Importovanje JavaScript-a za funkcionalnosti administratorske stranice -->
</body>
</html>
