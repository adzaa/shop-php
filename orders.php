<?php
include 'config.php';        // Uključivanje konekcije sa bazom podataka

session_start();

// Provjera da li je korisnik prijavljen, ako nije, preusmjeri ga na stranicu za prijavu
$user_id = $_SESSION['user_id'];
if (!isset($user_id)) {
    header('location:login.php');
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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<section class="placed-orders">

   <h1 class="title">Primljene narudžbe</h1>

   <div class="box-container">

      <?php
      // Izvlačenje narudžbi korisnika iz baze podataka
      $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE user_id = '$user_id'") or die('query failed');
      if(mysqli_num_rows($order_query) > 0){
         while($fetch_orders = mysqli_fetch_assoc($order_query)){
      ?>
      <div class="box">
         <p> Primljeno : <span><?php echo $fetch_orders['placed_on']; ?></span> </p>
         <p> Ime : <span><?php echo $fetch_orders['name']; ?></span> </p>
         <p> Broj telefona : <span><?php echo $fetch_orders['number']; ?></span> </p>
         <p> Email : <span><?php echo $fetch_orders['email']; ?></span> </p>
         <p> Adresa : <span><?php echo $fetch_orders['address']; ?></span> </p>
         <p> Način plaćanja : <span><?php echo $fetch_orders['method']; ?></span> </p>
         <p> Vaša narudžba : <span><?php echo $fetch_orders['total_products']; ?></span> </p>
         <p> Ukupna cijena : <span><?php echo $fetch_orders['total_price']; ?>/-KM</span> </p>
         <p>Status plaćanja : <span style="color:<?php if($fetch_orders['payment_status'] == 'pending'){ echo 'red'; }else{ echo 'green'; } ?>;"><?php echo $fetch_orders['payment_status']; ?></span> </p>
      </div>
      <?php
       }
      }else{
         echo '<p class="empty">Još nema primljenih narudžbi!</p>';
      }
      ?>
   </div>
</section>

<?php include 'footer.php';?>
<script src="js/script.js"></script> 
</body>
</html>
