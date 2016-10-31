<div class="form-horizontal">

<?php $comments = $this->commentService->findCommentsByReportId($this->reportId);
      foreach ($comments as $comment): ?>

    <div class="row">
        <div class="col-sm-offset-2 col-sm-1">
            <div class="thumbnail">
                <img class="img-responsive user-photo" src="/profile/image?id=<?php echo $comment->userId(); ?>">
            </div>
        </div>
        <div class="col-sm-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><?php echo $comment->userId(); ?></strong>
                    <span class="text-muted"><?php echo $comment->date(); ?></span>
                </div>
                <div class="panel-body">
                    <?php echo $comment->content(); ?>
                </div>
            </div>
        </div>
    </div>

<?php endforeach; ?>

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
                        <input type="hidden" id="traineeId" name="traineeId" value="<?php echo $this->traineeId; ?>" />
                        <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
