<?php
/**
 * Quick debug script to clear coupon session
 * Access: /wp-content/themes/caremil/clear-coupon-session.php
 */

session_start();

echo "<h2>Session Before Clear:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Clear coupon sessions
unset($_SESSION['caremil_applied_coupon']);
unset($_SESSION['caremil_applied_coupons']);

echo "<h2>Session After Clear:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3 style='color: green;'>✅ Coupon session cleared!</h3>";
echo "<p><a href='/thanh-toan'>← Quay lại checkout</a></p>";
