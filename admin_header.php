<!-- Iskačuća poruka na vrhu -->
<?php
if(isset($message)){
    foreach($message as $msg){
        echo '<div class="message"><span>'.$msg.'</span> <i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
    }
}
?>

<header class="header"> <!-- Kreiranje administratorskog zaglavlja -->
    <div class="flex">
        <a href="admin.php" class="logo">Administratorska<span>Stranica</span></a>
        <nav class="navbar">
            <a href="admin.php">Početna</a>
            <a href="admin_products.php">Proizvodi</a>
            <a href="admin_orders.php">Narudžbe</a>
            <a href="admin_users.php">Korisnici</a>
        </nav>
        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="user-btn" class="fas fa-user"></div>
        </div>
        <div class="account-box">
            <p>Korisničko ime: <span><?php echo $_SESSION['admin_name']; ?></span></p>
            <p>Email: <span><?php echo $_SESSION['admin_email']; ?></span></p>
            <a href="logout.php" class="delete-btn">Odjava</a>
        </div>
    </div>
</header>
