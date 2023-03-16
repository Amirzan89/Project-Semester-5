<?php
    $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <form action="<?php echo $link."/api/weather"; ?>">
        <input type="submit">
    </form>
    <a href="<?php echo $link."/register"; ?>">Halaman Register</a><br>
    <a href="<?php echo $link."/login"; ?>">Halaman Login</a><br>
</body>
</html>