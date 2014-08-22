<?php
require_once("config.php"); 

session_start();
$debug=false;
$fields=array("login","pass1","pass2","email","url","cap");
$found=0;
foreach($fields as $f) if (isset($_POST[$f])) $found++;
$error=array();
$info=array();

if ($found==6 && $_POST["url"]=="") {
  if ($_SESSION["captcha"]!=$_POST["cap"]) {
    $error[]=_("The captcha is incorrect, please try again"); 
  }
  if (!csrf_check($_POST["csrf"])) {
    $error[]=_("The captcha is incorrect, please try again (2)"); 
  }
  $login=fixlogin($_POST["login"]);
  if ($login!=$_POST["login"] || strlen($login)<3 || strlen($login)>80) {
    $error[]=_("The login must be between 3 and 80 characters long, and must not contains special characters (unicode and accents authorized though)");
  }
  $pass=fixlogin($_POST["pass1"]);
  if ($_POST["pass1"]!=$_POST["pass2"] || $pass!=$_POST["pass1"]) {
    $error[]=_("Your passwords are not the same, or contains special characters (unicode and accents authorized though), please try again");
  }
  if (count($error)==0) {
    sleep(5); // Let create some artificial waiting for the one who want to create many accounts ...
    // Try to create the account.
    $already=@mysql_fetch_assoc(mysql_query("SELECT id FROM accounts WHERE jabberid='".addslashes($_POST["login"]."@$domain")."';"));
    if ($already) {
      $error[]=_("This account already exist, or is disabled. You can't create that login now, please find another one!");
    }

    if (count($error)==0) {

      mysql_query("INSERT INTO accounts SET jabberid='".addslashes($_POST["login"]."@$domain")."', createdate=NOW(), email='".hashmail(trim($_POST["email"]))."', ack=0;");
      // Connect to the telnet console of prosody.
      $f=fsockopen("localhost",5582,$errno,$errstr,5);
      if (!$f) {
	$error[]=_("Can't connect to jabber server");
	mysql_query("DELETE FROM accounts WHERE jabberid='".addslashes($_POST["login"]."@$domain")."';");
      } else {
	for($i=0;$i<12;$i++) {
	  $s=fgets($f,1024);
	  if ($debug) echo ":".$s.":<br>";
	}
	fputs($f,"user:create(\"".$login."@$domain\",\"".$password."\")\n");
	$s=fgets($f,1024);
	if ($debug) echo ":".$s.":<br>";
	if (trim($s)=="| OK: User created") {
	  mysql_query("UPDATE accounts SET ack=1 WHERE jabberid='".addslashes($_POST["login"]."@$domain")."';");
	  $info[]=_("Your account has been created successfully. You can use it immediately.");
	  unset($_POST);
	  unset($_REQUEST);
	} else {
	  if ($debug) { $s=fgets($f,1024); echo ":".$s.":<br>"; }
	  mysql_query("DELETE FROM accounts WHERE jabberid='".addslashes($_POST["login"]."@$domain")."';");
	  $error[]=_("An error occurred trying to create your account, please try again later");
	  // TODO : send an email to us ;) 
	}
	fclose($f);
      }
    }
  }
}

require_once("header.php");
?>
<style type="text/css">
  .error {
color: #F00;
background-color: #FEE;
padding: 10px;
margin: 10px;
border: 2px solid #F00;
}
  .info {
color: #090;
background-color: #EFE;
padding: 10px;
margin: 10px;
border: 2px solid #090;
}
  sup {
color:red; font-size: 0.6em
}
.wichtig { display: none }
.btn {
padding: 6px 10px; background: #497ed5; color: white; font-weight: bold;
 }
</style>
<p>
<b><?php __("Menu:"); ?></b>
 <?php __("Create an account"); ?> - 
 <a href="lost.php"><?php __("I lost my password"); ?></a> - 
 <a href="disabled.php"><?php __("My account is disabled"); ?></a>
</p>

<h1><?php __("Account creation on our Jabber server"); ?></h1>

<?php
if (count($error)) {
  echo "<div class=\"error\">";
  foreach($error as $e) echo $e."<br>\n";
  echo "</div>";
}
if (count($info)) {
  echo "<div class=\"info\">";
  foreach($info as $e) echo $e."<br>\n";
  echo "</div>";
}
?>

<p><?php __("If you want to create an account on our Jabber server, please enter a login name and enter a password two times in the form below. You can also give us a non-mandatory email address which will allow you to change your password if you lose it later."); ?></p>

<form method="post" action="create.php">
  <input type="hidden" name="csrf" value="<?php echo csrf_gen(); ?>" />
  <table style="width: 700px">
  <tr><th style="width: 200px"><?php __("Login"); ?><sup>*</sup> <br /><i><small><?php __("3 characters or more"); ?></small></i></th>
  <td><input type="text" name="login" id="login" value="<?php eher("login"); ?>" style="width: 200px" />@$domain</td></tr>

  <tr><th><?php __("Password"); ?><sup>*</sup></th>
  <td><input type="password" name="pass1" id="pass1" value="<?php eher("pass1"); ?>" style="width: 200px"/></td></tr>
  <tr><th><?php __("Password (again)"); ?><sup>*</sup></th>
  <td><input type="password" name="pass2" id="pass2" value="<?php eher("pass2"); ?>" style="width: 200px"/></td></tr>
  <tr><th><?php __("Your email address"); ?></th>
  <td><input type="text" name="email" id="email" value="<?php eher("email"); ?>" style="width: 300px"/></td></tr>

  <tr><th><?php __("Enter this word to prove you are human"); ?><sup>*</sup></th>
  <td>
<img src="cap.php">
<br />
<input type="text" name="cap" id="cap" value="" style="width: 200px"/>
</td></tr>

</table>

<div class="wichtig">
<?php __("Don't put anything in this field"); ?><input type="text" name="url" id="url" value="" style="width: 200px"/>
</div>
  <input type="submit" name="go" value="<?php __("Create my account"); ?>" class="btn" id="go"/>
</form>

  <p>&nbsp;</p>
  <p><i><?php __("Please note that:"); ?></i>
<ul>
<li><?php __("Any account unused for 6 months will be disabled, and this login will not be allowed as registration for 6 more months. During that time, you will be allowed to recover that account if we have an email address for this account. After that, any disabled account will be permanently destroyed and the login will be available again for other users"); ?></li>
<li><?php __("We don't store your password or email in cleartext, but only a hashed version. We don't verify your email address, so write it down properly. We will only use it to send you a recover link if you lose your password."); ?></li>
</ul>
</p>


<?php
require_once("footer.php");
?>

