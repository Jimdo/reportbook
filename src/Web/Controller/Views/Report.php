<?php use \Jimdo\Reports\Reportbook\Report;
      use \Jimdo\Reports\Reportbook\Category; ?>
<form action="<?php echo $this->action; ?>" method="POST">
  <fieldset>

    <div class="row">
        <legend><?php echo $this->legend; ?> <?php echo $this->traineeName; ?> </legend>
    </div>

    <div class="row">
        <?php if (is_array($this->errorMessages)) :
            foreach ($this->errorMessages as $error) : ?>
                <div class="alert alert-danger col-md-10 col-md-offset-2" role="alert"><strong><?php echo $error; ?></strong></div>
            <?php endforeach;
        endif; ?>
    </div>

    <div class="row">
        <label class="col-md-2 control-label" for="calendarWeek">Kalenderwoche: </label>
        <div class="col-md-1 col-md-offset-0">
            <input class="form-control" type="text" placeholder="Kalenderwoche"
            <?php echo $this->readonly; ?> id="calendarWeek" name="calendarWeek" value="<?php echo $this->calendarWeek; ?>"></br>
        </div>
    </div>

    <div class="row">
        <label class="col-md-2 control-label" for="calendarWeek">Kalenderjahr: </label>
        <div class="col-md-1 col-md-offset-0">
            <input class="form-control" type="text"
            <?php echo $this->readonly; ?> id="calendarYear" name="calendarYear" value="<?php echo $this->calendarYear; ?>"></br>
        </div>
    </div>

    <div class="row">

        <label class="col-md-2 control-label" for="date">Kategorie: </label>
        <div class="col-md-2 col-md-offset-0">

            <label class="radio-inline">
                <input <?php echo $this->radioReadonly; ?> <?php echo $this->isCompany; ?> type="radio" name="category" value="<?php echo Category::COMPANY ?>">Betrieb
            </label>
            <label class="radio-inline">
                <input <?php echo $this->radioReadonly; ?> <?php echo $this->isSchool; ?> type="radio" name="category" value="<?php echo Category::SCHOOL ?>">Schule
            </label>

        </div>
    </div>

    </br>

    <div class="row">
        <label class="col-md-2 control-label" for="calendarWeek">Bericht: </label>
        <div class="col-md-10 col-md-offset-0">
            <textarea style="resize: none" <?php echo $this->readonly; ?> id="content" name="content" class="form-control" rows="15"><?php echo $this->content; ?></textarea></br>
        </div>
    </div>

    <div class="col-md-2 col-md-offset-10">
    <?php if ($this->isTrainee || $this->isAdmin) : ?>
        <?php if ($this->createButton) : ?>
            <input type="hidden" id="reportId" name="reportId" value="<?php echo $this->reportId; ?>" />
            <button style="width: 175px;" type="submit" class="btn btn-primary col-md-1"><?php echo $this->buttonName; ?></button>
            </br></br></br>
        <?php endif; ?>
    <?php endif; ?>
    </div>

  </fieldset>
</form>

<?php if ($this->statusButtons) : ?>

    <form action="/report/approveReport" method="POST">
        <div class="col-md-2 col-md-offset-8">

        <input type="hidden" name="reportId" value="<?php echo $this->reportId; ?>">
        <button style="width: 175px;" type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Genehmigen</button>

        </div>
    </form>

    <form action="/report/disapproveReport" method="POST">
        <div class="col-md-1">

        <input type="hidden" name="reportId" value="<?php echo $this->reportId; ?>">
        <button style="width: 175px;" type="submit" class="btn btn-default"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Ablehnen</button>

        </div>
    </form>

    </br></br></br>

<?php endif; ?>
