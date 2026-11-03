<?php
	session_start();
	if($_SESSION['unohs'] == null){
		header("location:index.php?msg=unauthorized");
	}
?>
<?php
	include ("conn.php");
	
	$userid = $_GET['user'];
	
	$snum = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `shonu_subjects` WHERE `id` = '".$userid."' "));
	$owncode = $snum['owncode'];
	
	$balquery = "SELECT motta
	  FROM shonu_kaichila
	  WHERE balakedara = ".$userid;
	$balresult = $conn->query($balquery);
	$balarr = mysqli_fetch_array($balresult);
	$total_balance = $balarr['motta'];
	
	$total_refer = mysqli_fetch_assoc(mysqli_query($conn,"SELECT count(id) as total FROM `shonu_subjects` where code = '".$owncode."'"));
	
	$bet_wingo_1 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT sum(ketebida) as total FROM `bajikattuttate` where byabaharkarta = '".$userid."'"));
	$bet_wingo_3 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT sum(ketebida) as total FROM `bajikattuttate_drei` where byabaharkarta = '".$userid."'"));
	$bet_wingo_5 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT sum(ketebida) as total FROM `bajikattuttate_funf` where byabaharkarta = '".$userid."'"));
	$bet_wingo_10 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT sum(ketebida) as total FROM `bajikattuttate_zehn` where byabaharkarta = '".$userid."'"));
	$bet_k3_1 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT sum(ketebida) as total FROM `bajikattuttate_kemuru` where byabaharkarta = '".$userid."'"));
	$bet_k3_3 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT sum(ketebida) as total FROM `bajikattuttate_kemuru_drei` where byabaharkarta = '".$userid."'"));
	$bet_k3_5 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT sum(ketebida) as total FROM `bajikattuttate_kemuru_funf` where byabaharkarta = '".$userid."'"));
	$bet_k3_10 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT sum(ketebida) as total FROM `bajikattuttate_kemuru_zehn` where byabaharkarta = '".$userid."'"));
	$bet_5d_1 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT sum(ketebida) as total FROM `bajikattuttate_aidudi` where byabaharkarta = '".$userid."'"));
	$bet_5d_3 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT sum(ketebida) as total FROM `bajikattuttate_aidudi_drei` where byabaharkarta = '".$userid."'"));
	$bet_5d_5 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT sum(ketebida) as total FROM `bajikattuttate_aidudi_funf` where byabaharkarta = '".$userid."'"));
	$bet_5d_10 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT sum(ketebida) as total FROM `bajikattuttate_aidudi_zehn` where byabaharkarta = '".$userid."'"));
	$total_bet = $bet_wingo_1['total'] + $bet_wingo_3['total'] + $bet_wingo_5['total'] + $bet_wingo_10['total'] + $bet_k3_1['total'] + $bet_k3_3['total'] + $bet_k3_5['total'] + $bet_k3_10['total'] + $bet_5d_1['total'] + $bet_5d_3['total'] + $bet_5d_5['total'] + $bet_5d_10['total'];
	
	$total_recharge = mysqli_fetch_assoc(mysqli_query($conn,"SELECT sum(motta) as total FROM `thevani` WHERE `sthiti` = '1' AND `balakedara` = '".$userid."'"));
	
	$total_withdraw = mysqli_fetch_assoc(mysqli_query($conn,"SELECT sum(motta) as total FROM `hintegedukolli` WHERE sthiti = 1 AND balakedara = '".$userid."'"));
	
	$total_reward = mysqli_fetch_assoc(mysqli_query($conn,"SELECT sum(price) as total FROM `hodike_balakedara` where userkani = '".$userid."'"));
	
	//$wingo = mysqli_fetch_assoc(mysqli_query($conn,"SELECT *  FROM `bajikattuttate_zehn` where byabaharkarta = '".$userid."'"));


	$refer_record = mysqli_query($conn,"SELECT * FROM `shonu_subjects` where code = '".$owncode."' ORDER BY id DESC");
	
	$deposit_record = mysqli_query($conn,"SELECT * FROM `thevani` WHERE `balakedara` = '".$userid."' ORDER BY shonu DESC");
	
	$withdraw_record = mysqli_query($conn,"SELECT * FROM `hintegedukolli` where balakedara = '".$userid."' ORDER BY shonu DESC");
	
	$rbxquery = "SELECT SUM(ayoga) as sumayoga
	  FROM vyavahara
	  WHERE balakedara = '".$userid."' AND (prakara = 'LVLCOMM1' OR prakara = 'LVLCOMM2' OR prakara = 'LVLCOMM3' OR prakara = 'LVLCOMM4' OR prakara = 'LVLCOMM5' OR prakara = 'LVLCOMM6')";
	$rbxresult = $conn->query($rbxquery);
	$rbxar = mysqli_fetch_array($rbxresult);
	$sumayoga = (float)$rbxar['sumayoga'];

	// Custom promotion data query
	$cp_query = mysqli_query($conn, "SELECT * FROM `custom_promotion_data` WHERE `user_id` = '".$userid."'");
	$cp_data = ($cp_query && mysqli_num_rows($cp_query) > 0) ? mysqli_fetch_assoc($cp_query) : array();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Dashboard</title>
  <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="vendors/feather/feather.css">
  <link rel="stylesheet" href="vendors/base/vendor.bundle.base.css">
  <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css"/>
  <link rel="stylesheet" href="vendors/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="vendors/jquery-bar-rating/fontawesome-stars-o.css">
  <link rel="stylesheet" href="vendors/jquery-bar-rating/fontawesome-stars.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.2.3/css/fixedHeader.dataTables.min.css">
  <link rel="shortcut icon" href="images/favicon.png" />
  <style>
	.cool-input {
        border: 2px solid #007bff;
        border-radius: 0.25rem;
        padding: 0.5rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    .cool-input:focus {
        border-color: #0056b3;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .cool-input::placeholder {
        color: #6c757d;
        opacity: 1;
    }
	.cool-button {
        padding: 0.5rem 1rem;
        font-size: 1rem;
        border-radius: 0.25rem;
        transition: all 0.3s ease;
    }
    .cool-button:hover {
        background-color: #0056b3;
        color: #fff;
    }
    .cool-button.btn-secondary:hover {
        background-color: #343a40;
        color: #fff;
    }
	#copied{
		visibility: hidden;
		z-index: 1;
		position: fixed;
		bottom: 50%;
		background-color: #333;
		color: #fff;
		border-radius: 6px;
		padding: 16px;
		max-width: 250px;
		font-size: 17px;
	}	   
	#copied.show {
		visibility: visible;
		-webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
		animation: fadein 0.5s, fadeout 0.5s 2.5s;
	}
  </style>
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
          <div class="user-name">
              selim
          </div>
          <div class="user-designation">
              Admin
          </div>
        </div>
        <?php include 'compass.php';?>
      </nav>
      <div class="main-panel">
        <div class="content-wrapper">
			<div class="row">
				<div class="box-header box-header2 align-middle">
					<div class="col-xs-6 text-right">
					<h3 class="box-title"><?php 
						if(isset($_GET['msg']) && $_GET['msg']=="updt") 
						{ ?>
						<font size="+1" color="#008000">Updated Successfully...</font>
						<?php  } ?>
						<?php if(isset($_GET['msg']) && $_GET['msg']=="promoupdt") { ?>
						<font size="+1" color="#008000">Promotion & Subordinate Data Updated Successfully!</font>
						<?php } ?>
						<?php if(isset($_GET['msg']) && $_GET['msg']=="promoreset") { ?>
						<font size="+1" color="#008000">Promotion Data Reset to Automatic Calculation!</font>
						<?php } ?>
						<?php if(isset($_GET['msg']) && $_GET['msg']=="uidexists") { ?>
						<font size="+1" color="#FF0000">Error: User ID already exists!</font>
						<?php } ?>
						<?php if(isset($_GET['msg']) && $_GET['msg']=="invitecodeexists") { ?>
						<font size="+1" color="#FF0000">Error: Invite Code already exists!</font>
						<?php } ?>
						<?php if(isset($_GET['msg']) && $_GET['msg']=="error") { ?>
						<font size="+1" color="#FF0000">Error updating data!</font>
						<?php } ?>
					</h3>
					</div>
					<div class="col-sm-6">
						<div class="pull-right">&nbsp;</div>
					</div>		  
				</div>
			</div>
          <div class="row">
            <div class="col-sm-12 mb-4 mb-xl-0">
              <h4 class="font-weight-bold text-dark">User Details</h4>
            </div>
          </div> 		  		  		  			
		  <div class="row">
			<div class="col-lg-6 col-sm-6 col-xs-6">
				<ul>
					<li>User Id : 
						<!-- Editable "User Id" Field -->
						<form action="update_userid.php" method="POST">
							<input type="text" name="new_user_id" value="<?php echo $snum['id']; ?>" class="cool-input" required>
							<input type="hidden" name="old_user_id" value="<?php echo $snum['id']; ?>">
							<button type="submit" class="cool-button">Update</button>
						</form>
					</li>
					<li>Referal Id : 
						<!-- Editable "Referal Id / Invite Code" Field -->
						<form action="update_invitecode.php" method="POST">
							<input type="text" name="new_invite_code" value="<?php echo $snum['owncode']; ?>" class="cool-input" required>
							<input type="hidden" name="user_id" value="<?php echo $snum['id']; ?>">
							<button type="submit" class="cool-button">Update</button>
						</form>
					</li>						
					<li>IP Address : <?php echo "<b>".$snum['ishonup']."</b>"; ?></li>
                  
                  
                  
                  	<li>User Password : <form action="update_userpass.php" method="POST">
                    <input type="text" name="resetpsw"  class="cool-input" required>
                    <input type="hidden" name="user_id" value="<?php echo $snum['id']; ?>">
                    <button type="submit" class="cool-button">Update</button>
                </form></li>
                  
                  
                  
				</ul>
			</div>
			<div class="col-lg-6 col-sm-6 col-xs-6">
				 <ul>
            <li>Phone No. : <b><?php echo $snum['mobile']; ?> </b> </li>
            <li>Created At : <?php echo "<b>".$snum['createdate']."</b>"; ?></li>
            <li>Referred By : 
                <!-- Editable "Referred By" Field -->
                <form action="update_reffcode.php" method="POST">
                    <input type="text" name="referred_by" value="<?php echo $snum['code']; ?>" class="cool-input" required>
                    <input type="hidden" name="user_id" value="<?php echo $snum['id']; ?>">
                    <button type="submit" class="cool-button">Update</button>
                </form>
            </li>
        </ul>
			</div>			
          </div>
         <!-- In the user details section where "Referred By" is displayed -->
