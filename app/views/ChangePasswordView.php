<div class="col-sm-offset-3 col-sm-6">

<form action="/user/editPassword" method="POST" class="form-horizontal">

<div class="form-group">
    <label class="col-sm-6 control-label">Derzeitiges Passwort</label>
    <div class="col-sm-6">
        <input type="password" name="currentPassword" class="form-control" id="currentPassword" placeholder="Derzeitiges Passwort">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-6 control-label">Neues Passwort</label>
    <div class="col-sm-6">
        <input type="password" name="newPassword" class="form-control" id="newPassword" placeholder="Neues Passwort">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-6 control-label">Passwort wiederholen</label>
    <div class="col-sm-6">
        <input type="password" name="passwordConfirmation" class="form-control" id="passwordConfirmation" placeholder="Passwort wiederholen">
    </div>
</div>

<div class="form-group">
    <div class="col-sm-6 control-label"></div>
    <div class="col-sm-6">
        <button class="btn btn-md btn-primary btn-block" type="submit">Speichern</button></br>
    </div>
</div>
