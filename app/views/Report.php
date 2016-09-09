<?php use \Jimdo\Reports\Report as Report; ?>


<?php if (is_array($this->errorMessages)):
foreach ($this->errorMessages as $error): ?>
<ul>
    <li><?php echo $error; ?></li>
</ul>
<?php endforeach;
endif; ?>

<form action="<?php echo $this->action; ?>" method="POST">
  <fieldset>
    <legend><?php echo $this->legend; ?></legend>

    <div class="form-group form-group-md col-md-offset-0 col-md-12">
        <label class="col-md-2 control-label" for="calendarWeek">Kalenderwoche: </label>
        <div class="col-md-2 col-md-offset-0">
            <input class="form-control" type="text" placeholder="Kalenderwoche"
            <?php echo $this->readonly; ?> id="calendarWeek" name="calendarWeek" value="<?php echo $this->calendarWeek; ?>">
        </div>
    </div>

    <div class="form-group form-group-md col-md-offset-0 col-md-12">
        <label class="col-md-2 control-label" for="date">Datum: </label>
        <div class="col-md-2 col-md-offset-0">
            <input class="form-control" type="text" placeholder="Datum"
            <?php echo $this->readonly; ?> id="date" name="date" value="<?php echo $this->date; ?>">
        </div>
    </div>

    <div class="form-group form-group-md col-md-offset-0 col-md-12">
        <label class="col-md-2 control-label" for="calendarWeek">Bericht: </label>
        <div class="col-md-10 col-md-offset-0">
            <textarea <?php echo $this->readonly; ?> id="content" name="content" class="form-control" rows="15"><?php echo $this->content; ?></textarea>
        </div>
    </div>

    <div class="form-group form-group-md col-md-offset-0 col-md-12">
    <?php if ($this->role === 'Trainee'): ?>
        <?php if ($this->status !== Report::STATUS_APPROVED && $this->status !== Report::STATUS_APPROVAL_REQUESTED): ?>
            <input type="hidden" id="reportId" name="reportId" value="<?php echo $this->reportId; ?>" />
            <button type="submit" class="btn btn-primary col-md-offset-10 col-md-2"><?php echo $this->buttonName; ?></button>
        <?php endif; ?>
    <?php endif; ?>
    </div>
  </fieldset>
</form>

<?php if ($this->role === 'Trainer' && $this->status !== Report::STATUS_DISAPPROVED && $this->status !== Report::STATUS_APPROVED && $this->status !== Report::STATUS_REVISED): ?>

<div class="form-group form-group-md col-md-offset-0 col-md-12">
    <form action="/report/disapprove" method="POST">
    <div>
        <input type="hidden" name="reportId" value="<?php echo $this->reportId; ?>">
        <input type="hidden" name="reportAction" value="disapprove">
        <button type="submit" class="btn btn-default col-md-2 col-md-offset-8" style="margin-right: 2px; padding-right: 15px; width: 180px;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Ablehnen</button>
    </div>
    </form>


    <form action="/report/approve" method="POST">
    <div>
        <input type="hidden" name="reportId" value="<?php echo $this->reportId; ?>">
        <input type="hidden" name="reportAction" value="approve">
        <button type="submit" class="btn btn-primary col-md-2 col-md-offset-0" style="margin-left: 5px; padding-right: 15px; width: 180px;"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Genehmigen</button>
    </div>
    </form>
</div>
<?php endif; ?>
