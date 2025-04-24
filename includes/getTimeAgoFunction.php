<?php
function getTimeAgo($oracleTimestamp) {
    $uploadTime = new DateTime($oracleTimestamp);
    $now = new DateTime();

    $diff = $now->getTimestamp() - $uploadTime->getTimestamp();

    if ($diff < 60) return $diff . " másodperce";
    if ($diff < 3600) return floor($diff / 60) . " perce";
    if ($diff < 86400) return floor($diff / 3600) . " órája";
    return floor($diff / 86400) . " napja";
}
?><?php
