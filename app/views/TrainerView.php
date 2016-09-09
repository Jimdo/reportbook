<table class="table table-hover">
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
            <td><?php echo substr($report->content(), 0, 20); ?></td>
            <td><?php echo $report->date(); ?></td>
            <td><?php echo $report->calendarWeek(); ?></td>
            <td><?php echo $report->status(); ?></td>
            <td>
                    <a href="/report/viewReport?reportId=<?php echo $reportId; ?>&traineeId=<?php echo $traineeId; ?>"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a>
            </td>
        </tr>
    <?php endforeach ?>
</table>