<div class="row">
    <div class="col-lg-6 col-sm-6 col-xs-6">
        
    </div>
    <div class="col-lg-6 col-sm-6 col-xs-6">
       
    </div>			
</div>
 
         
          
          
          
		  <div class="row">
			<div class="col-md-3 col-sm-12 col-lg-3">
               <strong><a href="#total_transcation">Total Balance</a></strong>
                <h2>₹<?= round($total_balance,2);?></h2>
            </div>
			<div class="col-md-3 col-sm-12 col-lg-3">
                <strong><a href="#total_refer">Total Referral</a></strong>
                <h2><?= (float)$total_refer['total'];?></h2>
            </div>
			<div class="col-md-3 col-sm-12 col-lg-3">
                <strong><a href="#total_bet">Total Bet</a></strong>
                <h2>₹<?= round($total_bet, 2);?></h2>
            </div>
			<div class="col-md-3 col-sm-12 col-lg-3">
				<strong><a href="#total_recharge">Total Recharge</a></strong>
                <h2>₹<?= round($total_recharge['total'],2);?></h2>
            </div>
			<div class="col-md-3 col-sm-12 col-lg-3">
				<strong><a href="#total_withdrawal">Total Withdraw</a></strong>
                <h2>₹<?= round($total_withdraw['total'],2);?></h2>
            </div>
			<div class="col-md-3 col-sm-12 col-lg-3">
                <strong><a href="#total_reward">Total Gift</a></strong>
                <h2>₹<?= round($total_reward['total'],2);?></h2>
            </div>
			<div class="col-md-3 col-sm-12 col-lg-3">
                <strong><a href="#total_reward">Total Commission</a></strong>
                <h2>₹<?= $sumayoga;?></h2>
            </div>
		  </div>

		  <!-- Promotion & Subordinates Data Edit Form -->
		  <div class="row mt-4 mb-4" style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #ddd;">
		    <div class="col-md-12">
		        <h4 class="text-primary font-weight-bold mb-3"><i class="fa fa-edit"></i> Edit User Promotion & Subordinates Data</h4>
		        <form action="update_promotion_data.php" method="POST">
		            <input type="hidden" name="user_id" value="<?php echo $userid; ?>">
		            <input type="hidden" name="redirect" value="user-details.php?user=<?php echo $userid; ?>">
		            
		            <h5 class="text-info font-weight-bold mb-3"><i class="fa fa-bullhorn"></i> Promotion Page Data</h5>
		            <div class="row">
		                <div class="col-md-4 form-group">
		                    <label class="font-weight-bold">Yesterday's Total Commission (₹)</label>
		                    <input type="number" step="0.01" class="form-control cool-input" name="yesterday_commission" value="<?php echo isset($cp_data['yesterday_commission']) ? $cp_data['yesterday_commission'] : ''; ?>" placeholder="Auto calculated if empty">
		                </div>
		                <div class="col-md-4 form-group">
		                    <label class="font-weight-bold">This Week Commission (₹)</label>
		                    <input type="number" step="0.01" class="form-control cool-input" name="this_week_commission" value="<?php echo isset($cp_data['this_week_commission']) ? $cp_data['this_week_commission'] : ''; ?>" placeholder="Auto calculated if empty">
		                </div>
		                <div class="col-md-4 form-group">
		                    <label class="font-weight-bold">Total Commission (₹)</label>
		                    <input type="number" step="0.01" class="form-control cool-input" name="total_commission" value="<?php echo isset($cp_data['total_commission']) ? $cp_data['total_commission'] : ''; ?>" placeholder="Auto calculated if empty">
		                </div>
		            </div>

		            <h6 class="font-weight-bold text-dark mt-2">Direct Subordinates Stats</h6>
		            <div class="row">
		                <div class="col-md-3 form-group">
		                    <label>Register Count</label>
		                    <input type="number" class="form-control" name="direct_sub_register" value="<?php echo isset($cp_data['direct_sub_register']) ? $cp_data['direct_sub_register'] : ''; ?>" placeholder="Auto">
		                </div>
		                <div class="col-md-3 form-group">
		                    <label>Deposit Number</label>
		                    <input type="number" class="form-control" name="direct_sub_deposit_num" value="<?php echo isset($cp_data['direct_sub_deposit_num']) ? $cp_data['direct_sub_deposit_num'] : ''; ?>" placeholder="Auto">
		                </div>
		                <div class="col-md-3 form-group">
		                    <label>Deposit Amount (₹)</label>
		                    <input type="number" step="0.01" class="form-control" name="direct_sub_deposit_amount" value="<?php echo isset($cp_data['direct_sub_deposit_amount']) ? $cp_data['direct_sub_deposit_amount'] : ''; ?>" placeholder="Auto">
		                </div>
		                <div class="col-md-3 form-group">
		                    <label>1st Deposit People</label>
		                    <input type="number" class="form-control" name="direct_sub_first_deposit" value="<?php echo isset($cp_data['direct_sub_first_deposit']) ? $cp_data['direct_sub_first_deposit'] : ''; ?>" placeholder="Auto">
		                </div>
		            </div>

		            <h6 class="font-weight-bold text-dark mt-2">Team Subordinates Stats</h6>
		            <div class="row">
		                <div class="col-md-3 form-group">
		                    <label>Register Count</label>
		                    <input type="number" class="form-control" name="team_sub_register" value="<?php echo isset($cp_data['team_sub_register']) ? $cp_data['team_sub_register'] : ''; ?>" placeholder="Auto">
		                </div>
		                <div class="col-md-3 form-group">
		                    <label>Deposit Number</label>
		                    <input type="number" class="form-control" name="team_sub_deposit_num" value="<?php echo isset($cp_data['team_sub_deposit_num']) ? $cp_data['team_sub_deposit_num'] : ''; ?>" placeholder="Auto">
		                </div>
		                <div class="col-md-3 form-group">
		                    <label>Deposit Amount (₹)</label>
		                    <input type="number" step="0.01" class="form-control" name="team_sub_deposit_amount" value="<?php echo isset($cp_data['team_sub_deposit_amount']) ? $cp_data['team_sub_deposit_amount'] : ''; ?>" placeholder="Auto">
		                </div>
		                <div class="col-md-3 form-group">
		                    <label>1st Deposit People</label>
		                    <input type="number" class="form-control" name="team_sub_first_deposit" value="<?php echo isset($cp_data['team_sub_first_deposit']) ? $cp_data['team_sub_first_deposit'] : ''; ?>" placeholder="Auto">
		                </div>
		            </div>

		            <hr class="mt-3 mb-3">

		            <h5 class="text-info font-weight-bold mb-3"><i class="fa fa-users"></i> Subordinate Data Report Stats</h5>
		            <div class="row">
		                <div class="col-md-3 form-group">
		                    <label>Rebate Amount Sum (₹)</label>
		                    <input type="number" step="0.01" class="form-control" name="sub_rebate_amount" value="<?php echo isset($cp_data['sub_rebate_amount']) ? $cp_data['sub_rebate_amount'] : ''; ?>" placeholder="Auto">
		                </div>
		                <div class="col-md-3 form-group">
		                    <label>Bet Count Sum</label>
		                    <input type="number" class="form-control" name="sub_bet_count" value="<?php echo isset($cp_data['sub_bet_count']) ? $cp_data['sub_bet_count'] : ''; ?>" placeholder="Auto">
		                </div>
		                <div class="col-md-3 form-group">
		                    <label>Bet Amount Sum (₹)</label>
		                    <input type="number" step="0.01" class="form-control" name="sub_bet_amount" value="<?php echo isset($cp_data['sub_bet_amount']) ? $cp_data['sub_bet_amount'] : ''; ?>" placeholder="Auto">
		                </div>
		                <div class="col-md-3 form-group">
		                    <label>Recharge Count Sum</label>
		                    <input type="number" class="form-control" name="sub_recharge_count" value="<?php echo isset($cp_data['sub_recharge_count']) ? $cp_data['sub_recharge_count'] : ''; ?>" placeholder="Auto">
		                </div>
		                <div class="col-md-3 form-group">
		                    <label>Recharge Amount Sum (₹)</label>
		                    <input type="number" step="0.01" class="form-control" name="sub_recharge_amount" value="<?php echo isset($cp_data['sub_recharge_amount']) ? $cp_data['sub_recharge_amount'] : ''; ?>" placeholder="Auto">
		                </div>
		                <div class="col-md-3 form-group">
		                    <label>1st Recharge Count Sum</label>
		                    <input type="number" class="form-control" name="sub_first_recharge_count" value="<?php echo isset($cp_data['sub_first_recharge_count']) ? $cp_data['sub_first_recharge_count'] : ''; ?>" placeholder="Auto">
		                </div>
		                <div class="col-md-3 form-group">
		                    <label>1st Recharge Amount Sum (₹)</label>
		                    <input type="number" step="0.01" class="form-control" name="sub_first_recharge_amount" value="<?php echo isset($cp_data['sub_first_recharge_amount']) ? $cp_data['sub_first_recharge_amount'] : ''; ?>" placeholder="Auto">
		                </div>
		            </div>

		            <hr class="mt-3 mb-3">

		            <h5 class="text-info font-weight-bold mb-3"><i class="fa fa-list"></i> Subordinate List Items (Below Cards)</h5>
		            <div class="row">
		                <div class="col-md-4 form-group">
		                    <label class="font-weight-bold">Auto Random Subordinates List</label>
		                    <select class="form-control" name="auto_sub_list">
		                        <option value="1" <?php echo (!isset($cp_data['auto_sub_list']) || $cp_data['auto_sub_list'] == 1) ? 'selected' : ''; ?>>Yes - Auto Generate Random UID List</option>
		                        <option value="0" <?php echo (isset($cp_data['auto_sub_list']) && $cp_data['auto_sub_list'] == 0) ? 'selected' : ''; ?>>No - Show Real List / None</option>
		                    </select>
		                </div>
		                <div class="col-md-4 form-group">
		                    <label class="font-weight-bold">Subordinates List Count</label>
		                    <input type="number" class="form-control" name="sub_list_count" value="<?php echo isset($cp_data['sub_list_count']) ? $cp_data['sub_list_count'] : ''; ?>" placeholder="Default: 5 random cards">
		                </div>
		            </div>

		            <div class="mt-3">
		                <button type="submit" name="action" value="update" class="btn btn-success font-weight-bold mr-2"><i class="fa fa-save"></i> Save Promotion & Subordinate Data</button>
		                <button type="submit" name="action" value="reset" class="btn btn-danger font-weight-bold" onclick="return confirm('Are you sure you want to reset custom data back to automatic calculation?')"><i class="fa fa-refresh"></i> Reset to Automatic Data</button>
		            </div>
		        </form>
		    </div>
		  </div>

		  <div class="row" style="background-color: #fff;">
            
             
            
            
			<div class="col-lg-12 col-sm-12 col-xs-12">
				<h4>Referral Record</h4>
				<div class="table-responsive">
					<table id="example1" class="table table-condensed">
						<thead>
							<tr>
								<th>Join Date</th>
								<th>Number</th>
								<th>Invite Id</th>
								<th>IP Address</th>
								<th>Total Recharge</th> 
								<th>Wallet</th> 
								<th>Action</th> 
							</tr>
						</thead>
						<tbody>
						<?php 
							while($item=mysqli_fetch_array($refer_record)){ 
						?>
								<tr>
									<td><?= $item['createdate']; ?></td>
									<td><?= $item['mobile']; ?></td>
									<td><?= $item['owncode']; ?></td>
									<td><?= $item['ishonup']; ?></td>                                    
									<td>₹
										<?php														    
											$totalRecharge['total'] = 0;
											$q = mysqli_query($conn,"SELECT sum(motta) as total FROM `thevani` WHERE `sthiti` = '1' AND `balakedara` = '".$item['id']."'");
											$totalRecharge = mysqli_fetch_assoc($q);
											echo round($totalRecharge['total'],2);                                                           
										?>
									</td>
									<td>₹
										<?php
											$totalRecharge['total'] = 0;
											$q = mysqli_query($conn,"SELECT motta as total FROM shonu_kaichila WHERE balakedara = '".$item['id']."' ");
											$totalRecharge = mysqli_fetch_assoc($q);
											echo round($totalRecharge['total'],2);                                                            
										?>
									</td>
									<td>
										<a href="user-details.php?user=<?= $item['id']; ?>"  target="_blank" class="update-person" style="background-color: darkorange; color:white; font-size:12px; padding: 5px;" title="User Detail">User Detail</a>
									</td>                                                                                                                       													
								</tr>
						<?php 
							} 
						?>
						</tbody>
					</table>
				</div>
			</div>        
		  </div>
		  <br>
		  <div class="row" style="background-color: #fff;">
			<div class="col-lg-12 col-sm-12 col-xs-12">
				<h4>Recharge Record</h4>
				<div class="table-responsive">
					<table id="example3" class="table table-condensed">
						<thead>
							<tr>
								<th>Updated At</th>
								<th>Transaction ID</th>
								<th>Amount</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
						<?php 
							while($item=mysqli_fetch_array($deposit_record)){ 
						?>
								<tr>
									<td><?= $item['dinankavannuracisi']; ?></td>
									<td><?= $item['ullekha']; ?></td>
									<td><?= $item['motta']; ?></td>                                    
									<td>₹
										<?php 
											if($item['sthiti'] == 1){
												echo "Success";
											}
											else if($item['sthiti'] == 0){
												echo "Pending";
											} 
											else if($item['sthiti'] == 2){
												echo "Rejected";
											} 
										?>
									</td>									                                                                                                                      													
								</tr>
						<?php 
							} 
						?>
						</tbody>
					</table>
				</div>
			</div>        
		  </div>
		  <br>
		  <div class="row" style="background-color: #fff;">
			<div class="col-lg-12 col-sm-12 col-xs-12">
				<h4>Withdraw Record</h4>
				<div class="table-responsive">
					<table id="example4" class="table table-condensed">
						<thead>
							<tr>
								<th>Updated At</th>
								<th>Amount</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
						<?php 
							while($item=mysqli_fetch_array($withdraw_record)){ 
						?>
								<tr>
									<td><?= $item['dinankavannuracisi']; ?></td>
									<td><?= $item['motta']; ?></td>                                
									<td>₹
										<?php 
											if($item['sthiti'] == 1){
												echo "Success";
											}
											else if($item['sthiti'] == 0){
												echo "Pending";
											} 
											else if($item['sthiti'] == 2){
												echo "Rejected";
											} 
										?>
									</td>									                                                                                                                      													
								</tr>
						<?php 
							} 
						?>
						</tbody>
					</table>
				</div>
              
              <br>
		  <div class="row" style="background-color: #fff;">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <h4>Wingo Bet Record</h4>
        <div class="table-responsive">
            <table id="example5" class="table table-condensed">
                <thead>
                    <tr>
                        <th>Wingo Type</th>
                        <th>Period</th>
                        <th>Amount</th>
                        <th>Result</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $query = "
                    SELECT 'Wingo 30 Sec' AS wingo_type, kalaparichaya, ketebida, phalaphala 
                    FROM bajikattuttate_zehn WHERE byabaharkarta = '".$userid."'
                    UNION ALL
                    SELECT 'Wingo 1 Min' AS wingo_type, kalaparichaya, ketebida, phalaphala 
                    FROM bajikattuttate WHERE byabaharkarta = '".$userid."'
                    UNION ALL
                    SELECT 'Wingo 3 Min' AS wingo_type, kalaparichaya, ketebida, phalaphala 
                    FROM bajikattuttate_drei WHERE byabaharkarta = '".$userid."'
                    UNION ALL
                    SELECT 'Wingo 5 Min' AS wingo_type, kalaparichaya, ketebida, phalaphala 
                    FROM bajikattuttate_funf WHERE byabaharkarta = '".$userid."'
                ";

                $wingo = mysqli_query($conn, $query);

                if (!$wingo) {
                    echo "Error: " . mysqli_error($conn); // Error handling for query failure
                } else {
                    // Loop through all rows returned by the query
                    while ($imitator = mysqli_fetch_assoc($wingo)) {
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($imitator['wingo_type']); ?></td>
                        <td><?php echo htmlspecialchars($imitator['kalaparichaya']); ?></td>
                        <td><?php echo htmlspecialchars($imitator['ketebida']); ?></td>                                    
                        <td><?php 
                            echo ($imitator['phalaphala'] == "perte") ? "LOSS" : "WIN";
                        ?></td>           
                    </tr>
                <?php 
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>        
</div>
            
              
              <!--   here i add trx wingo   -->
              
<div class="row" style="background-color: #fff;">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <h4>Trx Wingo Bet Record</h4>
        <div class="table-responsive">
            <table id="example5" class="table table-condensed">
                <thead>
                    <tr>
                        <th>Trx Wingo Type</th>
                        <th>Period</th>
                        <th>Amount</th>
                        <th>Result</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $query = "
                    SELECT 'Trx Wingo 1 Min' AS trx_type, kalaparichaya, ketebida, phalaphala 
                    FROM bajikattuttate_trx WHERE byabaharkarta = '".$userid."'
                    UNION ALL
                    SELECT 'Trx Wingo 3 Min' AS trx_type, kalaparichaya, ketebida, phalaphala 
                    FROM bajikattuttate_trx3 WHERE byabaharkarta = '".$userid."'
                    UNION ALL
                    SELECT 'Trx Wingo 5 Min' AS trx_type, kalaparichaya, ketebida, phalaphala 
                    FROM bajikattuttate_trx5 WHERE byabaharkarta = '".$userid."'
                    UNION ALL
                    SELECT 'Trx Wingo 10 Min' AS trx_type, kalaparichaya, ketebida, phalaphala 
                    FROM bajikattuttate_trx10 WHERE byabaharkarta = '".$userid."'
                ";

                $trx = mysqli_query($conn, $query);

                if (!$trx) {
                    echo "Error: " . mysqli_error($conn); // Error handling for query failure
                } else {
                    // Loop through all rows returned by the query
                    while ($imitator = mysqli_fetch_assoc($trx)) {
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($imitator['trx_type']); ?></td>
                        <td><?php echo htmlspecialchars($imitator['kalaparichaya']); ?></td>
                        <td><?php echo htmlspecialchars($imitator['ketebida']); ?></td>                                    
                        <td><?php 
                            echo ($imitator['phalaphala'] == "perte") ? "LOSS" : "WIN";
                        ?></td>           
                    </tr>
                <?php 
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>        
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
  <script src="vendors/chart.js/Chart.min.js"></script>
  <script src="vendors/jquery-bar-rating/jquery.barrating.min.js"></script>
  <script src="js/dashboard.js"></script>
  <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
  <script>
	$('#example1').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": false,
      "info": true,
      "autoWidth": true,
	  "pageLength": 20
    });
	$('#example3').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": false,
      "info": true,
      "autoWidth": true,
	  "pageLength": 20
    });
	$('#example4').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": false,
      "info": true,
      "autoWidth": true,
	  "pageLength": 20
    });
    
    $('#example5').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": false,
      "info": true,
      "autoWidth": true,
	  "pageLength": 20
    });
    
    
    $('#example6').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": false,
      "info": true,
      "autoWidth": true,
	  "pageLength": 20
    });
    
  </script>
</body>

</html>