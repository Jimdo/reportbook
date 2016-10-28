<div class="form-horizontal">

    <div class="form-group">

        <div class="col-sm-offset-10 col-sm-2">
            <p class="form-control-static">
                <button class="btn btn-md btn-default btn-block" data-toggle="collapse" data-target="#createComment">Kommentar verfassen</button>
            </p>
         </div>

         <div class="collapse" id="createComment">
             <form action="/user/changeForename" method="POST" class="form-horizontal">
                 <div class="card card-block">

                     <div class="col-sm-offset-2 col-sm-10">
                         <textarea type="textarea" name="forename" class="form-control" id="createComment" rows="5" placeholder="Dein Kommentar"></textarea></br>
                     </div>

                     <div class="col-sm-offset-10 col-sm-2">
                         <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                     </div>

                  </div>
              </form>
        </div>
    </div>
</div>
