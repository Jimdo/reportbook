<div class="row">
    <legend>Dein Profil</legend>
</div>

<div class="row">
    <?php if (is_array($this->errorMessages)):
    foreach ($this->errorMessages as $error): ?>
            <div class="alert alert-danger col-sm-12" role="alert"><strong><?php echo $error; ?></strong></div>
    <?php endforeach;
    endif; ?>
</div>

<div class="col-sm-12" >
    <img src="data:image/gif;base64,<?php echo  $this->user->image(); ?>"  style="width:212px;height:212px;border:1px solid gray;"/>
    <a href="#changeImage" data-toggle="collapse" class="glyphicon glyphicon-pencil"></a>

    <br>
    <br>

    <div class="collapse" id="changeImage">
        <form action="/user/upload" method="post" enctype="multipart/form-data">
            <label class="btn btn-default btn-file">
            Bild öffnen <input type="file" name="fileToUpload" id="fileToUpload" style="display: none;">
            </label>
            <input type="submit" value="Upload Image" name="submit" class="btn btn-primary">
        </form>
    </div>

    <a href="/user/changePassword">Passwort ändern</a>

</div>

<div class="row"></div>

<div class="col-sm-offset-2 col-sm-5">
    <div class="col-sm-offset-1 col-sm-10">
        <h4>Persönliche Daten</h4><br>
    </div>

    <div class="form-horizontal">

        <div class="form-group">

            <label class="col-sm-6 control-label">Vorname</label>
            <div class="col-sm-6">

                <p class="form-control-static"><?php echo $this->user->forename(); ?>
                    <a href="#changeForename" data-toggle="collapse" class="glyphicon glyphicon-pencil"></a>
                </p>
             </div>

             <div class="collapse" id="changeForename">
                 <form action="/user/changeForename" method="POST" class="form-horizontal">
                     <div class="card card-block">
                         <div class="col-sm-offset-6 col-sm-6">
                              <input type="text" name="forename" class="form-control" id="forename" placeholder="Neuer Vorname"></br>
                              <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                          </div>
                      </div>
                  </form>
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
                 <form action="/user/changeSurname" method="POST" class="form-horizontal">
                     <div class="card card-block">
                         <div class="col-sm-offset-6 col-sm-6">
                             <input type="text" name="surname" class="form-control" id="surname" placeholder="Neuer Nachname"></br>
                             <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                         </div>
                     </div>
                 </form>
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
                 <form action="/user/changeDateOfBirth" method="POST" class="form-horizontal">
                     <div class="card card-block">
                         <div class="col-sm-offset-6 col-sm-6">
                             <input type="text" name="dateOfBirth" class="form-control" id="dateOfBirth" placeholder="Neues Geburtsdatum"></br>
                             <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                         </div>
                     </div>
                 </form>
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
                 <form action="/user/changeUsername" method="POST" class="form-horizontal">
                     <div class="card card-block">
                         <div class="col-sm-offset-6 col-sm-6">
                             <input type="text" name="username" class="form-control" id="username" placeholder="Neuer Benutzername"></br>
                             <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                         </div>
                     </div>
                 </form>
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
                 <form action="/user/changeEmail" method="POST" class="form-horizontal">
                     <div class="card card-block">
                         <div class="col-sm-offset-6 col-sm-6">
                             <input type="text" name="email" class="form-control" id="email" placeholder="Neue E-Mail"></br>
                             <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                         </div>
                     </div>
                 </form>
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

                <p class="form-control-static"><?php echo $this->user->company(); ?>
                    <a href="#changeCompany" data-toggle="collapse" class="glyphicon glyphicon-pencil"></a>
                </p>
             </div>

             <div class="collapse" id="changeCompany">
                 <form action="/user/changeCompany" method="POST" class="form-horizontal">
                     <div class="card card-block">
                         <div class="col-sm-offset-6 col-sm-6">
                             <input type="text" name="company" class="form-control" id="company" placeholder="Neue Firma"></br>
                             <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                         </div>
                     </div>
                 </form>
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
                 <form action="/user/changeJobTitle" method="POST" class="form-horizontal">
                     <div class="card card-block">
                         <div class="col-sm-offset-6 col-sm-6">
                             <input type="text" name="jobTitle" class="form-control" id="jobTitle" placeholder="Neue Berufsbezeichung"></br>
                             <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                         </div>
                     </div>
                 </form>
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

                    <p class="form-control-static"><?php echo $this->user->school(); ?>
                        <a href="#changeSchool" data-toggle="collapse" class="glyphicon glyphicon-pencil"></a>
                    </p>
                 </div>

                 <div class="collapse" id="changeSchool">
                     <form action="/user/changeSchool" method="POST" class="form-horizontal">
                         <div class="card card-block">
                             <div class="col-sm-offset-6 col-sm-6">
                                 <input type="text" name="school" class="form-control" id="school" placeholder="Neue Schule"></br>
                                 <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                             </div>
                         </div>
                     </form>
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
                     <form action="/user/changeGrade" method="POST" class="form-horizontal">
                         <div class="card card-block">
                             <div class="col-sm-offset-6 col-sm-6">
                                 <input type="text" name="grade" class="form-control" id="grade" placeholder="Neue Klasse"></br>
                                 <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                             </div>
                         </div>
                     </form>
                 </div>

            </div>

            <div class="form-group">

                <label class="col-sm-6 control-label">Ausbildungsbeginn</label>
                <div class="col-sm-6">

                    <p class="form-control-static"><?php echo $this->user->startOfTraining(); ?>
                        <a href="#changeStartOfTraining" data-toggle="collapse" class="glyphicon glyphicon-pencil"></a>
                    </p>
                 </div>

                 <div class="collapse" id="changeStartOfTraining">
                     <form action="/user/changeStartOfTraining" method="POST" class="form-horizontal">
                         <div class="card card-block">
                             <div class="col-sm-offset-6 col-sm-6">
                                 <input type="text" name="startOfTraining" class="form-control" id="startOfTraining" placeholder="Neuer Ausbildungsbeginn"></br>
                                 <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                             </div>
                         </div>
                     </form>
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
                     <form action="/user/changeTrainingYear" method="POST" class="form-horizontal">
                         <div class="card card-block">
                             <div class="col-sm-offset-6 col-sm-6">
                                 <input type="text" name="trainingYear" class="form-control" id="trainingYear" placeholder="Neues Ausbildungsjahr"></br>
                                 <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                             </div>
                         </div>
                     </form>
                 </div>

            </div>
        </div>
    </div>

<?php endif ?>
