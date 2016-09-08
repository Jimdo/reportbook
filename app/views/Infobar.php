<div class="row">
  <div class="col-md-12"><legend><h2>Berichtsheft</h2></legend></div>
</div>
<div class="row">
  <div class="col-sm-offset-0 col-sm-3">
     <label>Benutzer: <?php echo $this->username; ?> | <?php echo $this->role; ?></label></br>
     <label>Datum: <?php echo date("d.m.Y"); ?> | <?php echo date("W"); ?></label>
  </div>
  <div class="col-sm-offset-8 col-sm-1"><a href="/user/login"><span class="glyphicon glyphicon-off" aria-hidden="true"></span></a></div>
</div></br>
<h4><?php echo $this->infoHeadline; ?></h4>
