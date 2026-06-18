<?php
if ($_REQUEST["act"] == "logout") {
    session_destroy();
    header("Location: /");
    exit;
}
