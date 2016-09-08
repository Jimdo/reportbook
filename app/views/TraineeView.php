<?php use \Jimdo\Reports\Report as Report; ?>
<table class="table table-hover">
    <tr>
        <th>Teaser</th>
        <th>Erstellungsdatum</th>
        <th>KW</th>
        <th>Status</th>
        <th>Aktionen</th>
    </tr>
    <?php foreach ($this->reports as $report):
         $reportId = $report->id();
         $traineeId = $report->traineeId(); ?>
        <tr>
            <td><?php echo substr($report->content(), 0, 20); ?></td>
            <td><?php echo $report->date(); ?></td>
            <td><?php echo $report->calendarWeek(); ?></td>
            <td><?php echo $report->status(); ?></td>
                <td>
                    <?php if ($report->status() !== Report::STATUS_APPROVED
                                && $report->status() !== Report::STATUS_APPROVAL_REQUESTED): ?>
                            <a href="/report/editReport?reportId=<?php echo $reportId; ?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                        <?php endif; ?>
                        <?php if ($report->status() !== Report::STATUS_DISAPPROVED
                                && $report->status() !== Report::STATUS_APPROVED
                                && $report->status() !== Report::STATUS_APPROVAL_REQUESTED): ?>
                            <a href="/report/requestApproval?reportId=<?php echo $reportId; ?>&action=requestApproval" onclick="return confirm('Soll der Bericht eingereicht werden?')"><span class="glyphicon glyphicon-send" aria-hidden="true"></span></a>
                            <?php if ($report->status() !== Report::STATUS_REVISED): ?>
                                <a href="/report/delete?reportId=<?php echo $reportId; ?>&action=delete" onclick="return confirm('Soll der Bericht wirklich gelöscht werden?')"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                            <?php endif ?>
                        <?php endif; ?>
                        <?php if ($report->status() === Report::STATUS_APPROVED
                                || $report->status() === Report::STATUS_APPROVAL_REQUESTED): ?>
                            <a href="/report/viewReport?reportId=<?php echo $reportId; ?>&traineeId=<?php echo $traineeId; ?>"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a>
                        <?php endif; ?>
                </td>
        </tr>
    <?php endforeach; ?>
</table>
<div>
    <form action="/report/createReport" method="POST">
        <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Bericht erstellen</button>
    </form>
</div>
