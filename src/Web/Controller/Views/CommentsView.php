<div class="form-horizontal">

<?php foreach ($this->comments as $comment) :
    $editActive = false; ?>

    <div class="row">
        <div class="col-sm-offset-2 col-sm-1">
            <div class="thumbnail">
                <img class="img-responsive user-photo" src="/profile/image?id=<?php echo $comment->userId(); ?>">
            </div>
        </div>
        <div class="col-sm-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><?php echo $this->userService->findUserById($comment->userId())->username(); ?></strong>
                    <span class="text-muted"><?php echo $comment->date(); ?></span>

                    <?php if ($comment->userId() === $this->userId) : ?>

                        <div style="float:right">
                            <form action="/comment/deleteComment" method="POST">

                                <input type="hidden" id="reportId" name="reportId" value="<?php echo $this->reportId; ?>"/>
                                <input type="hidden" id="traineeId" name="traineeId" value="<?php echo $this->traineeId; ?>"/>
                                <input type="hidden" name="userId" value="<?php echo $this->userId; ?>">
                                <input type="hidden" name="commentId" value="<?php echo $comment->id(); ?>">
                                <button type="submit" class="btn-link glyphicon glyphicon-trash" onclick="return confirm('Soll der Bericht wirklich gelöscht werden?')" aria-hidden="true"></button>

                            </form>
                        </div>

                        <div style="float:right">
                            <a href="#changeComment<?php echo $comment->id(); ?>" data-toggle="collapse" class="glyphicon glyphicon-pencil"></a>
                        </div>

                    <?php endif; ?>

                    <div style="float:right">
                        <?php if ($comment->status() === 'EDITED') : ?>
                            <span class="text-muted" style="margin-right:0.5em;"><?php echo $this->viewHelper->getTranslationForStatus($comment->status()); ?></span>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="panel-body">

                    <?php echo nl2br($comment->content());?>

                    <div class="collapse" id="changeComment<?php echo $comment->id(); ?>">
                        <form action="/comment/editComment" method="POST" class="form-horizontal">

                            <div class="card card-block">
                                    <input type="hidden" name="reportId" value="<?php echo $this->reportId; ?>">
                                    <input type="hidden" name="userId" value="<?php echo $this->userId; ?>">
                                    <input type="hidden" name="commentId" value="<?php echo $comment->id(); ?>">
                                    <input type="hidden" id="traineeId" name="traineeId" value="<?php echo $this->traineeId; ?>">
                                    <textarea style="resize: none" rows="5" name="newComment" class="form-control"><?php echo $comment->content();?></textarea><br>
                            </div>

                            <div class="form-group">
                                <div  class="col-sm-offset-10 col-sm-2 ">
                                    <button class="btn btn-md btn-default btn-block" type="submit">Speichern</button>
                                </div>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

<?php endforeach; ?>

    <div class="col-sm-offset-10 col-sm-2">

        <?php if ($this->showCreateCommentButton) : ?>
            <p class="form-control-static">
                <button style="width: 175px; padding-right: 10px;" class="btn btn-md btn-default" data-toggle="collapse" data-target="#createComment">Kommentar verfassen</button>
            </p>
        <?php endif; ?>

    </div>
    <div class="form-group">
        <div class="collapse" id="createComment">
            <form action="/comment/createComment" method="POST" class="form-horizontal">
                <div class="card card-block">
                    <div class="col-sm-offset-2 col-sm-10">
                        <textarea  style="resize:none" type="textarea" name="content" class="form-control" id="createComment" rows="5" placeholder="Dein Kommentar"></textarea></br>
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
