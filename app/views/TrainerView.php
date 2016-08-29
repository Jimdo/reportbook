<table>
    <tr>
        <th>Azubi</th>
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
            <td><?php echo $report->traineeId(); ?></td>
            <td><?php echo substr($report->content(), 0, 10); ?></td>
            <td><?php echo $report->date(); ?></td>
            <td><?php echo $report->calendarWeek(); ?></td>
            <td><?php echo $report->status(); ?></td>
            <td>
                <ul>
                    <li><a href="viewReport.php?reportId=<?php echo $reportId; ?>&traineeId=<?php echo $traineeId; ?>">Öffnen</a></li>
                </ul>
            </td>
        </tr>
    <?php endforeach ?>
</table>
