<h3><?php echo $this->infoHeadline; ?></h3>
<p>
  Benutzer: <?php echo session('username'); ?><br/>
  Sie sind eingeloggt als: <?php echo session('role'); ?><br/>
  <a href="/user/login">Abmelden</a><br/>
  Datum/KW: <?php echo date("d.m.Y / W"); ?> <br/>
</p>
