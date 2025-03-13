<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSI nginx demo</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<header><?php include "_component/header.php" ?></header>
<div class="container">
    <nav class="left-menu"><?php include "_component/left_menu.php" ?>
    </nav>
    <div class="content-area">
        <div class="breadcrumbs"><?php include "_component/breadcrumbs.php" ?></div>
        <h1>Use standard components include</h1>
        <div class="content">
            <div class="content1"><?php include "_component/content.php" ?></div>
            <div class="content2"><?php include "_component/content.php" ?></div>
            <div class="content3"><?php include "_component/content.php" ?></div>
            <div class="content4"><?php include "_component/content.php" ?></div>
        </div>
    </div>
</div>
<footer><?php include "_component/footer.php" ?></footer>
</body>
</html>
