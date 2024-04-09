<?php 
// Provjera postojanja poruka i njihov prikaz
if(isset($message)){
    foreach($message as $msg){
        echo '<div class="message"><span>'.$msg.'</span> <i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
    };
};
?>

<!-- 
    Ovaj kod je header koji se koristi na svim stranicama trgovine. 
    U ovom headeru se nalazi navigacija, korisnički podaci, ikone za pretragu, korpu i korisnički meni.
    Ukoliko korisnik nije prijavljen, u headeru se nalazi link za prijavu i registraciju.
    Ukoliko je korisnik prijavljen, u headeru se nalazi korisničko ime i email korisnika.
-->
<header class="header">
    <div class="header-1">
        <div class="flex">
            <div class="share">
                <a href="#" class="fab fa-facebook"></a>
                <a href="#" class="fab fa-twitter"></a>
                <a href="#" class="fab fa-linkedin"></a>
            </div>
            <p>
                <?php
                // Provjera da li je korisnik prijavljen i prikaz odgovarajućih podataka
                if (isset($_SESSION['user_name'])) {
                    echo '<span>' . $_SESSION['user_name'] . '</span>';
                } else {
                    echo '<a href="login.php">Prijava</a>';
                }
                ?>
                | <a href="register(index).php">Registracija</a>
            </p>        
        </div>
    </div>
    <div class="header-2">
        <div class="flex">
            <!-- Logo i navigacija -->
            <a href="home.php" class="logo">Raw Tehnology</a>
            <nav class="navbar">
                <a href="home.php">Početna</a>
                <a href="shop.php">Trgovina</a>
                <a href="orders.php">Narudžbe</a>
            </nav>
            <!-- Ikone za pretragu, korpu i korisnički meni -->
            <div class="icons">
                <div id="menu-btn" class="fas fa-bars"></div>
                <a href="search_page.php" class="fas fa-search"></a>
                <div id="user-btn" class="fas fa-user"></div>
                <?php
                    // Broj proizvoda u korpi
                    $select_cart_number = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id='{$_SESSION['user_id']}'") or die('query failed');
                    $cart_rows_number = mysqli_num_rows($select_cart_number);
                ?>
                <!-- Prikaz broja proizvoda u korpi -->
                <a href="cart.php"> <i class="fas fa-shopping-cart"></i> <span>(<?php echo $cart_rows_number; ?>)</span></a>
            </div>
            <!-- Korisnički meni sa podacima korisnika -->
            <div class="user-box">
                <p>Korisničko ime : <span><?php echo $_SESSION['user_name']; ?></span></p>
                <p>Email : <span><?php echo $_SESSION['user_email']; ?></span></p>
                <a href="logout.php" class="delete-btn">Odjava</a>
            </div>
        </div>
    </div>
</header>
