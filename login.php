<?php
include 'config.php';   // Uključivanje konekcije sa bazom podataka
session_start();       // Početak sesije

// Provjera login forme
if(isset($_POST['submit'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

    $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$pass'") or die('query failed');

    if(mysqli_num_rows($select_users) > 0){
        $row = mysqli_fetch_assoc($select_users);

        // Provjera tipa korisnika (administrator ili obični korisnik)
        if($row['user_type'] == 'admin'){
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['admin_email'] = $row['email'];
            $_SESSION['admin_id'] = $row['id'];
            header('location:admin.php');
        } elseif($row['user_type'] == 'user'){
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_email'] = $row['email'];
            $_SESSION['user_id'] = $row['id'];
            header('location:home.php');
        }
    } else {
        $message[] = 'Pogrešan email ili šifra!';
    }
}    
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prijava</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php               // Poruka za korisnika
if(isset($message)){
    foreach($message as $message){
        echo '<div class="message"><span>'.$message.'</span> <i class="fas fa-times" onclick="this.parentElement.style.display = `none`;"></i></div>';
    };
};
?>

<div class="form-container">        <!-- Forma za prijavu -->
    <form action="" method="post">
        <h3>Prijavite se</h3>
        <input type="email" name="email" placeholder="Upišite vašu e-mail adresu" required class="box">
        <input type="password" name="password" placeholder="Upišite vašu šifru" required class="box">
        <input type="submit" name="submit" value="Prijava" class="btn">
        <p>Nemate kreiran nalog? <a href="register(index).php">Registrujte se</a></p>
    </form>
</div>  
</body>
</html>
