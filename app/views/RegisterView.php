<legend><h2 class="form-signin-heading">Registrieren</h2></legend>
<div class="col-sm-offset-4 col-sm-4">
    <form class="form-horizontal">

        <div class="form-group">
            <label class="col-sm-3 control-label">Vorname</label>
            <div class="col-sm-9">
                <input type="forename" class="form-control" id="forename" placeholder="Vorname">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">Nachname</label>
            <div class="col-sm-9">
                <input type="surname" class="form-control" id="surname" placeholder="Nachname">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">Email</label>
            <div class="col-sm-9">
                <input type="email" class="form-control" id="email" placeholder="E-Mail Adresse">
            </div>
        </div>

        <div class="form-group">
            <label for="password" class="col-sm-3 control-label">Passwort</label>
            <div class="col-sm-9">
                <input type="password" class="form-control" id="password" placeholder="Passwort">
            </div>
        </div>

        <div class="form-group">
            <div for="password" class="col-sm-3 control-label"></div>
            <div class="col-sm-9">
                <input type="password" class="form-control" id="passwordConfirmation" placeholder="Passwort bestätigen">
            </div>
        </div>

        <button class="btn btn-md btn-primary btn-block" type="submit">Registrieren</button></br>

        <input type="hidden" name="role" value="<?php echo $this->role; ?>">

    </form>
</div>
