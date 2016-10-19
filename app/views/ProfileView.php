        <div class="row">
            <legend>Dein Profil</legend>
        </div>

<div class="col-sm-offset-2 col-sm-5">
    <div class="col-sm-offset-1 col-sm-10">
        <h4>Persönliche Daten</h4><br>
    </div>
    <form action="/user/editPassword" method="POST" class="form-horizontal">

        <div class="form-group">

            <label class="col-sm-6 control-label">Vorname</label>
            <div class="col-sm-6">

                <p class="form-control-static"><?php echo $this->user->forename(); ?>
                    <a href="#changeForename" data-toggle="collapse" class="glyphicon glyphicon-pencil"></a>
                </p>
             </div>

             <div class="collapse" id="changeForename">
                 <div class="card card-block" >
                     <div class="col-sm-offset-6 col-sm-6">
                         <input type="text" name="newForename" class="form-control" id="newForename" placeholder="Neuer Vorname"></br>
                         <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                     </div>
                 </div>
             </div>

        </div>

        <div class="form-group">

            <label class="col-sm-6 control-label">Nachname</label>
            <div class="col-sm-6">

                <p class="form-control-static"><?php echo $this->user->surname(); ?>
                    <a href="#changeSurname" data-toggle="collapse" class="glyphicon glyphicon-pencil"></a>
                </p>
             </div>

             <div class="collapse" id="changeSurname">
                 <div class="card card-block" >
                     <div class="col-sm-offset-6 col-sm-6">
                         <input type="text" name="newSurname" class="form-control" id="newSurname" placeholder="Neuer Nachname"></br>
                         <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                     </div>
                 </div>
             </div>

        </div>

        <div class="form-group">

            <label class="col-sm-6 control-label">Geburtsdatum</label>
            <div class="col-sm-6">

                <p class="form-control-static"><?php echo $this->user->dateOfBirth(); ?>
                    <a href="#changeDateOfBirth" data-toggle="collapse" class="glyphicon glyphicon-pencil"></a>
                </p>
             </div>

             <div class="collapse" id="changeDateOfBirth">
                 <div class="card card-block" >
                     <div class="col-sm-offset-6 col-sm-6">
                         <input type="text" name="newDateOfBirth" class="form-control" id="newDateOfBirth" placeholder="Neues Geburtsdatum"></br>
                         <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                     </div>
                 </div>
             </div>

        </div>

        <div class="form-group">

            <label class="col-sm-6 control-label">Benutzername</label>
            <div class="col-sm-6">

                <p class="form-control-static"><?php echo $this->user->username(); ?>
                    <a href="#changeUsername" data-toggle="collapse" class="glyphicon glyphicon-pencil"></a>
                </p>
             </div>

             <div class="collapse" id="changeUsername">
                 <div class="card card-block" >
                     <div class="col-sm-offset-6 col-sm-6">
                         <input type="text" name="newUsername" class="form-control" id="newUsername" placeholder="Neuer Benutzername"></br>
                         <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                     </div>
                 </div>
             </div>

        </div>

        <div class="form-group">

            <label class="col-sm-6 control-label">E-Mail</label>
            <div class="col-sm-6">

                <p class="form-control-static"><?php echo $this->user->email(); ?>
                    <a href="#changeEmail" data-toggle="collapse" class="glyphicon glyphicon-pencil"></a>
                </p>
             </div>

             <div class="collapse" id="changeEmail">
                 <div class="card card-block" >
                     <div class="col-sm-offset-6 col-sm-6">
                         <input type="text" name="newEmail" class="form-control" id="newEmail" placeholder="Neue E-Mail"></br>
                         <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                     </div>
                 </div>
             </div>

        </div>

    </form>

    <div class="col-sm-offset-6 col-sm-5">
        <button class="btn btn-md btn-default btn-block" type="submit">Passwort ändern</button><br>
    </div>

</div>

