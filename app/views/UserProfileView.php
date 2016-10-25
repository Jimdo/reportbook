<div class="row">
    <legend><?php echo 'Profil von ' . $this->profile->forename() . ' ' . $this->profile->surname(); ?></legend>
</div>

<div class="col-sm-12" >
    <img src= "/profile/image?id=<?php echo $this->profile->userId(); ?>" alt="Profilbild" style="width:212px; height:212px; border:1px solid gray;">
</div>

<div class="col-sm-offset-2 col-sm-5">
    <div class="col-sm-offset-1 col-sm-10">
        <h4>Persönliche Daten</h4><br>
    </div>

    <div class="form-horizontal">

        <div class="form-group">
            <label class="col-sm-6 control-label">Vorname</label>
            <div class="col-sm-6">
                <p class="form-control-static"><?php echo $this->user->forename(); ?></p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-6 control-label">Nachname</label>
            <div class="col-sm-6">
                <p class="form-control-static"><?php echo $this->user->surname(); ?></p>
             </div>
        </div>

        <div class="form-group">
            <label class="col-sm-6 control-label">Geburtsdatum</label>
            <div class="col-sm-6">
                <p class="form-control-static"><?php echo $this->user->dateOfBirth(); ?></p>
             </div>
        </div>

        <div class="form-group">
            <label class="col-sm-6 control-label">Benutzername</label>
            <div class="col-sm-6">
                <p class="form-control-static"><?php echo $this->user->username(); ?></p>
             </div>
        </div>

        <div class="form-group">

            <label class="col-sm-6 control-label">E-Mail</label>
            <div class="col-sm-6">

                <p class="form-control-static"><?php echo $this->user->email(); ?></p>
             </div>
        </div>
    </div>
</div>

<div class="col-sm-offset-2 col-sm-5">

    <div class="col-sm-offset-1 col-sm-10">
        <h4>Betriebliche Daten</h4><br>
    </div>

    <div class="form-horizontal">

        <div class="form-group">

            <label class="col-sm-6 control-label">Firma</label>
            <div class="col-sm-6">

                <p class="form-control-static"><?php echo $this->user->company(); ?></p>
            </div>
        </div>

        <div class="form-group">

            <label class="col-sm-6 control-label">Berufsbezeichung</label>
            <div class="col-sm-6">

                <p class="form-control-static"><?php echo $this->user->jobTitle(); ?></p>
             </div>
        </div>
    </div>
</div>

<?php if ($this->user->roleName() === 'TRAINEE'): ?>
    <div class="col-sm-offset-2 col-sm-5">
        <div class="col-sm-offset-1 col-sm-10">
            <h4>Schulische Daten</h4><br>
        </div>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-6 control-label">Schule</label>
                <div class="col-sm-6">
                    <p class="form-control-static"><?php echo $this->user->school(); ?></p>
                 </div>
            </div>

            <div class="form-group">
                <label class="col-sm-6 control-label">Klasse</label>
                <div class="col-sm-6">
                    <p class="form-control-static"><?php echo $this->user->grade(); ?></p>
                 </div>
            </div>

            <div class="form-group">
                <label class="col-sm-6 control-label">Ausbildungsbeginn:</label>
                <div class="col-sm-6">
                    <p class="form-control-static"><?php echo $this->user->startOfTraining(); ?></p>
                 </div>
            </div>

            <div class="form-group">
                <label class="col-sm-6 control-label">Ausbildungsjahr</label>
                <div class="col-sm-6">
                    <p class="form-control-static"><?php echo $this->user->trainingYear(); ?></p>
                 </div>
            </div>
        </div>
    </div>
<?php endif ?>

<div class="row"></div>
