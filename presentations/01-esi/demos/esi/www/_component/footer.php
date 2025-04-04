<?php
if (!headers_sent()) {
    header("Cache-Control: public, max-age=10");
}
usleep(100000); echo "Footer";