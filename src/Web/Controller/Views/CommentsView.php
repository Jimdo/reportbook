<div class="form-horizontal">

    <div class="form-group">

        <div class="col-sm-offset-10 col-sm-2">
            <p class="form-control-static">
                <button class="btn btn-md btn-default btn-block" data-toggle="collapse" data-target="#createComment">Kommentar verfassen</button>
            </p>
         </div>

         <div class="collapse" id="createComment">
             <form action="/comment/createComment" method="POST" class="form-horizontal">
                 <div class="card card-block">

                     <div class="col-sm-offset-2 col-sm-10">
                         <textarea type="textarea" name="content" class="form-control" id="createComment" rows="5" placeholder="Dein Kommentar"></textarea></br>
                     </div>

                     <div class="col-sm-offset-10 col-sm-2">
                         <input type="hidden" id="reportId" name="reportId" value="<?php echo $this->reportId; ?>" />
                         <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                     </div>

                  </div>
              </form>
        </div>
    </div>
</div>
