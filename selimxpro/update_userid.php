<?php
include("conn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_user_id = trim($_POST['old_user_id']);
    $new_user_id = trim($_POST['new_user_id']);

    if (empty($old_user_id) || empty($new_user_id) || !ctype_digit($new_user_id)) {
        header("Location: user-details.php?user=" . urlencode($old_user_id) . "&msg=error");
        exit();
    }

    if ($old_user_id === $new_user_id) {
        header("Location: user-details.php?user=" . urlencode($new_user_id) . "&msg=updt");
        exit();
    }

    // Check if new_user_id already exists in shonu_subjects
    $stmt_check = $conn->prepare("SELECT id FROM shonu_subjects WHERE id = ?");
    $stmt_check->bind_param("s", $new_user_id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();

    if ($res_check->num_rows > 0) {
        // User ID already exists
        header("Location: user-details.php?user=" . urlencode($old_user_id) . "&msg=uidexists");
        exit();
    }

    // Disable foreign key checks for clean cascading updates
    $conn->query("SET FOREIGN_KEY_CHECKS=0");

    // Helper function to safely update tables if they exist
    function safeUpdateTable($conn, $table, $column, $new_id, $old_id) {
        $check = $conn->query("SHOW TABLES LIKE '$table'");
        if ($check && $check->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE `$table` SET `$column` = ? WHERE `$column` = ?");
            if ($stmt) {
                $stmt->bind_param("ss", $new_id, $old_id);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // 1. shonu_subjects primary key
    safeUpdateTable($conn, 'shonu_subjects', 'id', $new_user_id, $old_user_id);

    // 2. Tables referencing balakedara
    $tables_balakedara = ['shonu_kaichila', 'thevani', 'hintegedukolli', 'egrahcer_sonub', 'vyavahara', 'demo', 'khate'];
    foreach ($tables_balakedara as $table) {
        safeUpdateTable($conn, $table, 'balakedara', $new_user_id, $old_user_id);
    }

    // 3. Tables referencing byabaharkarta
    $tables_byabaharkarta = [
        'bajikattuttate', 'bajikattuttate_drei', 'bajikattuttate_funf', 'bajikattuttate_zehn',
        'bajikattuttate_kemuru', 'bajikattuttate_kemuru_drei', 'bajikattuttate_kemuru_funf', 'bajikattuttate_kemuru_zehn',
        'bajikattuttate_aidudi', 'bajikattuttate_aidudi_drei', 'bajikattuttate_aidudi_funf', 'bajikattuttate_aidudi_zehn',
        'bajikattuttate_trx', 'bajikattuttate_trx3', 'bajikattuttate_trx5', 'bajikattuttate_trx10', 'bajikattuttate30',
        'abbetrec', 'abbetting', 'crashbetrecord'
    ];
    foreach ($tables_byabaharkarta as $table) {
        safeUpdateTable($conn, $table, 'byabaharkarta', $new_user_id, $old_user_id);
    }

    // 4. Tables referencing user_id
    $tables_user_id = ['custom_promotion_data', 'spinrec', 'notification', 'user_extra_funds', 'banned_users'];
    foreach ($tables_user_id as $table) {
        safeUpdateTable($conn, $table, 'user_id', $new_user_id, $old_user_id);
    }

    // 5. Tables referencing userkani
    safeUpdateTable($conn, 'hodike_balakedara', 'userkani', $new_user_id, $old_user_id);

    // 6. Tables referencing userid
    safeUpdateTable($conn, 'vip', 'userid', $new_user_id, $old_user_id);

    // Re-enable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS=1");

    header("Location: user-details.php?user=" . urlencode($new_user_id) . "&msg=updt");
    exit();
} else {
    header("Location: dashboard.php");
    exit();
}
?>
