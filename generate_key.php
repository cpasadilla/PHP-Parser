<?php
// Generate a new Laravel application key
echo "Generated APP_KEY: base64:" . base64_encode(random_bytes(32)) . "\n";
?>
