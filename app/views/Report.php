<?php use \Jimdo\Reports\Report as Report; ?>
<h1><?php echo $this->title; ?></h1>
<form action="<?php echo $this->action; ?>" method="POST">
  <fieldset>
    <legend><?php echo $this->legend; ?></legend>
    <div>
      <label for="calendarWeek">Kalenderwoche: </label>
      <input <?php echo $this->readonly; ?> type="text" id="calendarWeek" name="calendarWeek" value=<?php echo $this->calendarWeek; ?> />
    </div>
    <div>
      <label for="date">Datum:</label>
      <input <?php echo $this->readonly; ?> type="text" id="date" name="date" value=<?php echo $this->date; ?> />
    </div>
    <div>
      <label for="content">Bericht:</label>
      <textarea <?php echo $this->readonly; ?> id="content" name="content"><?php echo $this->content; ?></textarea>
    </div>
    <div>
    <?php if ($this->role === 'Trainee'): ?>
        <input type="hidden" id="reportId" name="reportId" value=<?php echo $this->reportId; ?> />
        <input type="hidden" id="reportAction" name="reportAction" value=<?php echo $this->reportAction; ?> />
        <button type="submit"><?php echo $this->buttonName; ?></button>
    <?php endif; ?>
    </div>
  </fieldset>
</form>

<?php if ($this->role === 'Trainer' && $this->status !== Report::STATUS_DISAPPROVED && $this->status !== Report::STATUS_APPROVED): ?>
    <form action="<?php echo $this->action; ?>" method="POST">
    <div>
        <input type="hidden" name="reportId" value="<?php echo $this->reportId; ?>">
        <input type="hidden" name="reportAction" value="approve">
        <button type="submit">Genehmigen</button>
    </div>
    </form>
    <form action="<?php echo $this->action; ?>" method="POST">
    <div>
        <input type="hidden" name="reportId" value="<?php echo $this->reportId; ?>">
        <input type="hidden" name="reportAction" value="disapprove">
        <button type="submit">Ablehnen</button>
    </div>
    </form>
<?php endif; ?>

<?php require 'Footer.php';?>
