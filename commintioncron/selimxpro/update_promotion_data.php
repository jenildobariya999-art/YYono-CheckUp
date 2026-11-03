<?php
session_start();
if (!isset($_SESSION['unohs']) || $_SESSION['unohs'] == null) {
    header("location:index.php?msg=unauthorized");
    exit();
}

include("conn.php");

// Auto-create custom_promotion_data table if not exists
$createTableQuery = "CREATE TABLE IF NOT EXISTS `custom_promotion_data` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `yesterday_commission` DECIMAL(15,2) DEFAULT NULL,
  `this_week_commission` DECIMAL(15,2) DEFAULT NULL,
  `total_commission` DECIMAL(15,2) DEFAULT NULL,
  `direct_sub_register` INT DEFAULT NULL,
  `direct_sub_deposit_num` INT DEFAULT NULL,
  `direct_sub_deposit_amount` DECIMAL(15,2) DEFAULT NULL,
  `direct_sub_first_deposit` INT DEFAULT NULL,
  `team_sub_register` INT DEFAULT NULL,
  `team_sub_deposit_num` INT DEFAULT NULL,
  `team_sub_deposit_amount` DECIMAL(15,2) DEFAULT NULL,
  `team_sub_first_deposit` INT DEFAULT NULL,
  `sub_rebate_amount` DECIMAL(15,2) DEFAULT NULL,
  `sub_bet_count` INT DEFAULT NULL,
  `sub_bet_amount` DECIMAL(15,2) DEFAULT NULL,
  `sub_recharge_count` INT DEFAULT NULL,
  `sub_recharge_amount` DECIMAL(15,2) DEFAULT NULL,
  `sub_first_recharge_count` INT DEFAULT NULL,
  `sub_first_recharge_amount` DECIMAL(15,2) DEFAULT NULL,
  `sub_list_count` INT DEFAULT NULL,
  `auto_sub_list` TINYINT(1) DEFAULT 1,
  `custom_sub_list_json` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

mysqli_query($conn, $createTableQuery);

