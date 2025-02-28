<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login-Admin</title>
    <link rel="stylesheet" href="view/style.css">
</head>
<body>
    <div class="main"></div>
    <h2>LOGIN</h2>
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
        <input type="text" name="user" id="">
        <input type="text" name="pass" id="">
        <input type="submit" name="dangnhap" value="ĐĂng NHẬP">
    </form>    
</body>
</html>