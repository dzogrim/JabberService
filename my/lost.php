<?php
require_once("config.php"); 

session_start();
$debug=false;

if (isset($_POST["email"]) && isset($_POST["login"]) && isset($_POST["csrf"]) && isset($_POST["captcha"])
    ) {
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
  if (count($error)==0) {
    sleep(5); // Let create some artificial waiting for the one who want to restore many accounts ...
    // Try to create the account.
    $already=@mysql_fetch_assoc(mysql_query("SELECT id FROM accounts WHERE jabberid='".addslashes($_POST["login"]."@jabber.lqdn.fr")."';"));
    if (!$already) {
      $error[]=_("This account doesn't exist, or have been permanently destroyed. You can't restore that login now. You'd better create a new account altogether");
    }

    if (count($error)==0) {

    } // still no error ? 
  } // no error ?
} // isset ?

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
 <a href="create.php"><?php __("Create an account"); ?></a> - 
 <?php __("I lost my password"); ?> - 
 <a href="disabled.php"><?php __("My account is disabled"); ?></a>
</p>

<h1><?php __("I lost my password on this Jabber server"); ?></h1>

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

<p><?php __("If you have lost your password in this Jabber server, and if you entered an email address when you created that account, please enter your login and the email you use at that time. You will receive an email with a link to reset your password for this account."); ?></p>

<form method="post" action="lost.php">
  <input type="hidden" name="csrf" value="<?php echo csrf_gen(); ?>" />
  <table style="width: 700px">
  <tr><th style="width: 200px"><?php __("Login"); ?><sup>*</sup> <br /><i><small><?php __("3 characters or more"); ?></small></i></th>
  <td><input type="text" name="login" id="login" value="<?php eher("login"); ?>" style="width: 200px" />@jabber.lqdn.fr</td></tr>

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
  <input type="submit" name="go" value="<?php __("Send me a password restore link by mail"); ?>" class="btn" id="go"/>
</form>

  <p>&nbsp;</p>


<?php
require_once("footer.php");
?>

