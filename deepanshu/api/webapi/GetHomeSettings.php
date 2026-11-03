<?php
	include "../../conn.php";
			
	header('Content-Type: application/json; charset=utf-8');
	header('Strict-Transport-Security: max-age=31536000');
	header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
	header('Access-Control-Allow-Credentials: true');
	$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
	header('Access-Control-Allow-Origin: ' . $origin);
	header('vary: Origin');
	
	date_default_timezone_set("Asia/Kolkata");
	$shnunc = date("Y-m-d H:i:s");
	$res = [
		'code' => 11,
		'msg' => 'Method not allowed',
		'msgCode' => 12,
		'serviceNowTime' => $shnunc,
	];
	$shonubody = file_get_contents("php://input");
	$shonupost = json_decode($shonubody, true);
	if ($_SERVER['REQUEST_METHOD'] != 'GET') {		
		if (isset($shonupost['language']) && isset($shonupost['random']) && isset($shonupost['signature']) && isset($shonupost['timestamp'])) {
			$language = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['language']));
			$random = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['random']));
			$signature = htmlspecialchars(mysqli_real_escape_string($conn, $shonupost['signature']));
			$shonustr = '{"language":'.$language.',"random":"'.$random.'"}';
			$shonusign = strtoupper(md5($shonustr));
			if($shonusign == $signature){
				$data['isShowAppDownloadUp'] = true;
				$data['isShowAppDownloadDown'] = true;
				$data['isShowLotteryDragon'] = true;
				$data['isSplitLocalEWallet'] = true;
				$data['jackportMaxReswadAmount'] = 500;
				$data['projectName'] = '82 lottry';
				$data['projectLogo'] = 'https://ossimg.82lottery-82lottery.com/india82/other/h5setting_20231224075634xoud.png';
				$data['languages'] = 'en|hd';
				$data['webIco'] = 'https://i.postimg.cc/gjsGqY7k/625a0d3dab354c2603da24323e7f234b20fa5821f146c09bd367dc8da8f1aad7-200.webp';
				$data['headLogo'] = 'https://ossimg.82lottery-82lottery.com/india82/other/h5setting_20231224075634xoud.png';
				$data['dollarSign'] = '₹';
				$data['upperOrLower'] = '0';
				$data['defaultCurrentLanguage'] = 'en';
				$data['registerMobile'] = '1';
				$data['registerEmail'] = '0';
				$data['areaPhoneLenList'][0]['area'] = '+91';
				$data['areaPhoneLenList'][0]['len'] = '9-12';
			
				$data['registerSms'] = '0';
				$data['isOpenLoginChangeLanguage'] = '1';
				$data['rewardValidityTime'] = 30;
				$data['electronicWinRateExternalLink'] = '';
				$data['electronicWinRateImgUrl'] = 'https://ossimg.yuk87k786d.com/91club';
				$data['isShowElectronicWinRateExternalLink'] = false;
				$data['isShowAppHandCodeWashingSwitch'] = true;
				$data['isShowHotGameWinOdds'] = true;
				$data['ossUrl'] = 'https://ossimg.yuk87k786d.com';
				$data['bigTurntableLink'] = null;
				$data['telegramExternalLink'] = null;
				$data['isOpenActivityAward'] = false;
				$data['isOpenTurntable'] = true;
				$data['isPartnerReward'] = true;
				$data['isSelfCustomerService'] = true;
				$data['webSiteUrl'] = 'https://82lotter9281.site';
				$data['isOpenFacebookEvent'] = true;
				$data['isOpenRegisterPhoneFirstZeroSwitch'] = false;
				$data['eventRegionConfigList'] = null;
				$data['firstDepositRewardCodeAmount'] = "1";
				$data['isOpenAdjustEvent'] = false;
				
				$res['data'] = $data;
				$res['code'] = 0;
				$res['msg'] = 'Succeed';
				$res['msgCode'] = 0;
				http_response_code(200);
				echo json_encode($res);
			}
			else{
				$res['code'] = 5;
				$res['msg'] = 'Wrong signature';
				$res['msgCode'] = 3;
				http_response_code(200);
				echo json_encode($res);
			}
		}
		else{
			$res['code'] = 7;
			$res['msg'] = 'Param is Invalid';
			$res['msgCode'] = 6;
			http_response_code(200);
			echo json_encode($res);
		}		
	} else {		
		http_response_code(405);
		echo json_encode($res);
	}
?>