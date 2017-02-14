<?php
use \Jimdo\Reports\Reportbook\Report;
use \Jimdo\Reports\Web\Controller\ReportController;
?>

<div class="row">
    <ul class="nav nav-tabs">
        <li role="presentation" class="active"><a href="/report/list">Listenansicht</a></li>
        <li role="presentation"><a href="/report/calendar?userId=<?php echo $this->reports[0]->traineeId(); ?>">Kalenderansicht</a></li>
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
            <th class="text-center">Name <a href="/report/list?sort=name" style="color: gray;" type="submit" class="btn-link glyphicon glyphicon-chevron-down" aria-hidden="true"></a></th>
            <th class="text-center">Vorschau</th>
            <th class="text-center">Kategorie <a href="/report/list?sort=category" style="color: gray;" type="submit" class="btn-link glyphicon glyphicon-chevron-down" aria-hidden="true"></a></th>
            <th class="text-center">Erstellungsdatum</th>
            <th class="text-center">KW <a href="/report/list?sort=calendarWeek" style="color: gray;" type="submit" class="btn-link glyphicon glyphicon-chevron-down" aria-hidden="true"></a></th>
            <th class="text-center">Status <a href="/report/list?sort=status" style="color: gray;" type="submit" class="btn-link glyphicon glyphicon-chevron-down" aria-hidden="true"></a></th>
            <th class="text-center">Kommentare <a href="/report/list?sort=comment" style="color: gray;" type="submit" class="btn-link glyphicon glyphicon-chevron-down" aria-hidden="true"></a></th>
            <th class="text-center">Aktionen</th>
        </tr>
        <?php foreach ($this->reports as $report) :
             $reportId = $report->id();
             $traineeId = $report->traineeId();
             $profile = $this->profileService->findProfileByUserId($traineeId);
             $user = $this->userService->findUserById($traineeId);?>
            <tr>
                <td class="text-center"><a href="/user/profile?userId=<?php echo $user->id(); ?>"><?php echo $profile->forename() . ' ' . $profile->surname(); ?></a></td>
                <td class="text-center"><?php echo substr($report->content(), 0, 20); ?></td>
                <td class="text-center"><?php echo $this->viewHelper->getTranslationForCategory($report->category()); ?></td>
                <td class="text-center"><?php echo $report->date(); ?></td>
                <td class="text-center"><?php echo $report->calendarWeek(); ?></td>
                <td class="text-center"><?php echo $this->viewHelper->getTranslationForStatus($report->status()); ?></td>
                <td class="text-center"><?php echo count($this->commentService->findCommentsByReportId($reportId)); ?></td>
                    <td>

                        <form action="/report/editReport" method="POST">
                            <div class="col-md-1 col-md-offset-1">
                                <input type="hidden" id="reportId" name="reportId" value="<?php echo $reportId; ?>"/>
                                <input type="hidden" id="traineeId" name="traineeId" value="<?php echo $traineeId; ?>"/>
                                <button type="submit" class="btn-link glyphicon glyphicon-pencil"></button>
                            </div>
                        </form>

                        <form action="/report/requestApproval" method="POST">
                            <div class="col-md-1">
                                <input type="hidden" id="reportId" name="reportId" value="<?php echo $reportId; ?>"/>
                                <button type="submit" class="btn-link glyphicon glyphicon-send" onclick="return confirm('Soll der Bericht eingereicht werden?')" aria-hidden="true"></button>
                            </div>
                        </form>

                    <div class="col-md-1">
                        <form action="/report/deleteReport" method="POST">
                                <input type="hidden" id="reportId" name="reportId" value="<?php echo $reportId; ?>"/>
                                <input type="hidden" id="traineeId" name="traineeId" value="<?php echo $traineeId; ?>"/>
                                <button type="submit" class="btn-link glyphicon glyphicon-trash" onclick="return confirm('Soll der Bericht wirklich gelöscht werden?')" aria-hidden="true"></button>
                        </form>
                    </div>

                    <div class="col-md-1">
                        <form action="/report/viewReport" method="POST">
                                <input type="hidden" id="reportId" name="reportId" value="<?php echo $reportId; ?>"/>
                                <input type="hidden" id="traineeId" name="traineeId" value="<?php echo $traineeId; ?>"/>
                                <button type="submit" class="btn-link glyphicon glyphicon-eye-open"></button>
                        </form>
                    </div>

                    </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div>
    <?php if ($this->reports === []) : ?>
        <label>Keine Berichte gefunden</label>
    <?php endif; ?>
</div></br>
