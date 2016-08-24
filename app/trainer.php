<?php
namespace Jimdo\Reports;

require 'bootstrap.php';

isAuthorized('Trainer');

require 'views/header_user_trainer.html';

$reportRepository = new ReportFileRepository('../reports');
$service = new ReportBookService($reportRepository);


$reports = array_merge(
    $service->findByStatus(Report::STATUS_APPROVAL_REQUESTED),
    $service->findByStatus(Report::STATUS_APPROVED),
    $service->findByStatus(Report::STATUS_DISAPPROVED)
);
?>

<table>
    <tr>
        <th>Azubi</th>
        <th>Teaser</th>
        <th>Erstellungsdatum</th>
        <th>KW</th>
        <th>Status</th>
        <th>Aktionen</th>
    </tr>
    <?php foreach ($reports as $report): ?>
        <?php $reportId = $report->id(); ?>
        <?php $traineeId = $report->traineeId(); ?>
        <tr>

            <td><?php echo $report->traineeId(); ?></td>
            <td><?php echo substr($report->content(), 0, 10); ?></td>
            <td><?php echo $report->date(); ?></td>
            <td><?php echo $report->calendarWeek(); ?></td>
            <td><?php echo $report->status(); ?></td>
            <td>
                <ul>
                    <li><a href="viewReport.php?report_Id=<?php echo $reportId; ?>&trainee_Id=<?php echo $traineeId; ?>">Öffnen</a></li>
                </ul>
            </td>
        </tr>
    <?php endforeach ?>
</table>

<?php require 'views/html_end.html'; ?>
