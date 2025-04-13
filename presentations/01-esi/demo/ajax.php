<?php
if (!headers_sent()) {
    ob_implicit_flush();
    ob_end_flush();
    header("Cache-Control: no-cache, no-store");
    flush();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTMX Traefik Demo</title>
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/html.min.js"></script>
</head>
<body>
<header hx-get="/_component/header.php" hx-trigger="load"></header>
<div class="container">
    <nav class="left-menu" hx-get="/_component/left_menu.php" hx-trigger="load"></nav>
    <div class="content-area">
        <div class="breadcrumbs" hx-get="/_component/breadcrumbs.php" hx-trigger="load"></div>
        <h1>Use HTMX to include components</h1>
        <div class="content">
            <div class="content1" hx-get="/_component/content.php" hx-trigger="load"></div>
            <div class="content2" hx-get="/_component/content.php" hx-trigger="load"></div>
            <div class="content3" hx-get="/_component/content.php" hx-trigger="load"></div>
            <div class="content4" hx-get="/_component/content.php" hx-trigger="load"></div>
        </div>
    </div>
</div>
<footer hx-get="/_component/footer.php" hx-trigger="load"></footer>
</body>
</html>
