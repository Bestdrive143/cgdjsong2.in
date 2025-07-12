<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta forua="true" http-equiv="Cache-Control" content="max-age=0"/>

<title>Contact Us  </title>

<link href="/css/styles.css" rel="stylesheet" style="text/css"></head><body>

<div class="logo"><center><img src="/image/sonamhost.png" width="300" height="80" alt="logo"/></center></div><br/>

<?php
$ip = $_POST['ip'];
$httpref = $_POST['httpref'];
$httpagent = $_POST['httpagent'];
$visitor = $_POST['visitor'];
$visitormail = $_POST['visitormail'];
$notes = $_POST['notes'];
$attn = $_POST['attn'];

if (eregi('http:', $notes)) {
die ("Do NOT try that! ! ");
}
if(!$visitormail == "" && (!strstr($visitormail,"@") || !strstr($visitormail,".")))
{
echo "Enter valid e-mail\n";
$badinput = "<h2>Feedback was NOT submitted</h2>\n";
echo $badinput;
die ("Go back! ! ");
}

if(empty($visitor) || empty($visitormail) || empty($notes )) {
echo "Seems Like u Missed SomeTing.<br/>\n";
die ("<a href='index.php'>Back</a><br/><br/>");
}

$todayis = date("l, F j, Y, g:i a");

$attn = $attn;
$subject = $attn;

$notes = stripcslashes($notes);

$message = " $todayis [EST] \n
About: $attn \n\n
Message: $notes \n\n
From: $visitor ($visitormail)\n
Additional Info : IP = $ip \n
Browser Info: $httpagent \n
Referral : $httpref \n
";

$from = "From: $visitormail\r\n";


mail("djyuyumix@gmail.com", $subject, $message, $from);

?>




<p>

<br />
Thank You  <?php echo $visitor ?> ( <?php echo $visitormail ?> ) 
<br /><br />
Your Mail
About  <?php echo $attn ?>
<br/><br />
 And The Message
<div class="mess">
<?php $notesout = str_replace("\r", "<br/>", $notes);
echo $notesout; ?>
</div>
 Has Been Sent To Admin! <br/><br />
You'll Get A Response As Soon As Possible.<br/><br />
We Have Logged Your<br/> <br />
IP+Browser+Location <br/><br />
To Aviod Misuse Of This Service!<BR/>

<br />

</p>


<div class="search"><b><a class="siteLink" href="index.php"><font color="#ffffff">Home</font></a></b></div>



  </body>
</html>