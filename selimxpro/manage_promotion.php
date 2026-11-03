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
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($conn, $createTableQuery);

$search_query = "";
$where_clause = "";
if (isset($_GET['search']) && trim($_GET['search']) != "") {
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
    $where_clause = " WHERE mobile LIKE '%$search%' OR id LIKE '%$search%' OR owncode LIKE '%$search%' ";
}

$users_result = mysqli_query($conn, "SELECT id, mobile, owncode, code, createdate FROM shonu_subjects $where_clause ORDER BY id DESC LIMIT 50");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Manage Promotion & Subordinates Data</title>
  <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="vendors/feather/feather.css">
  <link rel="stylesheet" href="vendors/base/vendor.bundle.base.css">
  <link rel="stylesheet" href="vendors/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="shortcut icon" href="images/favicon.png" />
</head>
<body>
  <div class="container-scroller">
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo" href="dashboard.php"><img src="images/logo.png" alt="logo"/></a>
        <a class="navbar-brand brand-logo-mini" href="dashboard.php"><img src="images/logo-mini.png" alt="logo"/></a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button>       
        <ul class="navbar-nav navbar-nav-right">           
          <li class="nav-item dropdown d-flex mr-4 ">
            <a class="nav-link count-indicator dropdown-toggle d-flex align-items-center justify-content-center" id="notificationDropdown" href="#" data-toggle="dropdown">
              <i class="icon-cog"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
              <p class="mb-0 font-weight-normal float-left dropdown-header">Settings</p>              
              <a class="dropdown-item preview-item" href="logout.php">
                  <i class="icon-inbox"></i> Logout
              </a>
            </div>
          </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>

    <div class="container-fluid page-body-wrapper">
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <div class="user-profile">
          <div class="user-image">
            <img src="images/faces/face28.png">
          </div>
          <div class="user-name">selim</div>
          <div class="user-designation">Admin</div>
        </div>
        <?php include 'compass.php';?>
      </nav>

      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row mb-3">
            <div class="col-sm-12">
              <h3 class="font-weight-bold text-dark"><i class="fa fa-bullhorn"></i> Manage Promotion & Subordinates Data</h3>
              <?php if (isset($_GET['msg']) && $_GET['msg'] == 'promoupdt') { ?>
                <div class="alert alert-success">Promotion & Subordinates Data updated successfully!</div>
              <?php } else if (isset($_GET['msg']) && $_GET['msg'] == 'promoreset') { ?>
                <div class="alert alert-info">Promotion Data reset to automatic calculation.</div>
              <?php } ?>
            </div>
          </div>

          <!-- Search Filter -->
          <div class="row mb-4">
            <div class="col-md-6">
              <form method="GET" action="manage_promotion.php" class="form-inline">
                <input type="text" name="search" class="form-control mr-2" style="width:70%;" placeholder="Search by Mobile, User ID or Code..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                <?php if (isset($_GET['search'])) { ?>
                  <a href="manage_promotion.php" class="btn btn-secondary ml-2">Clear</a>
                <?php } ?>
              </form>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr class="bg-dark text-white">
                      <th>User ID</th>
                      <th>Mobile</th>
                      <th>Own Code</th>
                      <th>Referred By</th>
                      <th>Custom Promotion Data Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    if (mysqli_num_rows($users_result) > 0) {
                      while ($u = mysqli_fetch_assoc($users_result)) {
                        $uid = $u['id'];
                        $chk = mysqli_query($conn, "SELECT id FROM custom_promotion_data WHERE user_id = '$uid'");
                        $hasCustom = (mysqli_num_rows($chk) > 0);
                    ?>
                      <tr>
                        <td><b><?php echo $u['id']; ?></b></td>
                        <td><?php echo $u['mobile']; ?></td>
                        <td><code><?php echo $u['owncode']; ?></code></td>
                        <td><?php echo $u['code']; ?></td>
                        <td>
                          <?php if ($hasCustom) { ?>
                            <span class="badge badge-success">Custom Modified</span>
                          <?php } else { ?>
                            <span class="badge badge-secondary">Automatic</span>
                          <?php } ?>
                        </td>
                        <td>
                          <a href="user-details.php?user=<?php echo $u['id']; ?>" class="btn btn-sm btn-info">
                            <i class="fa fa-edit"></i> Edit Promotion & Subordinates Data
                          </a>
                        </td>
                      </tr>
                    <?php 
                      }
                    } else {
                    ?>
                      <tr>
                        <td colspan="6" class="text-center">No users found.</td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © selim 2025</span>
          </div>
        </footer>
      </div>
    </div>
  </div>

  <script src="vendors/base/vendor.bundle.base.js"></script>
  <script src="js/off-canvas.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
</body>
</html>
