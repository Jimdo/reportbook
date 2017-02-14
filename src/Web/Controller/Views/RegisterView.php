<legend><h2 class="form-signin-heading">Registrieren</h2></legend>
<div class="col-sm-offset-4 col-sm-4">

    <div class="row">
        <?php if (is_array($this->errorMessages)) :
            foreach ($this->errorMessages as $error) : ?>
                <div class="alert alert-danger col-sm-12" role="alert"><strong><?php echo $error; ?></strong></div>
            <?php endforeach;
        endif; ?>
    </div>

    <form action="/user/createUser" class="form-horizontal" method="POST">

        <div class="form-group">
            <label class="col-sm-4 control-label">Vorname</label>
            <div class="col-sm-8">
                <input type="forename" name="forename" class="form-control" id="forename" placeholder="Vorname">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4 control-label">Nachname</label>
            <div class="col-sm-8">
                <input type="surname" name="surname" class="form-control" id="surname" placeholder="Nachname">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4 control-label">Benutzername</label>
            <div class="col-sm-8">
                <input type="username" name="username" class="form-control" id="username" placeholder="Benutzername">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4 control-label">E-Mail</label>
            <div class="col-sm-8">
                <input type="email" name="email" class="form-control" id="email" placeholder="E-Mail Adresse">
            </div>
        </div>

        <div class="form-group">
            <label for="password" class="col-sm-4 control-label">Passwort</label>
            <div class="col-sm-8">
                <input type="password" name="password" class="form-control" id="password" placeholder="Passwort">
            </div>
        </div>

        <div class="form-group">
            <div for="password" class="col-sm-4 control-label"></div>
            <div class="col-sm-8">
                <input type="password" name="passwordConfirmation" class="form-control" id="passwordConfirmation" placeholder="Passwort bestätigen">
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4 control-label"></div>
            <div class="col-sm-8">
                <button class="btn btn-md btn-primary btn-block" type="submit">Registrieren</button></br>
                <a href="/user/login"><p class="text-center">Zurück zum Login</p></a>
            </div>
        </div>

        <input type="hidden" name="role" value="<?php echo $this->role; ?>">

    </form>
</div>
