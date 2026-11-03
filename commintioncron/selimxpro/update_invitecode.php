<?php
include("conn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = trim($_POST['user_id']);
    $new_invite_code = trim($_POST['new_invite_code']);

    if (empty($user_id) || empty($new_invite_code)) {
        header("Location: user-details.php?user=" . urlencode($user_id) . "&msg=error");
        exit();
    }

    // Get current owncode
    $stmt_current = $conn->prepare("SELECT owncode FROM shonu_subjects WHERE id = ?");
    $stmt_current->bind_param("s", $user_id);
    $stmt_current->execute();
    $res_current = $stmt_current->get_result();
    if ($res_current->num_rows == 0) {
        header("Location: user-details.php?user=" . urlencode($user_id) . "&msg=error");
        exit();
    }
    $row_current = $res_current->fetch_assoc();
    $old_invite_code = $row_current['owncode'];

    if ($old_invite_code === $new_invite_code) {
        header("Location: user-details.php?user=" . urlencode($user_id) . "&msg=updt");
        exit();
    }

    // Check if new invite code is already taken by another user
    $stmt_check = $conn->prepare("SELECT id FROM shonu_subjects WHERE owncode = ? AND id != ?");
    $stmt_check->bind_param("ss", $new_invite_code, $user_id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();

    if ($res_check->num_rows > 0) {
        // Invite code exists
        header("Location: user-details.php?user=" . urlencode($user_id) . "&msg=invitecodeexists");
        exit();
    }

    // Update user's owncode in shonu_subjects
    $stmt_upd = $conn->prepare("UPDATE shonu_subjects SET owncode = ? WHERE id = ?");
    $stmt_upd->bind_param("ss", $new_invite_code, $user_id);
    $stmt_upd->execute();

    // Also update downlines who referred by old_invite_code
    $stmt_sub = $conn->prepare("UPDATE shonu_subjects SET code = ? WHERE code = ?");
    $stmt_sub->bind_param("ss", $new_invite_code, $old_invite_code);
    $stmt_sub->execute();

    header("Location: user-details.php?user=" . urlencode($user_id) . "&msg=updt");
    exit();
} else {
    header("Location: dashboard.php");
    exit();
}
?>
