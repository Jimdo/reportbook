<div class="row">
    <nav class="navbar navbar-default" style="font-size:1.2em; margin-top:20px;">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><strong>Berichtsheft</strong></a>
          <ul class="nav navbar-nav">
            <li><a href="/report/list">Übersicht</a></li>
            <?php if ($this->trainerRole || $this->adminRole) : ?>
                <li><a href="/user/userlist">Benutzer</a></li>
            <?php endif; ?>
          </ul>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="/user/profile"><strong><span class="glyphicon glyphicon-user" aria-hidden="true"></span></strong></a></li>
            <li><a href="/user/login"><strong><span class="glyphicon glyphicon-off" aria-hidden="true"></span></strong></a></li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>

</div>

<?php if ($this->hideInfos === false) : ?>
    <div class="row">
      <div class="col-sm-offset-0 col-sm-3">
         <label>Benutzer: <?php echo $this->username; ?> | <?php echo $this->viewHelper->getTranslationForRole($this->role); ?></label></br>
         <label>Datum: <?php echo date("d.m.Y"); ?> | <?php echo date("W"); ?></label>
      </div>
    </div>
<?php endif; ?>

</br>
