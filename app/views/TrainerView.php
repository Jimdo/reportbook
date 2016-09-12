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
                <form action="/report/actions" method="POST">
                  <input type="hidden" id="reportId" name="reportId" value="<?php echo $reportId; ?>"/>
                  <input type="hidden" id="traineeId" name="traineeId" value="<?php echo $traineeId; ?>"/>
                  <button type="submit" id="view" name="action" class="btn-link glyphicon glyphicon-eye-open" value="view"></button>
                </form>
            </td>
        </tr>
    <?php endforeach ?>
</table>
