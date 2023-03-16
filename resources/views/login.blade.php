<?php
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Hash;
    $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $url = "https://api.open-meteo.com/v1/forecast?latitude=-6.21&longitude=106.85&hourly=temperature_2m";
    $hasil = "tersrah";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form action="<?php echo $link."/login-form"; ?>" method="post">
        @csrf
        Masukkan email<input type="text" name="email"><br>
        Masukkan password<input type="password" name="pass"><br>
        <input type="submit" value="Login" name="login"><br>
    </form> 
    <a href="<?php echo $link."/register"; ?>">Halaman Register</a><br>
</body>
</html>