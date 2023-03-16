<?php
    $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dashboard</title>
</head>
<body>
    <b1>ncadcnkanvknknakvnknvknakvnanknkbnblbmlwnwnvkwekbebfubfuwfwhfw</b1>
    <a href="<?php echo $link."/register"; ?>">Halaman Register</a><br>
    <a href="<?php echo $link."/login"; ?>">Halaman Login</a><br>
    <a href="<?php echo $link."/user/config"; ?>">Halaman Pengaturan</a><br>
    <p>ickankbvjajdszdxfhjkhgyfsesrtyuijohugf</p>
    {{-- <form action="<?php echo $link."/logout"; ?>" method="post">
        <input type="submit" value="Logout">
    </form> --}}
    <form action="<?php echo $link."/logout"; ?>" method="post">
        @csrf
        <input type="submit" value="Logout">
    </form>
</body>
</html>