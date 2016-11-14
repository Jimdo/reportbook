<?php use \Jimdo\Reports\Reportbook\Report as Report; ?>
<?php use \Jimdo\Reports\Web\Controller\ReportController as ReportController; ?>

<form action="/report/search" method="POST">
    <div class="input-group input-group-md col-md-3 col-md-offset-9">
        <input type="text" name="text" class="form-control" placeholder="Kalenderwoche oder Inhalt">
        <span type="submit" class="input-group-addon"><span class=" glyphicon glyphicon-search"></span></span>
    </div></br>
</form>

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
         $traineeId = $report->traineeId();?>
        <tr>
            <td><?php echo substr($report->content(), 0, 20); ?></td>
            <td><?php echo $report->date(); ?></td>
            <td><?php echo $report->calendarWeek(); ?></td>
            <td><?php echo $this->viewHelper->getTranslationForStatus($report->status()); ?></td>
                <td>

                    <form action="/report/editReport" method="POST">

                        <input type="hidden" id="reportId" name="reportId" value="<?php echo $reportId; ?>"/>
                        <input type="hidden" id="traineeId" name="traineeId" value="<?php echo $traineeId; ?>"/>

                        <?php if ($report->status() !== Report::STATUS_APPROVED
                               && $report->status() !== Report::STATUS_APPROVAL_REQUESTED): ?>
                            <button type="submit" class="btn-link glyphicon glyphicon-pencil"></button>
                        <?php endif; ?>

                    </form>

                    <form action="/report/requestApproval" method="POST">

                        <input type="hidden" id="reportId" name="reportId" value="<?php echo $reportId; ?>"/>

                        <?php if ($report->status() !== Report::STATUS_DISAPPROVED
                               && $report->status() !== Report::STATUS_APPROVED
                               && $report->status() !== Report::STATUS_APPROVAL_REQUESTED): ?>
                            <button type="submit" class="btn-link glyphicon glyphicon-send" onclick="return confirm('Soll der Bericht eingereicht werden?')" aria-hidden="true"></button>

                    </form>

                    <form action="/report/deleteReport" method="POST">

                        <input type="hidden" id="reportId" name="reportId" value="<?php echo $reportId; ?>"/>
                        <input type="hidden" id="traineeId" name="traineeId" value="<?php echo $traineeId; ?>"/>

                        <?php if ($report->status() !== Report::STATUS_REVISED): ?>
                            <button type="submit" class="btn-link glyphicon glyphicon-trash" onclick="return confirm('Soll der Bericht wirklich gelöscht werden?')" aria-hidden="true"></button>
                        <?php endif; ?>
                        <?php endif; ?>

                    </form>

                    <form action="/report/viewReport" method="POST">

                        <input type="hidden" id="reportId" name="reportId" value="<?php echo $reportId; ?>"/>
                        <input type="hidden" id="traineeId" name="traineeId" value="<?php echo $traineeId; ?>"/>

                        <?php if ($report->status() === Report::STATUS_APPROVED
                               || $report->status() === Report::STATUS_APPROVAL_REQUESTED): ?>
                            <button type="submit" class="btn-link glyphicon glyphicon-eye-open"></button>
                        <?php endif; ?>

                    </form>

                </td>
        </tr>
    <?php endforeach; ?>
</table>
<div>
    <form action="/report/createReport" method="POST">
        <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Neuen Bericht erstellen</button>
    </form>
</div>
