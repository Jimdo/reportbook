<legend><h2 class="form-signin-heading">Anmelden</h2></legend>
<div class="col-sm-offset-4 col-sm-4">

<form action="user/login" method="POST" class="form-horizontal">

<div class="form-group">
    <label class="col-sm-4 control-label">Benutzername</label>
    <div class="col-sm-8">
        <input type="identifier" name="identifier" class="form-control" id="identifier" placeholder="Benutzername / E-Mail">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-4 control-label">Passwort</label>
    <div class="col-sm-8">
        <input type="password" name="password" class="form-control" id="password" placeholder="Passwort">
    </div>
</div>

  <div class="form-group">
      <div class="col-sm-8 col-sm-offset-4">
          <button class="btn btn-md btn-primary btn-block" type="submit">Anmelden</button>
      </div>
  </div>

  <div class="form-group">
      <div class="col-sm-8 col-sm-offset-4">
          <a href="/user/register?role=TRAINER"><p class="text-center">Als Ausbilder registrieren</p></a>
          <a href="/user/register?role=TRAINEE"><p class="text-center">Als Azubi registrieren</p></a>
      </div>
  </div>

</form>
</div>
