<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSI nginx demo</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<header><!--# include virtual="/_component/header.php" --></header>
<div class="container">
    <nav class="left-menu"><!--# include virtual="/_component/left_menu.php" --></nav>
    <div class="content-area">
        <div class="breadcrumbs"><!--# include virtual="/_component/breadcrumbs.php" --></div>
        <h1>Use SSI to include components</h1>
        <div class="content">
            <div class="content1"><!--# include virtual="/_component/content.php" --></div>
            <div class="content2"><!--# include virtual="/_component/content.php" --></div>
            <div class="content3"><!--# include virtual="/_component/content.php" --></div>
            <div class="content4"><!--# include virtual="/_component/content.php" --></div>
        </div>
    </div>
</div>
<footer><!--# include virtual="/_component/footer.php" --></footer>
</body>
</html>
