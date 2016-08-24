<?php

namespace Jimdo\Reports;

require 'bootstrap.php';

isAuthorized('Trainer');

$reportRepository = new ReportFileRepository('../reports');
$service = new ReportBookService($reportRepository);

$reportId = get('report_Id');
$traineeId = get('trainee_Id');
$report = $service->findById($reportId, $traineeId);

?>

<h1>Bericht</h1>
<form>
    <fieldset>
        <legend>Bericht bearbeiten</legend>

        <div>
            <label for="calendarWeek">Kalenderwoche: </label>
            <input readonly type="text" id="calendarWeek" name="calendarWeek" value=<?php echo $report->calendarWeek(); ?> />
        </div>

        <div>
            <label for="date">Datum:</label>
            <input readonly type="text" id="date" name="date" value=<?php echo $report->date(); ?> />
        </div>

        <div>
            <label for="content">Bericht:</label>
            <textarea readonly id="content" name="content"><?php echo $report->content(); ?></textarea>
        </div>
    </fieldset>
</form>

<div>
    <form action="changeStatus.php" method="POST">
        <input type="hidden" name="report_Id" value="<?php echo $reportId; ?>">
        <input type="hidden" name="status" value="approve">
        <button type="submit">Genehmigen</button>
    </form>
</div>

<div>
    <form action="changeStatus.php" method="POST">
        <input type="hidden" name="report_Id" value="<?php echo $reportId; ?>">
        <input type="hidden" name="status" value="disapprove">
        <button type="submit">Ablehnen</button>
    </form>
</div>

<div id="back">
    <a href="trainer.php">zurück zur Übersicht</a>
</div>
