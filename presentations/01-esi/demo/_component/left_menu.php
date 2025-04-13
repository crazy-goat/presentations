<?php
if (!headers_sent()) {
    header("Cache-Control: public, max-age=10");
}
usleep(100000);?>
Left Menu

<p><a href="/">Standard</a></p>
<p><a href="/ajax.php">Ajax</a></p>
<p><a href="/ssi.php">SSI</a></p>
<p><a href="/esi.php">ESI</a></p>