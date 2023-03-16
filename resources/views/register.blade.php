<?php 
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\DB;
    $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    try{
        }catch(Exception $e){
            $pError = $e->getMessage();
            $e->getTrace();
            echo "<script type='text/javascript'>alert('$pError');</script>";
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <form action="<?php echo $link."/register-form"; ?>" method="post">
        @csrf
        Masukkan username<input type="text" name="username"><br>
        Masukkan email<input type="text" name="email"><br>
        Masukkan Nama Lengkap<input type="text" name="nama"><br>
        Masukkan password<input type="password" name="pass"><br>
        Masukkan password lagi<input type="password" name="pass_new"><br>
        <input type="submit" value="Register" name="register">
    </form>
    <a href="<?php echo $link."/login"; ?>">Halaman Login</a><br>
</body>
</html>