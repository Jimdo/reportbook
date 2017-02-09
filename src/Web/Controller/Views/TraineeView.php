<?php use \Jimdo\Reports\Reportbook\Report as Report; ?>
<?php use \Jimdo\Reports\Web\Controller\ReportController as ReportController; ?>
<div class="row">
    <ul class="nav nav-tabs">
        <li role="presentation" class="active"><a href="/report/list">Listenansicht</a></li>
        <?php if ($this->reports !== []): ?>
            <li role="presentation"><a href="/report/calendar?userId=<?php echo $this->reports[0]->traineeId(); ?>">Kalenderansicht</a></li>
        <?php endif; ?>
    </ul>
</div>

</br>

<form action="/report/search" method="POST">
    <div class="input-group input-group-md col-md-3 col-md-offset-9">
        <input type="text" name="text" class="form-control" placeholder="Kalenderwoche oder Inhalt">
        <span type="submit" class="input-group-addon"><span class=" glyphicon glyphicon-search"></span></span>
    </div></br>
</form>

<div style="border:1px solid #BDBDBD; border-radius: 5px;">
    <table class="table table-hover">
        <tr>
            <th class="text-center">Vorschau</th>
            <th class="text-center">Kategorie <a href="/report/list?sort=category" style="color: gray;" type="submit" class="btn-link glyphicon glyphicon-chevron-down" aria-hidden="true"></a></th>
            <th class="text-center">Erstellungsdatum</th>
            <th class="text-center">KW <a href="/report/list?sort=calendarWeek" style="color: gray;" type="submit" class="btn-link glyphicon glyphicon-chevron-down" aria-hidden="true"></a></th>
            <th class="text-center">Status <a href="/report/list?sort=status" style="color: gray;" type="submit" class="btn-link glyphicon glyphicon-chevron-down" aria-hidden="true"></a></th>
            <th class="text-center">Kommentare <a href="/report/list?sort=comment" style="color: gray;" type="submit" class="btn-link glyphicon glyphicon-chevron-down" aria-hidden="true"></a></th>
            <th class="text-center">Aktionen</th>
        </tr>
        <?php foreach ($this->reports as $report):
             $reportId = $report->id();
             $traineeId = $report->traineeId();?>
            <tr>
                <td class="text-center"><?php echo substr($report->content(), 0, 20); ?></td>
                <td class="text-center"><?php echo $this->viewHelper->getTranslationForCategory($report->category()); ?></td>
                <td class="text-center"><?php echo $report->date(); ?></td>
                <td class="text-center"><?php echo $report->calendarWeek(); ?></td>
                <td class="text-center"><?php echo $this->viewHelper->getTranslationForStatus($report->status()); ?></td>
                <td class="text-center"><?php echo count($this->commentService->findCommentsByReportId($reportId)); ?></td>
                    <td>
                        <form action="/report/editReport" method="POST">
                            <?php
                            if ($report->status() !== Report::STATUS_APPROVED
                            && $report->status() !== Report::STATUS_APPROVAL_REQUESTED):
                            ?>
                                <div class="col-md-1">

                                    <input type="hidden" id="reportId" name="reportId" value="<?php echo $reportId; ?>"/>
                                    <input type="hidden" id="traineeId" name="traineeId" value="<?php echo $traineeId; ?>"/>
                                    <button type="submit" class="btn-link glyphicon glyphicon-pencil"></button>

                                </div>
                        <?php endif; ?>
                        </form>

                        <form action="/report/requestApproval" method="POST">
                            <?php
                            if ($report->status() !== Report::STATUS_DISAPPROVED
                            && $report->status() !== Report::STATUS_APPROVED
                            && $report->status() !== Report::STATUS_APPROVAL_REQUESTED):
                            ?>
                                <div class="col-md-1">

                                    <input type="hidden" id="reportId" name="reportId" value="<?php echo $reportId; ?>"/>
                                    <button type="submit" class="btn-link glyphicon glyphicon-send" onclick="return confirm('Soll der Bericht eingereicht werden?')" aria-hidden="true"></button>

                                </div>
                        </form>

                        <form action="/report/deleteReport" method="POST">
                            <?php if ($report->status() !== Report::STATUS_REVISED): ?>
                                <div class="col-md-1">

                                    <input type="hidden" id="reportId" name="reportId" value="<?php echo $reportId; ?>"/>
                                    <input type="hidden" id="traineeId" name="traineeId" value="<?php echo $traineeId; ?>"/>
                                    <button type="submit" class="btn-link glyphicon glyphicon-trash" onclick="return confirm('Soll der Bericht wirklich gelöscht werden?')" aria-hidden="true"></button>

                                </div>
                            <?php endif; ?>
                            <?php endif; ?>
                        </form>

                        <form action="/report/viewReport" method="POST">
                            <?php
                            if ($report->status() === Report::STATUS_APPROVED
                                || $report->status() === Report::STATUS_APPROVAL_REQUESTED):
                            ?>
                                <div class="col-md-1">

                                    <input type="hidden" id="reportId" name="reportId" value="<?php echo $reportId; ?>"/>
                                    <input type="hidden" id="traineeId" name="traineeId" value="<?php echo $traineeId; ?>"/>
                                    <button type="submit" class="btn-link glyphicon glyphicon-eye-open"></button>

                                </div>
                        <?php endif; ?>
                        </form>
                    </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div>
    <?php if ($this->reports === []): ?>
        <label>Keine Berichte gefunden</label>
    <?php endif; ?>
</div></br>

<div>
    <form action="/report/createReport" method="POST">
        <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Neuen Bericht erstellen</button>
    </form>
</div>
</br>
