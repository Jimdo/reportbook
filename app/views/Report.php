<?php use \Jimdo\Reports\Report as Report; ?>
<h1><?php echo $this->title; ?></h1>

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
    <div>
      <label for="calendarWeek">Kalenderwoche: </label>
      <input <?php echo $this->readonly; ?> type="text" id="calendarWeek" name="calendarWeek" value="<?php echo $this->calendarWeek; ?>" />
    </div>
    <div>
      <label for="date">Datum:</label>
      <input <?php echo $this->readonly; ?> type="text" id="date" name="date" value="<?php echo $this->date; ?>" />
    </div>
    <div>
      <label for="content">Bericht:</label>
      <textarea <?php echo $this->readonly; ?> id="content" name="content"><?php echo $this->content; ?></textarea>
    </div>
    <div>
    <?php if ($this->role === 'Trainee'): ?>
        <input type="hidden" id="reportId" name="reportId" value="<?php echo $this->reportId; ?>" />
        <button type="submit"><?php echo $this->buttonName; ?></button>
    <?php endif; ?>
    </div>
  </fieldset>
</form>

<?php if ($this->role === 'Trainer' && $this->status !== Report::STATUS_DISAPPROVED && $this->status !== Report::STATUS_APPROVED): ?>
    <form action="/report/approve" method="POST">
    <div>
        <input type="hidden" name="reportId" value="<?php echo $this->reportId; ?>">
        <input type="hidden" name="reportAction" value="approve">
        <button type="submit">Genehmigen</button>
    </div>
    </form>
    <form action="/report/disapprove" method="POST">
    <div>
        <input type="hidden" name="reportId" value="<?php echo $this->reportId; ?>">
        <input type="hidden" name="reportAction" value="disapprove">
        <button type="submit">Ablehnen</button>
    </div>
    </form>
<?php endif; ?>
