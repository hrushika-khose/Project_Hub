<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Upload Page Loaded!</h1>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<p>Form submitted successfully.</p>";
    echo "<pre>";
    print_r($_POST);
    print_r($_FILES);
    echo "</pre>";
} else {
    echo "<p>No form data submitted.</p>";
}
?>