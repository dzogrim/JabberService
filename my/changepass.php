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

require_once("dochangepass.php"); 

require_once("header.php");
require_once("css.php"); 
?>

<p id="cmenu">
<b><?php __("Menu:"); ?></b>
 <a href="create.php"><?php __("Create an account"); ?></a> - 
 <?php __("I lost my password"); ?> - 
 <a href="disabled.php"><?php __("My account is disabled"); ?></a>
</p>

<h1><?php __("Change your password"); ?></h1>

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

<form method="post" action="/my/changepass.php">
  <input type="hidden" name="csrf" value="<?php echo csrf_gen(); ?>" />
  <input type="hidden" name="id" value="<?php echo $id; ?>" />
  <input type="hidden" name="key" value="<?php echo $key; ?>" />
  <table style="width: 700px">
  <tr><th style="width: 250px"><?php __("New password"); ?><sup>*</sup></th>
  <td style="width: 450px"><input type="password" name="pass1" id="pass1" value="<?php eher("pass1"); ?>" style="width: 200px"/></td></tr>
  <tr><th><?php __("New password (again)"); ?><sup>*</sup></th>
  <td><input type="password" name="pass2" id="pass2" value="<?php eher("pass2"); ?>" style="width: 200px"/></td></tr>
</table>

<div class="wichtig">
<?php __("Don't put anything in this field"); ?><input type="text" name="url" id="url" value="" style="width: 200px"/>
</div>
  <input type="submit" name="go" value="<?php __("Change my password"); ?>" class="btn" id="go"/>
</form>

  <p>&nbsp;</p>

<p><?php if (isset($support_pgp)) {  $support="<a href=\"http://pool.sks-keyservers.net/pks/lookup?op=get&amp;search=".$support_pgp."\">".str_replace("@"," [at] ",$mail_from)."</a>"; } else {   $support=str_replace("@"," [at] ",$mail_from); } printf(_("If you are lost or need help, you can contact the tech team at %s"),$support); ?></p>

<?php
require_once("footer.php");
?>