// Add columns if table already existed without them
@mysqli_query($conn, "ALTER TABLE `custom_promotion_data` ADD COLUMN `this_week_commission` DECIMAL(15,2) DEFAULT NULL");
@mysqli_query($conn, "ALTER TABLE `custom_promotion_data` ADD COLUMN `total_commission` DECIMAL(15,2) DEFAULT NULL");
@mysqli_query($conn, "ALTER TABLE `custom_promotion_data` ADD COLUMN `sub_list_count` INT DEFAULT NULL");
@mysqli_query($conn, "ALTER TABLE `custom_promotion_data` ADD COLUMN `auto_sub_list` TINYINT(1) DEFAULT 1");
@mysqli_query($conn, "ALTER TABLE `custom_promotion_data` ADD COLUMN `custom_sub_list_json` TEXT DEFAULT NULL");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = intval($_POST['user_id']);
    $action = isset($_POST['action']) ? $_POST['action'] : 'update';
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : "user-details.php?user=$user_id";

    if ($user_id <= 0) {
        header("Location: manage_user.php?msg=error");
        exit();
    }

    if ($action === 'reset') {
        $stmt = $conn->prepare("DELETE FROM custom_promotion_data WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        $sep = (strpos($redirect, '?') !== false) ? '&' : '?';
        header("Location: {$redirect}{$sep}msg=promoreset");
        exit();
    }

    // Helper to get null or float/int value
    function getVal($key, $isFloat = false) {
        if (!isset($_POST[$key]) || trim($_POST[$key]) === '') {
            return null;
        }
        return $isFloat ? floatval($_POST[$key]) : intval($_POST[$key]);
    }

    $yesterday_commission = getVal('yesterday_commission', true);
    $this_week_commission = getVal('this_week_commission', true);
    $total_commission = getVal('total_commission', true);

    $direct_sub_register = getVal('direct_sub_register', false);
    $direct_sub_deposit_num = getVal('direct_sub_deposit_num', false);
    $direct_sub_deposit_amount = getVal('direct_sub_deposit_amount', true);
    $direct_sub_first_deposit = getVal('direct_sub_first_deposit', false);

    $team_sub_register = getVal('team_sub_register', false);
    $team_sub_deposit_num = getVal('team_sub_deposit_num', false);
    $team_sub_deposit_amount = getVal('team_sub_deposit_amount', true);
    $team_sub_first_deposit = getVal('team_sub_first_deposit', false);

    $sub_rebate_amount = getVal('sub_rebate_amount', true);
    $sub_bet_count = getVal('sub_bet_count', false);
    $sub_bet_amount = getVal('sub_bet_amount', true);
    $sub_recharge_count = getVal('sub_recharge_count', false);
    $sub_recharge_amount = getVal('sub_recharge_amount', true);
    $sub_first_recharge_count = getVal('sub_first_recharge_count', false);
    $sub_first_recharge_amount = getVal('sub_first_recharge_amount', true);

    $sub_list_count = getVal('sub_list_count', false);
    $auto_sub_list = isset($_POST['auto_sub_list']) ? intval($_POST['auto_sub_list']) : 1;
    $custom_sub_list_json = isset($_POST['custom_sub_list_json']) && trim($_POST['custom_sub_list_json']) !== '' ? trim($_POST['custom_sub_list_json']) : null;

    $stmt = $conn->prepare("INSERT INTO custom_promotion_data (
        user_id, yesterday_commission, this_week_commission, total_commission, direct_sub_register, direct_sub_deposit_num, direct_sub_deposit_amount, direct_sub_first_deposit,
        team_sub_register, team_sub_deposit_num, team_sub_deposit_amount, team_sub_first_deposit,
        sub_rebate_amount, sub_bet_count, sub_bet_amount, sub_recharge_count, sub_recharge_amount, sub_first_recharge_count, sub_first_recharge_amount,
        sub_list_count, auto_sub_list, custom_sub_list_json
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        yesterday_commission = VALUES(yesterday_commission),
        this_week_commission = VALUES(this_week_commission),
        total_commission = VALUES(total_commission),
        direct_sub_register = VALUES(direct_sub_register),
        direct_sub_deposit_num = VALUES(direct_sub_deposit_num),
        direct_sub_deposit_amount = VALUES(direct_sub_deposit_amount),
        direct_sub_first_deposit = VALUES(direct_sub_first_deposit),
        team_sub_register = VALUES(team_sub_register),
        team_sub_deposit_num = VALUES(team_sub_deposit_num),
        team_sub_deposit_amount = VALUES(team_sub_deposit_amount),
        team_sub_first_deposit = VALUES(team_sub_first_deposit),
        sub_rebate_amount = VALUES(sub_rebate_amount),
        sub_bet_count = VALUES(sub_bet_count),
        sub_bet_amount = VALUES(sub_bet_amount),
        sub_recharge_count = VALUES(sub_recharge_count),
        sub_recharge_amount = VALUES(sub_recharge_amount),
        sub_first_recharge_count = VALUES(sub_first_recharge_count),
        sub_first_recharge_amount = VALUES(sub_first_recharge_amount),
        sub_list_count = VALUES(sub_list_count),
        auto_sub_list = VALUES(auto_sub_list),
        custom_sub_list_json = VALUES(custom_sub_list_json)
    ");

    $stmt->bind_param("idddidididididididdiis",
        $user_id, $yesterday_commission, $this_week_commission, $total_commission, $direct_sub_register, $direct_sub_deposit_num, $direct_sub_deposit_amount, $direct_sub_first_deposit,
        $team_sub_register, $team_sub_deposit_num, $team_sub_deposit_amount, $team_sub_first_deposit,
        $sub_rebate_amount, $sub_bet_count, $sub_bet_amount, $sub_recharge_count, $sub_recharge_amount, $sub_first_recharge_count, $sub_first_recharge_amount,
        $sub_list_count, $auto_sub_list, $custom_sub_list_json
    );

    if ($stmt->execute()) {
        $stmt->close();
        $sep = (strpos($redirect, '?') !== false) ? '&' : '?';
        header("Location: {$redirect}{$sep}msg=promoupdt");
        exit();
    } else {
        $stmt->close();
        $sep = (strpos($redirect, '?') !== false) ? '&' : '?';
        header("Location: {$redirect}{$sep}msg=error");
        exit();
    }
} else {
    header("Location: manage_user.php");
    exit();
}
?>
