<?php

namespace Jimdo\Reports;

require 'bootstrap.php';

isAuthorized('Trainee');

$reportRepository = new ReportFileRepository('../reports');
$service = new ReportBookService($reportRepository);

$traineeId = session('userId');

?>

<h1>Bericht</h1>
<form action="saveReport.php" method="POST">
  <fieldset>
    <legend>Bericht bearbeiten</legend>
    <div>
      <label for="calendarWeek">Kalenderwoche: </label>
      <input type="text" id="calendarWeek" name="calendarWeek" value=<?php echo date('W'); ?>>
    </div>
    <div>
      <label for="date">Datum:</label>
      <input type="text" id="date" name="date" value=<?php echo date('d.m.Y'); ?>>
    </div>
    <div>
      <label for="content">Bericht:</label>
      <textarea id="content" name="content"></textarea>
    </div>
    <div>
      <button type="submit">Speichern</button>
    </div>
  </fieldset>
</form>
<div id="back">
  <a href="trainee.php">zurück zur Übersicht</a>
</div>
