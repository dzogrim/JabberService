<?php
/*
    Prosody Account Manager
    Copyright (C) 2014 Benjamin Sonntag <benjamin@sonntag.fr>, SKhaen <skhaen@cyphercat.eu>   

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

    You can find the source code of this software at https://github.com/LaQuadratureDuNet/JabberService
 */

require_once("config.php"); 

require_once("docreate.php");

require_once("header.php");
require_once("css.php"); 
?>

<p id="cmenu">
<b><?php __("Menu:"); ?></b>
 <?php __("Create an account"); ?> - 
 <a href="lost.php"><?php __("I lost my password"); ?></a> - 
 <a href="recover.php"><?php __("My account is disabled"); ?></a>
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
  <td><input type="text" name="login" id="login" value="<?php eher("login"); ?>" style="width: 200px" />@<?php echo $domain; ?></td></tr>

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
  <p><i><?php __("Please note that:"); ?></i></p>
<ul>
<li><?php __("Any account unused for 6 months will be disabled, and this login will not be allowed as registration for 6 more months. During that time, you will be allowed to recover that account if we have an email address for this account. After that, any disabled account will be permanently destroyed and the login will be available again for other users"); ?></li>
<li><?php __("We don't store your password or email in cleartext, but only a hashed version. We don't verify your email address, so write it down properly. We will only use it to send you a recover link if you lose your password."); ?></li>
</ul>

<?php
require_once("footer.php");
?>

