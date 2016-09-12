<?php use \Jimdo\Reports\Report as Report; ?>
<form action="<?php echo $this->action; ?>" method="POST">
  <fieldset>

<div class="row">
    <legend><?php echo $this->legend; ?></legend>
</div>

<div class="row">
    <?php if (is_array($this->errorMessages)):
    foreach ($this->errorMessages as $error): ?>
            <div class="alert alert-danger col-md-10 col-md-offset-2" role="alert"><strong><?php echo $error; ?></strong></div>
    <?php endforeach;
    endif; ?>
</div>

    <div class="row">
        <label class="col-md-2 control-label" for="calendarWeek">Kalenderwoche: </label>
        <div class="col-md-2 col-md-offset-0">
            <input class="form-control" type="text" placeholder="Kalenderwoche"
            <?php echo $this->readonly; ?> id="calendarWeek" name="calendarWeek" value="<?php echo $this->calendarWeek; ?>"></br>
        </div>
    </div>

    <div class="row">
        <label class="col-md-2 control-label" for="date">Datum: </label>
        <div class="col-md-2 col-md-offset-0">
            <input class="form-control" type="text" placeholder="Datum"
            <?php echo $this->readonly; ?> id="date" name="date" value="<?php echo $this->date; ?>"></br>
        </div>
    </div>

    <div class="row">
        <label class="col-md-2 control-label" for="calendarWeek">Bericht: </label>
        <div class="col-md-10 col-md-offset-0">
            <textarea <?php echo $this->readonly; ?> id="content" name="content" class="form-control" rows="15"><?php echo $this->content; ?></textarea></br>
        </div>
    </div>

    <div class="row">
    <?php if ($this->role === 'TRAINEE'): ?>
        <?php if ($this->status !== Report::STATUS_APPROVED && $this->status !== Report::STATUS_APPROVAL_REQUESTED): ?>
            <input type="hidden" id="reportId" name="reportId" value="<?php echo $this->reportId; ?>" />
            <button type="submit" class="btn btn-primary col-md-offset-10 col-md-2"><?php echo $this->buttonName; ?></button>
        <?php endif; ?>
    <?php endif; ?>
    </div>
  </fieldset>
</form>

<?php if ($this->role === 'TRAINER' && $this->status !== Report::STATUS_DISAPPROVED && $this->status !== Report::STATUS_APPROVED && $this->status !== Report::STATUS_REVISED): ?>

<div class="form-group form-group-md col-md-offset-0 col-md-12">
    <form action="/report/actions" method="POST">

    <div>
        <input type="hidden" name="reportId" value="<?php echo $this->reportId; ?>">

        <button type="submit" id="disapprove" value="disapprove" name="action" class="btn btn-default col-md-2 col-md-offset-8" style="margin-right: 2px; padding-right: 15px; width: 180px;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Ablehnen</button>
        <button type="submit" id="approve" value="approve" name="action" class="btn btn-primary col-md-2 col-md-offset-0" style="margin-left: 5px; padding-right: 15px; width: 180px;"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Genehmigen</button>
    </div>

    </form>
</div>
<?php endif; ?>
