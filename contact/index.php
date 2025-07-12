<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta forua="true" http-equiv="Cache-Control" content="max-age=0"/>

<title>Contact Us </title>

<link href="/assets/css/css.css" rel="stylesheet" style="text/css"></head><body>

<div class="logo"><center><img src="/logo.png"  alt="logo"/></center></div><br/>

<h2>
Contact Us!
</h2>
<form method="post" action="sendmail.php">
<?php
$ipi = getenv("REMOTE_ADDR");
$httprefi = getenv ("HTTP_REFERER");
$httpagenti = getenv ("HTTP_USER_AGENT");
?>
<input type="hidden" name="ip" value="<?php echo $ipi ?>" />
<input type="hidden" name="httpref" value="<?php echo $httprefi ?>" />
<input type="hidden" name="httpagent" value="<?php echo $httpagenti ?>" />
Your Name:* <br />
<input type="text" name="visitor" size="35" />
<br />
Your Email:*<br />
<input type="text" name="visitormail" size="35" />
<br />
Whats It About?:<br />
<select name="attn" size="1">
<option value=" Report Abuse ">Report Abuse</option>
<option value=" Request ">Request</option>
<option value=" Suggestions ">Suggestions</option>
<option value=" Partnership ">Partnership</option>
</select>
<br /><br />
Message:*
<br />
<textarea name="notes" rows="4" cols="40"></textarea>
<br />
<input type="submit" value="Send Mail" />
</form>
<div class="c">
<small>*Required Fields</small><br/><br/>


<div class="search"><b><a class="siteLink" href="index.php"><font color="#ffffff">Home</font></a></b></div>

</body></html>

 