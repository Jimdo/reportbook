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
         $reportId = $report->id(); ?>
        <tr>
            <td><?php echo substr($report->content(), 0, 10); ?></td>
            <td><?php echo $report->date(); ?></td>
            <td><?php echo $report->calendarWeek(); ?></td>
            <td><?php echo $report->status(); ?></td>
            <?php if ($report->status() === Report::STATUS_NEW
                || $report->status() === Report::STATUS_DISAPPROVED
                || $report->status() === Report::STATUS_EDITED): ?>
                <td>
                    <ul>
                        <li><a href="editReport.php?reportId=<?php echo $reportId; ?>">Bearbeiten</a></li>
                        <li><a href="ReportActionProcessor.php?reportId=<?php echo $reportId; ?>&action=delete" onclick="return confirm('Soll der Bericht wirklich gelöscht werden?')">Löschen</a></li>
                        <?php if ($report->status() !== Report::STATUS_DISAPPROVED): ?>
                            <li><a href="ReportActionProcessor.php?reportId=<?php echo $reportId; ?>&action=requestApproval" onclick="return confirm('Soll der Bericht eingereicht werden?')">Einreichen</a></li>
                        <?php endif; ?>
                    </ul>
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
</table>
<div>
    <form action="createReport.php">
        <button type="submit">Bericht erstellen</button>
    </form>
</div>
