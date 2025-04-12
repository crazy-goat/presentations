<?php
if (!headers_sent()) {
    ob_implicit_flush();
    ob_end_flush();
    header("Cache-Control: no-cache, no-store");
    flush();
}?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESI traefik demo</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<header><esi:include src="http://127.0.0.1:8080/_component/header.php" /></header>
<div class="container">
    <nav class="left-menu"><esi:include src="http://127.0.0.1:8080/_component/left_menu.php" /></nav>
    <div class="content-area">
        <div class="breadcrumbs"><esi:include src="http://127.0.0.1:8080/_component/breadcrumbs.php" /></div>
        <h1>Use ESI to include components</h1>
        <div class="content">
            <div class="content1"><esi:include src="http://127.0.0.1:8080/_component/content.php" /></div>
            <div class="content2"><esi:include src="http://127.0.0.1:8080/_component/content.php" /></div>
            <div class="content3"><esi:include src="http://127.0.0.1:8080/_component/content.php" /></div>
            <div class="content4"><esi:include src="http://127.0.0.1:8080/_component/content.php" /></div>
        </div>
    </div>
</div>
<footer><esi:include src="http://127.0.0.1:8080/_component/footer.php" /></footer>
</body>
</html>
