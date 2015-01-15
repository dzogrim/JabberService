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

myheader();
require_once("css.php"); 
?>

<p id="cmenu">
<b><?php __("Menu:"); ?></b>
 <a href="create.php"><?php __("Create an account"); ?></a> - 
 <a href="lost.php"><?php __("I lost my password"); ?></a> - 
<?php __("My account is disabled"); ?>
</p>

<h1><?php __("My account is disabled"); ?></h1>

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

<p><?php __("No it's not"); ?></p>
  <p>&nbsp;</p>


<?php
  myfooter();
?>

