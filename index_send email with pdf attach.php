<?php
function is_digits($in)
{
	return !preg_match("/[^0-9]/",$in);
}
$sendemail = 0;//email to shelley? (1= yes, 0=direct to browser)
$pdfinbrowser = 1;//0=show in browser or 1=download
$errorfound = 0;
if(isset($_POST['submit']))
{
	/* start checks */
	$error['name'] = (trim($_POST['name']) == null)?1:0;
	$error['timeleftworkh'] = (!is_digits(trim($_POST['timeleftworkh']))&&trim($_POST['timeleftworkh'])!="HH")?1:0;
	$error['timeleftworkm'] = (!is_digits(trim($_POST['timeleftworkm']))&&trim($_POST['timeleftworkm'])!="MM")?1:0;
	$error['timeleftworkh'] = (((trim($_POST['timeleftworkh']) > 12) && $_POST['timeleftworka'] == "am")||(!is_digits(trim($_POST['timeleftworkh']))&&trim($_POST['timeleftworkh'])!="HH"))?1:0;
	$error['nature1'] = (trim($_POST['nature1']) == null)?1:0;
	$error['nature2'] = 0;//only require 1 line
	$error['absentfromd'] = (!is_digits(trim($_POST['absentfromd'])))?1:0;
	$error['absentfromm'] = (!is_digits(trim($_POST['absentfromm'])))?1:0;
	$error['absentfromy'] = (!is_digits(trim($_POST['absentfromy'])))?1:0;
	$error['absenttod'] = (!is_digits(trim($_POST['absenttod'])))?1:0;
	$error['absenttom'] = (!is_digits(trim($_POST['absenttom'])))?1:0;
	$error['absenttoy'] = (!is_digits(trim($_POST['absenttoy'])))?1:0;
	$error['absenttotal'] = (trim($_POST['absenttotal']) == null)?1:0;
	$error['treatment_yes_line1'] = (trim($_POST['treatment_yes_line1']) == null && $_POST['seendoc'] == "Yes")?1:0;
	$error['treatment_yes_line2'] = 0;//only require 1 line
	$error['treatment_yes_line3'] = 0;//only require 1 line
	$error['treatment_no_line1'] = (trim($_POST['treatment_no_line1']) == null && $_POST['seendoc'] == "No")?1:0;
	$error['treatment_no_line2'] = 0;//only require 1 line
	$error['declaration_name'] = (trim($_POST['declaration_name']) == null)?1:0;
	$error['declaration_dated'] = (!is_digits(trim($_POST['declaration_dated'])))?1:0;
	$error['declaration_datem'] = (!is_digits(trim($_POST['declaration_datem'])))?1:0;
	$error['declaration_datey'] = (!is_digits(trim($_POST['declaration_datey'])))?1:0;
	if(in_array(1,$error))
	{$errorfound = 1;}
	/* end checks */
	if($errorfound == 1)//found errors
	{	$tohighlight = "";
		foreach($error as $err => $val)
		{
			if($val == 1){$tohighlight .= "&amp;$err=error";}
			else{$tohighlight .= "&amp;$err=$_POST[$err]";}
		}
		$tohighlight .= "&amp;timeleftworka=$_POST[timeleftworka]&amp;seendoc=$_POST[seendoc]";
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<style type="text/css">td img {border:0px;}body{margin:0px auto 0px;background:#ffffff;} div{font-size:13px;font-family:Arial, Helvetica, sans-serif}form{margin:0px;}.textfield,.selects,.radios{border:0px;}</style>
		<title>EMPLOYEE SELF-CERTIFICATION</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		</head>
		<body>
		<div style="width:600px;margin:100px auto 0px;text-align:center;font: 18px bold Arial, Helvetica, sans-serif;color:#777777;">There was an problem with the details you submitted<br />Please click <a href="index.php?errors=1<?=$tohighlight?>" style="color:#333333;">here</a> to return to the form.</div>
		</body>
		</html>
		<?
	}
	else//passed tests
	{
		$name = trim($_POST['name']);
		$time_left_work = (trim($_POST['timeleftworkh']) != "HH")?trim($_POST['timeleftworkh']).":".trim($_POST['timeleftworkm']).$_POST['timeleftworka']:"N/A";
		$nature1 = str_replace("\\","",$_POST['nature1']);
		$nature2 = str_replace("\\","",$_POST['nature2']);
		$absentfrom = trim($_POST['absentfromd'])."/".trim($_POST['absentfromm'])."/".trim($_POST['absentfromy']);
		$absentto = trim($_POST['absenttod'])."/".trim($_POST['absenttom'])."/".trim($_POST['absenttoy']);
		$absenttotal = trim($_POST['absenttotal']);
		$seendoc = ($_POST['seendoc'] == "No") ? "485" : "411";//411 = yes, 485 = no
		$sawdoc = $_POST['seendoc'];
		$treatment_yes_line1 = (trim($_POST['treatment_yes_line1']) != null)?str_replace("\\","",trim($_POST['treatment_yes_line1'])):"N/A";
		$treatment_yes_line2 = str_replace("\\","",trim($_POST['treatment_yes_line2']));
		$treatment_yes_line3 = str_replace("\\","",trim($_POST['treatment_yes_line3']));
		$treatment_no_line1 = (trim($_POST['treatment_no_line1']) != null)?str_replace("\\","",trim($_POST['treatment_no_line1'])):"N/A";
		$treatment_no_line2 = str_replace("\\","",trim($_POST['treatment_no_line2']));
		$declaration_name = trim($_POST['declaration_name']);
		$declaration_date = trim($_POST['declaration_dated'])."/".trim($_POST['declaration_datem'])."/".trim($_POST['declaration_datey']);
		require_once("dompdf_config.inc.php");
		$html =
			'<html>
		<head>
		<style type="text/css">td img {border:0px;}body{} div{font-size:13px;font-family:arial, helvetica, sans-serif}</style>
		<title>sickform_forpdf</title>
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
		</head>
		<body bgcolor="#ffffff" style="margin:0px auto 0px">
		<div style="position:absolute;top:40px"><img src="./www/self_cert/images/1.jpg" alt="" /><br /><img src="./www/self_cert/images/2.jpg" alt="" /><br /><img src="./www/self_cert/images/3.jpg" alt="" /><br /><img src="./www/self_cert/images/4.jpg" alt="" /><br /><img src="./www/self_cert/images/5.jpg" alt="" />
		<div style="position:absolute;top:11px;left:73px;color:white;font-family:helvetica;font-size:26px;font-weight:bold;">EMPLOYEE SELF-CERTIFICATION</div>
		<div style="position:absolute;top:68px;left:93px;text-align:justify;font-size:11px;width:645px;line-height:14px;">Please complete this form carefully as any entitlement to sick pay (statutory or occupational, if applicable) will depend on the evidence you provide below. Please ask your manager if there is anything you do not understand. You must complete this form on returning to work after an absence due to sickness or injury. If your sickness absence exceeds 7 days (or where separate periods of linked absence, as defined in the statutory sick pay administration regulations, exceed 7 days) you must obtain a medical statement from your doctor as evidence of the nature of your incapacity.</div>
		
		
		<div style="position:absolute;top:168px;left:105px;color:white;font-family:helvetica;font-size:13px;font-weight:bold;">Employee details</div>
		<div style="position:absolute;top:201px;left:100px;">Name</div>
		<div style="position:absolute;top:201px;left:143px;">'.$name.'</div>
		
		<div style="position:absolute;top:289px;left:105px;color:white;font-family:helvetica;font-size:13px;font-weight:bold;">Absence</div>
		<div style="position:absolute;top:323px;left:104px;line-height:14px;font-size:10px;width:340px;">If you were at work when you became sick, state the time you left work to go home/to the doctor/to hospital.</div>
		<div style="position:absolute;top:322px;left:492px;">'.$time_left_work.'</div>
		<div style="position:absolute;top:369px;left:104px;line-height:14px;font-size:10px;width:624px;">I certify that i was unable to attend work for the following reason(s). (State the nature of illness or symptoms or describe the injury or other incapacity.</div>
		<div style="position:absolute;top:406px;left:110px;">'.$nature1.'</div>
		<div style="position:absolute;top:431px;left:110px;">'.$nature2.'</div>
		<div style="position:absolute;top:469px;left:104px;font-size:10px">Period of absence:</div>
		<div style="position:absolute;top:469px;left:212px;font-size:10px">From</div>
		<div style="position:absolute;top:467px;left:246px;">'.$absentfrom.'</div>
		<div style="position:absolute;top:469px;left:395px;font-size:10px">To</div>
		<div style="position:absolute;top:467px;left:418px;">'.$absentto.'</div>
		<div style="position:absolute;top:469px;left:585px;font-size:10px">Total no. of days</div>
		<div style="position:absolute;top:467px;left:677px;">'.$absenttotal.'</div>
		
		<div style="position:absolute;top:551px;left:105px;color:white;font-family:helvetica;font-size:13px;font-weight:bold;">Treatment</div>
		<div style="position:absolute;top:585px;left:104px;font-size:10px">Have you seen a doctor or visited a hospital?</div>
		<div style="position:absolute;top:585px;left:381px;font-size:10px">Yes</div>
		<div style="position:absolute;top:585px;left:456px;font-size:10px">No</div>
		<div style="position:absolute;top:574px;left:'.$seendoc.'px;font-size:32px;">&bull;</div>
		<div style="position:absolute;top:617px;left:104px;font-size:10px">If yes, please give name and address of doctor or hospital and state treatment prescribed.</div>
		<div style="position:absolute;top:642px;left:110px;">'.$treatment_yes_line1.'</div>
		<div style="position:absolute;top:668px;left:110px;">'.$treatment_yes_line2.'</div>
		<div style="position:absolute;top:693px;left:110px;">'.$treatment_yes_line3.'</div>
		<div style="position:absolute;top:722px;left:104px;font-size:10px">If no, did you seek advice from a pharmacist and take any medicine or treatment? please give brief details below.</div>
		<div style="position:absolute;top:746px;left:110px;">'.$treatment_no_line1.'</div>
		<div style="position:absolute;top:771px;left:110px;">'.$treatment_no_line2.'</div>
		
		<div style="position:absolute;top:853px;left:105px;color:white;font-family:helvetica;font-size:13px;font-weight:bold;">Declaration</div>
		<div style="position:absolute;top:887px;left:104px;font-size:10px;width:280px;text-align:justify;">I certify that during the above period of absence i have not worked or taken part in any activities inconsistent with my absence and that the above information is complete and correct. i agree that my doctor, or other medical authority, may be approached by the company to give further information relevant to my absence, either to the company or a medical practitioner appointed by the company, if so requested.</div>
		<div style="position:absolute;top:892px;left:398px;font-size:10px">Name</div>
		<div style="position:absolute;top:894px;left:460px;">'.$declaration_name.'</div>
		<div style="position:absolute;top:918px;left:398px;font-size:10px">Date</div>
		<div style="position:absolute;top:920px;left:460px;">'.$declaration_date.'</div></div>
		</body>
		</html>';
		$formattedname = str_replace(" ","_",strtolower($name));
		$formatteddate = str_replace("/","-",$declaration_date);
		$filename = "gmk_self-certification_".$formattedname."_".$formatteddate.".pdf";
		$dompdf = new dompdf();
		$dompdf->load_html($html);
		$dompdf->render();
		if($sendemail == 1)
		{
			file_put_contents($filename, $dompdf->output());  //put file into temp dir
			$random_hash = md5(date('r', time()));
      $attachment = chunk_split(base64_encode(file_get_contents($filename)));
			$to = "shelley@gmk.co.uk";
			$subject = "GMK EMPLOYEE SELF-CERTIFICATION $name-$declaration_date";
			$body = "
				<html>
				<head>
				</head>
				<body style='font-family:Arial;font-size:13px;'>
				NEW GMK EMPLOYEE SELF-CERTIFICATION FORM SUBMISSION<br />
				-----------------------------------------------------------------------------------------------------<br />
				<table cellpadding='0' cellpsacing='0' width='100%' style='font-family:Arial;font-size:12px;'>
				<tr><td width='20%' nowrap='nowrap' align='right' style='font-weight:bold'>Name:</td><td width='80%'> $name</td></tr>
				<tr><td width='20%' nowrap='nowrap' align='right' style='font-weight:bold'>Absent:</td><td width='80%'> $absentfrom - $absentto ($absenttotal days)</td></tr>
				<tr><td width='20%' nowrap='nowrap' align='right' style='font-weight:bold'>Time left work:</td><td width='80%'> $time_left_work</td></tr>
				<tr><td width='20%' nowrap='nowrap' align='right' style='font-weight:bold' valign='top'>Nature of illness:</td><td width='80%'> $nature1 $nature2</td></tr>
				<tr><td width='20%' nowrap='nowrap' align='right' style='font-weight:bold'>Seen Doctor/Hospital:</td><td width='80%'> $sawdoc</td></tr>
				<tr><td width='20%' nowrap='nowrap' align='right' style='font-weight:bold' valign='top'>Doctor/Hospital Treatment:</td><td width='80%'> $treatment_yes_line1 $treatment_yes_line2 $treatment_yes_line3</td></tr>
				<tr><td width='20%' nowrap='nowrap' align='right' style='font-weight:bold' valign='top'>Pharmacy/Home treatment:</td><td width='80%'> $treatment_no_line1 $treatment_no_line2</td></tr>
				<tr><td width='100%' nowrap='nowrap' align='right' colspan='2'>&nbsp;</td></tr>
				<tr><td width='20%' nowrap='nowrap' align='right' style='font-weight:bold'>Declaration Name:</td><td width='80%'> $declaration_name</td></tr>
				<tr><td width='20%' nowrap='nowrap' align='right' style='font-weight:bold'>Declaration Date:</td><td width='80%'> $declaration_date</td></tr>
				</table>
				-----------------------------------------------------------------------------------------------------<br />
				The full PDF version of the completed form is attached.<br />
				</body>
				</html>";
			$headers = "From: noreply@gmk.co.uk\r\nReply-To: noreply@gmk.co.uk\r\n";
 			$headers .= "X-Mailer: PHP/".phpversion()."\r\n";
			$headers .= "MIME-Version: 1.0\r\nContent-Type: multipart/mixed; boundary=\"mixed-".$random_hash."\"\r\n";
//do not tab this accross!! it stops the attachment from working.
  $output = "
--mixed-$random_hash 
Content-Type: multipart/alternative; boundary=\"alt-$random_hash\"
--alt-$random_hash
Content-Type: text/html; charset=\"iso-8859-1\"
Content-Transfer-Encoding: 7bit

$body
 
--alt-$random_hash--
 
--mixed-$random_hash
Content-Type: application/pdf; name=$filename
Content-Transfer-Encoding: base64 
Content-Disposition: attachment 
filename=$filename

$attachment
--mixed-$random_hash--";

			
			// -=-=-=- send the email
			//ini_set("SMTP","smtp.murphx.net");//temp
			//ini_set("sendmail_from","senfield@gmk.co.uk");//temp
			if(mail( $to, $subject, $output, $headers ))
			{
				?>
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<style type="text/css">td img {border:0px;}body{} div{font-size:13px;font-family:Arial, Helvetica, sans-serif}</style>
				<title>EMPLOYEE SELF-CERTIFICATION</title>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				</head>
				<body bgcolor="#FFFFFF" style="margin:0px auto 0px">
				<div style="width:600px;margin:100px auto 0px;text-align:center;font: 18px bold Arial, Helvetica, sans-serif;color:#777777;">Your form has been sent, thank you.</div>
				</body></html>
				<?
			}
			$fh = fopen($filename, 'w') or die("error opening file");//open so we can close it(fixes issue if file was open)
			fclose($fh);//close file
			unlink($filename);//delete the file as no longer needed
		}
		else//show in browser (not email)
		{
			$dompdf->stream($filename,array("Attachment" => $pdfinbrowser));//show direct from browser
		}
	}
}
else
{
	$errors = 0;
	foreach($_GET as $got => $val)
	{
		$high[$got] = ($val == "error") ? "style='background:#ffbebe;'":"";
		if($val == "error"){$errors = 1;}
	}
	$name = (isset($_GET['name'])&&$_GET['name'] != "error")?$_GET['name']:"";
	$timeleftworkh = (isset($_GET['timeleftworkh'])&&$_GET['timeleftworkh']!="error")?$_GET['timeleftworkh']:"HH";
	$timeleftworkm = (isset($_GET['timeleftworkm'])&&$_GET['timeleftworkm']!="error")?$_GET['timeleftworkm']:"MM";
	$timeleftworka = (isset($_GET['timeleftworka'])&&$_GET['timeleftworka']!="error")?$_GET['timeleftworka']:"am";
	$nature1 = (isset($_GET['nature1'])&&$_GET['nature1']!="error")?str_replace("\\","",$_GET['nature1']):"";
	$nature2 = (isset($_GET['nature2'])&&$_GET['nature2']!="error")?str_replace("\\","",$_GET['nature2']):"";
	$absentfromd = (isset($_GET['absentfromd'])&&$_GET['absentfromd']!="error")?$_GET['absentfromd']:"DD";
	$absentfromm = (isset($_GET['absentfromm'])&&$_GET['absentfromm']!="error")?$_GET['absentfromm']:"MM";
	$absentfromy = (isset($_GET['absentfromy'])&&$_GET['absentfromy']!="error")?$_GET['absentfromy']:"YYYY";
	$absenttod = (isset($_GET['absenttod'])&&$_GET['absenttod']!="error")?$_GET['absenttod']:"DD";
	$absenttom = (isset($_GET['absenttom'])&&$_GET['absenttom']!="error")?$_GET['absenttom']:"MM";
	$absenttoy = (isset($_GET['absenttoy'])&&$_GET['absenttoy']!="error")?$_GET['absenttoy']:"YYYY";
	$absenttotal = (isset($_GET['absenttotal'])&&$_GET['absenttotal']!="error")?$_GET['absenttotal']:"";
	$treatment_yes_line1 = (isset($_GET['treatment_yes_line1'])&&$_GET['treatment_yes_line1']!="error")?str_replace("\\","",$_GET['treatment_yes_line1']):"";
	$treatment_yes_line2 = (isset($_GET['treatment_yes_line2'])&&$_GET['treatment_yes_line2']!="error")?str_replace("\\","",$_GET['treatment_yes_line2']):"";
	$treatment_yes_line3 = (isset($_GET['treatment_yes_line3'])&&$_GET['treatment_yes_line3']!="error")?str_replace("\\","",$_GET['treatment_yes_line3']):"";
	$treatment_no_line1 = (isset($_GET['treatment_no_line1'])&&$_GET['treatment_no_line1']!="error")?str_replace("\\","",$_GET['treatment_no_line1']):"";
	$treatment_no_line2 = (isset($_GET['treatment_no_line2'])&&$_GET['treatment_no_line2']!="error")?str_replace("\\","",$_GET['treatment_no_line2']):"";
	$declaration_name = (isset($_GET['declaration_name'])&&$_GET['declaration_name'] != "error")?$_GET['declaration_name']:"";
	$declaration_dated = (isset($_GET['declaration_dated'])&&$_GET['declaration_dated']!="error")?$_GET['declaration_dated']:"DD";
	$declaration_datem = (isset($_GET['declaration_datem'])&&$_GET['declaration_datem']!="error")?$_GET['declaration_datem']:"MM";
	$declaration_datey = (isset($_GET['declaration_datey'])&&$_GET['declaration_datey']!="error")?$_GET['declaration_datey']:"YYYY";
	$seendoc = (isset($_GET['seendoc'])&&$_GET['seendoc']!="error")?$_GET['seendoc']:"";
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<style type="text/css">td img {border:0px;}body{margin:0px auto 0px;background:#ffffff;} div{font-size:13px;font-family:Arial, Helvetica, sans-serif}form{margin:0px;}.textfield,.selects,.radios{border:0px;}</style>
	<title>EMPLOYEE SELF-CERTIFICATION</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>
	<body>
		<div style="width:820px;margin:0px auto;">
		<form action="index.php" method="post">
			<div style="position:absolute;top:40px;"><img src="./www/self_cert/images/1.jpg" alt="" /><br /><img src="./www/self_cert/images/2.jpg" alt="" /><br /><img src="./www/self_cert/images/3.jpg" alt="" /><br /><img src="./www/self_cert/images/4.jpg" alt="" /><br /><img src="./www/self_cert/images/5.jpg" alt="" />
			<div style="position:absolute;top:11px;left:73px;color:white;font-family:Helvetica;font-size:26px;font-weight:bold;">EMPLOYEE SELF-CERTIFICATION</div>
			<div style="position:absolute;top:68px;left:93px;text-align:justify;font-size:11px;width:645px;line-height:14px;">Please complete this form carefully as any entitlement to sick pay (statutory or occupational, if applicable) will depend on the evidence you provide below. Please ask your manager if there is anything you do not understand. You must complete this form on returning to work after an absence due to sickness or injury. If your sickness absence exceeds 7 days (or where separate periods of linked absence, as defined in the Statutory Sick Pay administration regulations, exceed 7 days) you must obtain a medical statement from your doctor as evidence of the nature of your incapacity.<? if($errors == 1){?><div style='color:red;font-size:12px'>! Fields highlighted require correction.</div><? }?></div>
			
			<div style="position:absolute;top:168px;left:105px;color:white;font-family:Helvetica;font-size:13px;font-weight:bold;">Employee Details</div>
			<div style="position:absolute;top:201px;left:100px;">Name</div>
			<div style="position:absolute;top:199px;left:143px;"><input type="text" name="name" value="<?=$name?>" class="textfield" style="width:578px;" <?=$high['name']?> /></div>
			
			<div style="position:absolute;top:289px;left:105px;color:white;font-family:Helvetica;font-size:13px;font-weight:bold;">Absence</div>
			<div style="position:absolute;top:323px;left:104px;line-height:14px;font-size:10px;width:340px;">If you were at work when you became sick, state the time you left work to go home/to the doctor/to hospital.</div>
			<div style="position:absolute;top:321px;left:492px;"><input type="text" name="timeleftworkh" value="<?=$timeleftworkh?>" <?=$high['timeleftworkh']?> class="textfield" style="width:24px;" maxlength="2" onFocus="if (value== 'HH') {value=''}" onBlur="if (value== '') {value='HH'}" />:<input type="text" name="timeleftworkm" value="<?=$timeleftworkm?>" <?=$high['timeleftworkm']?> class="textfield" style="width:24px;" maxlength="2" onFocus="if (value== 'MM') {value=''}" onBlur="if (value== '') {value='MM'}" /><select name="timeleftworka" class="selects"><option value="am" <? if($timeleftworka == "am"){?>selected="selected"<? }?>>am</option><option value="pm" <? if($timeleftworka == "pm"){?>selected="selected"<? }?>>pm</option></select></div>
			<div style="position:absolute;top:369px;left:104px;line-height:14px;font-size:10px;width:624px;">I certify that I was unable to attend work for the following reason(s). (State the nature of illness or symptoms or describe the injury or other incapacity.</div>
			<div style="position:absolute;top:404px;left:110px;"><input type="text" name="nature1" value="<?=$nature1?>" <?=$high['nature1']?> class="textfield" style="width:611px;" maxlength="95" /></div>
			<div style="position:absolute;top:430px;left:110px;"><input type="text" name="nature2" value="<?=$nature2?>" <?=$high['nature2']?> class="textfield" style="width:611px;" maxlength="95" /></div>
			<div style="position:absolute;top:469px;left:104px;font-size:10px">Period of absence:</div>
			<div style="position:absolute;top:469px;left:212px;font-size:10px">From</div>
			<div style="position:absolute;top:467px;left:246px;"><input type="text" name="absentfromd" class="textfield" style="width:24px;" maxlength="2" value="<?=$absentfromd?>" <?=$high['absentfromd']?> onFocus="if (value== 'DD') {value=''}" onBlur="if (value== '') {value='DD'}" />/<input type="text" name="absentfromm" class="textfield" style="width:24px;" maxlength="2" value="<?=$absentfromm?>" <?=$high['absentfromm']?> onFocus="if (value== 'MM') {value=''}" onBlur="if (value== '') {value='MM'}" />/<input type="text" name="absentfromy" class="textfield" style="width:40px;" maxlength="4" value="<?=$absentfromy?>" <?=$high['absentfromy']?> onFocus="if (value== 'YYYY') {value=''}" onBlur="if (value== '') {value='YYYY'}" /></div>
			<div style="position:absolute;top:469px;left:395px;font-size:10px">To</div>
			<div style="position:absolute;top:467px;left:418px;"><input type="text" name="absenttod" class="textfield" style="width:24px;" maxlength="2" value="<?=$absenttod?>" <?=$high['absenttod']?> onFocus="if (value== 'DD') {value=''}" onBlur="if (value== '') {value='DD'}" />/<input type="text" name="absenttom" class="textfield" style="width:24px;" maxlength="2" value="<?=$absenttom?>" <?=$high['absenttom']?> onFocus="if (value== 'MM') {value=''}" onBlur="if (value== '') {value='MM'}" />/<input type="text" name="absenttoy" class="textfield" style="width:40px;" maxlength="4" value="<?=$absenttoy?>" <?=$high['absenttoy']?> onFocus="if (value== 'YYYY') {value=''}" onBlur="if (value== '') {value='YYYY'}" /></div>
			<div style="position:absolute;top:469px;left:585px;font-size:10px">Total no. of days</div>
			<div style="position:absolute;top:467px;left:677px;"><input type="text" name="absenttotal" class="textfield" style="width:35px;" value="<?=$absenttotal?>" <?=$high['absenttotal']?> /></div>
			
			<div style="position:absolute;top:551px;left:105px;color:white;font-family:Helvetica;font-size:13px;font-weight:bold;">Treatment</div>
			<div style="position:absolute;top:585px;left:104px;font-size:10px">Have you seen a doctor or visited a hospital?</div>
			<div style="position:absolute;top:585px;left:381px;font-size:10px">Yes</div>
			<div style="position:absolute;top:585px;left:456px;font-size:10px">No</div>
			<div style="position:absolute;top:583px;left:407px;"><input type="radio" name="seendoc" value="Yes" class="radios" <? if($seendoc == "Yes"){?>checked="checked"<? }?> /></div>
			<div style="position:absolute;top:583px;left:480px;"><input type="radio" name="seendoc" value="No" class="radios" <? if($seendoc != "Yes"){?>checked="checked"<? }?>  /></div>
			<div style="position:absolute;top:617px;left:104px;font-size:10px">If yes, please give name and address of doctor or hospital and state treatment prescribed.</div>
			<div style="position:absolute;top:640px;left:110px;"><input type="text" name="treatment_yes_line1" value="<?=$treatment_yes_line1?>" <?=$high['treatment_yes_line1']?> class="textfield" style="width:611px;" maxlength="95" /></div>
			<div style="position:absolute;top:666px;left:110px;"><input type="text" name="treatment_yes_line2" value="<?=$treatment_yes_line2?>" <?=$high['treatment_yes_line2']?> class="textfield" style="width:611px;" maxlength="95" /></div>
			<div style="position:absolute;top:691px;left:110px;"><input type="text" name="treatment_yes_line3" value="<?=$treatment_yes_line3?>" <?=$high['treatment_yes_line3']?> class="textfield" style="width:611px;" maxlength="95" /></div>
			<div style="position:absolute;top:722px;left:104px;font-size:10px">If no, did you seek advice from a pharmacist and take any medicine or treatment? Please give brief details below.</div>
			<div style="position:absolute;top:743px;left:110px;"><input type="text" name="treatment_no_line1" value="<?=$treatment_no_line1?>" <?=$high['treatment_no_line1']?> class="textfield" style="width:611px;" maxlength="95" /></div>
			<div style="position:absolute;top:769px;left:110px;"><input type="text" name="treatment_no_line2" value="<?=$treatment_no_line2?>" <?=$high['treatment_no_line2']?> class="textfield" style="width:611px;" maxlength="95" /></div>
			
			<div style="position:absolute;top:853px;left:105px;color:white;font-family:Helvetica;font-size:13px;font-weight:bold;">Declaration</div>
			<div style="position:absolute;top:887px;left:104px;font-size:10px;width:280px;text-align:justify;">I certify that during the above period of absence I have not worked or taken part in any activities inconsistent with my absence and that the above information is complete and correct. I agree that my Doctor, or other medical authority, may be approached by the company to give further information relevant to my absence, either to the company or a medical practitioner appointed by the company, if so requested.</div>
			<div style="position:absolute;top:892px;left:398px;font-size:10px">Name</div>
			<div style="position:absolute;top:891px;left:460px;"><input type="text" name="declaration_name" value="<?=$declaration_name?>" <?=$high['declaration_name']?> class="textfield" style="width:260px;" /></div>
			<div style="position:absolute;top:918px;left:398px;font-size:10px">Date</div>
			<div style="position:absolute;top:919px;left:460px;"><input type="text" name="declaration_dated" class="textfield" style="width:24px;" maxlength="2" value="<?=$declaration_dated?>" <?=$high['declaration_dated']?> onFocus="if (value== 'DD') {value=''}" onBlur="if (value== '') {value='DD'}" />/<input type="text" name="declaration_datem" class="textfield" style="width:24px;" maxlength="2"  value="<?=$declaration_datem?>" <?=$high['declaration_datem']?> onFocus="if (value== 'MM') {value=''}" onBlur="if (value== '') {value='MM'}" />/<input type="text" name="declaration_datey" class="textfield" style="width:40px;" maxlength="4"  value="<?=$declaration_datey?>" <?=$high['declaration_datey']?> onFocus="if (value== 'YYYY') {value=''}" onBlur="if (value== '') {value='YYYY'}" /></div>
			<div style="position:absolute;top:968px;left:659px;"><input type="submit" name="submit" value="&nbsp;Submit&nbsp;" /></div>
	</form>
	</div>
	</body>
	</html>
	<?
}
?>