<?php use \Jimdo\Reports\Report as Report; ?>
<table>
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
            <td><?php echo substr($report->content(), 0, 10); ?></td>
            <td><?php echo $report->date(); ?></td>
            <td><?php echo $report->calendarWeek(); ?></td>
            <td><?php echo $report->status(); ?></td>
                <td>
                    <ul>
                        <?php if ($report->status() !== Report::STATUS_APPROVED
                                && $report->status() !== Report::STATUS_APPROVAL_REQUESTED): ?>
                            <li><a href="/report/editReport?reportId=<?php echo $reportId; ?>">Bearbeiten</a></li>
                        <?php endif; ?>
                        <?php if ($report->status() !== Report::STATUS_DISAPPROVED
                                && $report->status() !== Report::STATUS_APPROVED
                                && $report->status() !== Report::STATUS_APPROVAL_REQUESTED): ?>
                            <li><a href="/report/requestApproval?reportId=<?php echo $reportId; ?>&action=requestApproval" onclick="return confirm('Soll der Bericht eingereicht werden?')">Einreichen</a></li>
                            <?php if ($report->status() !== Report::STATUS_REVISED): ?>
                                <li><a href="/report/delete?reportId=<?php echo $reportId; ?>&action=delete" onclick="return confirm('Soll der Bericht wirklich gelöscht werden?')">Löschen</a></li>
                            <?php endif ?>
                        <?php endif; ?>
                        <?php if ($report->status() === Report::STATUS_APPROVED
                                || $report->status() === Report::STATUS_APPROVAL_REQUESTED): ?>
                            <li><a href="/report/viewReport?reportId=<?php echo $reportId; ?>&traineeId=<?php echo $traineeId; ?>">Öffnen</a></li>
                        <?php endif; ?>
                    </ul>
                </td>
        </tr>
    <?php endforeach; ?>
</table>
<div>
    <form action="/report/createReport" method="POST">
        <button type="submit">Bericht erstellen</button>
    </form>
</div>
