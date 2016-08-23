<?php
namespace Jimdo\Reports;

require 'bootstrap.php';

isAuthorized('Trainee');

require 'views/header_user_trainee.html';

$reportRepository = new ReportFileRepository('../reports');
$service = new ReportBookService($reportRepository);

$reports = $service->findByTraineeId(session('userId'));
?>

<table>
    <tr>
        <th>Teaser</th>
        <th>Erstellungsdatum</th>
        <th>KW</th>
        <th>Status</th>
        <th>Aktionen</th>
        </tr>
    <?php foreach ($reports as $report): ?>
        <?php $reportId = $report->id(); ?>
            <tr>
            <td><?php echo substr($report->content(), 0, 10); ?></td>
            <td><?php echo $report->date(); ?></td>
            <td><?php echo $report->calendarWeek(); ?></td>
            <td><?php echo $report->status(); ?></td>
            <td>
            <ul>
                <li><a href="editReport.php?report_Id=<?php echo $reportId; ?>">Bearbeiten</a></li>
                <li><a href="deleteReport.php?report_Id=<?php echo $reportId; ?>" onclick="return confirm('Soll der Bericht wirklich gelöscht werden?')">Löschen</a></li>
                <li><a href="requestApprovalReport.php?report_Id=<?php echo $reportId; ?>" onclick="return confirm('Soll der Bericht eingereicht werden?')">Einreichen</a></li>
            </ul>
            </td>
        </tr>
    <?php endforeach ?>
</table>



<?php require 'views/html_end.html'; ?>
