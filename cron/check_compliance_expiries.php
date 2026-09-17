<?php
/**
 * Cron task: Check expiring compliance credentials and dispatch notifications.
 *
 * Usage:
 *   php cron/check_compliance_expiries.php [--threshold=60] [--cooldown=7] [--force]
 */

require_once dirname(__DIR__) . '/bootstrap.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Helpers\ComplianceExpiryService;

echo "[" . date('Y-m-d H:i:s') . "] Starting compliance credential expiry scan...\n";

// Parse CLI options
$options = getopt('', ['threshold::', 'cooldown::', 'force']);
$threshold = isset($options['threshold']) ? (int)$options['threshold'] : 60;
$cooldown = isset($options['force']) ? 0 : (isset($options['cooldown']) ? (int)$options['cooldown'] : 7);

echo "Settings: Expiry Threshold = {$threshold} days | Reminder Cooldown = {$cooldown} days\n";

$result = ComplianceExpiryService::dispatchReminders($threshold, $cooldown);

echo "Scan complete:\n";
echo "  - Total Expiring Documents Scanned: " . $result['total_scanned'] . "\n";
echo "  - Alerts Dispatched (Email + In-App): " . $result['dispatched'] . "\n";
echo "  - Skipped (Cooldown active): " . $result['skipped'] . "\n";

if (!empty($result['details'])) {
    echo "Dispatched Details:\n";
    foreach ($result['details'] as $item) {
        echo "  * " . $item['recipient'] . " -> " . $item['document'] . " (" . $item['days_left'] . " days left)\n";
    }
}

echo "[" . date('Y-m-d H:i:s') . "] Compliance scan finished successfully.\n";
exit(0);
