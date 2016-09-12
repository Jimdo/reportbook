<legend><h2 class="form-signin-heading">Anmelden</h2></legend>
<div class="col-sm-offset-4 col-sm-4">
<form action="user/login" method="POST" class="form-signin">
  <input type="username" id="username" class="form-control" placeholder="E-Mail" required autofocus name="email"></br>
  <input type="password" id="password" class="form-control" placeholder="Passwort" required name="password"></br>
  <!-- <div class="checkbox">
    <label>
      <input type="checkbox" value="remember-me"> Remember me
    </label>
  </div> -->
  <button class="btn btn-md btn-primary btn-block" type="submit">Anmelden</button></br>
<a href="/user/register?role=trainer"><p class="text-center">Als Ausbilder registrieren</p></a>
<a href="/user/register?role=trainee"><p class="text-center">Als Azubi registrieren</p></a>
</form>
</div>
