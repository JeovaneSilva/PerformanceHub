<?php
session_start();

if (!function_exists('alert')) {
    function alert($msg) {
        echo "<script language='JavaScript'>";
        echo "alert('$msg');";
        echo "</script>";
    }
}
?>