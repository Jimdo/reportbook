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

                <p class="form-control-static">Tom
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

                <p class="form-control-static">Stich
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

            <label class="col-sm-6 control-label">Benutzername</label>
            <div class="col-sm-6">

                <p class="form-control-static">tomstich
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

                <p class="form-control-static">tomstich@jimdo.com
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
    <button class="btn btn-md btn-default btn-block" type="submit">Passwort ändern</button>


</div>
</div>