<div class="col-sm-offset-2 col-sm-5">

    <div class="col-sm-offset-1 col-sm-10">
        <h4>Betriebliche Daten</h4><br>
    </div>

    <form action="/user/editPassword" method="POST" class="form-horizontal">

        <div class="form-group">

            <label class="col-sm-6 control-label">Firma</label>
            <div class="col-sm-6">

                <p class="form-control-static"><?php echo $this->user->company(); ?>
                    <a href="#changeCompany" data-toggle="collapse" class="glyphicon glyphicon-pencil"></a>
                </p>
             </div>

             <div class="collapse" id="changeCompany">
                 <div class="card card-block" >
                     <div class="col-sm-offset-6 col-sm-6">
                         <input type="text" name="newCompany" class="form-control" id="newCompany" placeholder="Neue Firma"></br>
                         <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                     </div>
                 </div>
             </div>

        </div>

        <div class="form-group">

            <label class="col-sm-6 control-label">Berufsbezeichung</label>
            <div class="col-sm-6">

                <p class="form-control-static"><?php echo $this->user->jobTitle(); ?>
                    <a href="#changeJobTitle" data-toggle="collapse" class="glyphicon glyphicon-pencil"></a>
                </p>
             </div>

             <div class="collapse" id="changeJobTitle">
                 <div class="card card-block" >
                     <div class="col-sm-offset-6 col-sm-6">
                         <input type="text" name="newJobTitle" class="form-control" id="newJobTitle" placeholder="Neue Berufsbezeichung"></br>
                         <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                     </div>
                 </div>
             </div>

        </div>
    </form>
</div>

<div class="col-sm-offset-2 col-sm-5">
    <div class="col-sm-offset-1 col-sm-10">
        <h4>Schulische Daten</h4><br>
    </div>
    <form action="/user/editPassword" method="POST" class="form-horizontal">

        <div class="form-group">

            <label class="col-sm-6 control-label">Schule</label>
            <div class="col-sm-6">

                <p class="form-control-static"><?php echo $this->user->school(); ?>
                    <a href="#changeSchool" data-toggle="collapse" class="glyphicon glyphicon-pencil"></a>
                </p>
             </div>

             <div class="collapse" id="changeSchool">
                 <div class="card card-block" >
                     <div class="col-sm-offset-6 col-sm-6">
                         <input type="text" name="newSchool" class="form-control" id="newSchool" placeholder="Neue Schule"></br>
                         <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                     </div>
                 </div>
             </div>

        </div>

        <div class="form-group">

            <label class="col-sm-6 control-label">Klasse</label>
            <div class="col-sm-6">

                <p class="form-control-static"><?php echo $this->user->grade(); ?>
                    <a href="#changeGrade" data-toggle="collapse" class="glyphicon glyphicon-pencil"></a>
                </p>
             </div>

             <div class="collapse" id="changeGrade">
                 <div class="card card-block" >
                     <div class="col-sm-offset-6 col-sm-6">
                         <input type="text" name="newGrade" class="form-control" id="newGrade" placeholder="Neue Klasse"></br>
                         <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                     </div>
                 </div>
             </div>

        </div>

        <div class="form-group">

            <label class="col-sm-6 control-label">Ausbildungsjahr</label>
            <div class="col-sm-6">

                <p class="form-control-static"><?php echo $this->user->trainingYear(); ?>
                    <a href="#changeTrainingYear" data-toggle="collapse" class="glyphicon glyphicon-pencil"></a>
                </p>
             </div>

             <div class="collapse" id="changeTrainingYear">
                 <div class="card card-block" >
                     <div class="col-sm-offset-6 col-sm-6">
                         <input type="text" name="newTrainingYear" class="form-control" id="newTrainingYear" placeholder="Neues Ausbildungsjahr"></br>
                         <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                     </div>
                 </div>
             </div>

        </div>
    </form>
</div>
