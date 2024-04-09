<?php
include 'config.php';   // Uključivanje konekcije sa bazom podataka

// Provjera da li je forma za registraciju poslata
if (isset($_POST['submit'])) {
    // Čišćenje i sanitizacija unosa korisnika
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
    $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));
    $user_type = $_POST['user_type'];

    // Provjera da li korisnik sa istim emailom i šifrom već postoji u bazi
    $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$pass'") or die('query failed');

    // Ako korisnik već postoji, dodaj poruku o grešci
    if (mysqli_num_rows($select_users) > 0) {
        $message[] = 'Korisnik već postoji!';
    } else {
        // Ako šifra i potvrda šifre nisu iste, dodaj poruku o grešci
        if ($pass != $cpass) {
            $message[] = 'Šifra se ne podudara!';
        } else {
            // Inače, dodaj novog korisnika u bazu
            mysqli_query($conn, "INSERT INTO `users` (name, email, password, user_type) VALUES ('$name', '$email', '$cpass', '$user_type')") or die('query failed');
            $message[] = 'Uspješno ste se registrovali!';
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
    <title>Registracija</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php // Ispisivanje poruka za korisnika ako postoje
if (isset($message)) {
    foreach ($message as $message) {
        echo '<div class="message"><span>' . $message . '</span> <i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
    }
}
?>

<div class="form-container">    <!-- Forma za registraciju -->
    <form action="" method="post">
        <h3>Registrujte se</h3>
        <input type="text" name="name" placeholder="Upišite vaše ime" required class="box">
        <input type="email" name="email" placeholder="Upišite vašu e-mail adresu" required class="box">
        <input type="password" name="password" placeholder="Upišite vašu šifru" required class="box">
        <input type="password" name="cpassword" placeholder="Potvrdite vašu šifru" required class="box">
        <select name="user_type" class="box">
            <option value="user">Korisnik</option>
            <option value="admin">Administrator</option>
        </select>
        <input type="submit" name="submit" value="Registrujte se" class="btn">
        <p>Već imam kreiran nalog? <a href="login.php">Prijavite se</a></p>
    </form>
</div>  

</body>
</html>
