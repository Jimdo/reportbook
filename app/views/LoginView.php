<legend><h2 class="form-signin-heading">Anmelden</h2></legend>
<div class="col-sm-offset-4 col-sm-4">
<form action="user/login" method="POST" class="form-horizontal">

<div class="form-group">
    <label class="col-sm-3 control-label">E-Mail</label>
    <div class="col-sm-9">
        <input type="email" name="email" class="form-control" id="email" placeholder="E-Mail">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Passwort</label>
    <div class="col-sm-9">
        <input type="password" name="password" class="form-control" id="password" placeholder="Passwort">
    </div>
</div>

  <!-- <div class="checkbox">
    <label>
      <input type="checkbox" value="remember-me"> Remember me
    </label>
  </div> -->

  <div class="form-group">
      <div class="col-sm-9 col-sm-offset-3">
          <button class="btn btn-md btn-primary btn-block" type="submit">Anmelden</button>
      </div>
  </div>

  <div class="form-group">
      <div class="col-sm-9 col-sm-offset-3">
          <a href="/user/register?role=TRAINER"><p class="text-center">Als Ausbilder registrieren</p></a>
          <a href="/user/register?role=TRAINEE"><p class="text-center">Als Azubi registrieren</p></a>
      </div>
  </div>

</form>
</div>
