<?php
$type = $_GET['type'] ?? 'faq';
if ($type == 'faq') {
    echo "<h2>Frequently Asked Questions</h2>";
} elseif ($type == 'contact') {
    echo "<h2>Contact Us</h2>";
}
?>
