<form action="/report/search" method="POST">
    <div class="input-group input-group-md col-md-3 col-md-offset-9">
        <input type="text" name="text" class="form-control" placeholder="Kalenderwoche oder Inhalt">
        <span type="submit" class="input-group-addon"><span class=" glyphicon glyphicon-search"></span></span>
    </div></br>
</form>

<div style="border:1px solid #BDBDBD; border-radius: 5px;">
    <table class="table table-hover">
        <tr>
            <th>Name <a href="/report/list?sort=name" style="color: gray;" type="submit" class="btn-link glyphicon glyphicon-chevron-down" aria-hidden="true"></a></th>
            <th>Vorschau</th>
            <th>Kategorie <a href="/report/list?sort=category" style="color: gray;" type="submit" class="btn-link glyphicon glyphicon-chevron-down" aria-hidden="true"></a></th>
            <th>Erstellungsdatum <a href="" style="color: gray;" type="submit" class="btn-link glyphicon glyphicon-chevron-down" aria-hidden="true"></a></th>
            <th>KW <a href="/report/list?sort=calendarWeek" style="color: gray;" type="submit" class="btn-link glyphicon glyphicon-chevron-down" aria-hidden="true"></a></th>
            <th>Status <a href="" style="color: gray;" type="submit" class="btn-link glyphicon glyphicon-chevron-down" aria-hidden="true"></a></th>
            <th>Kommentare <a href="/report/list?sort=comment" style="color: gray;" type="submit" class="btn-link glyphicon glyphicon-chevron-down" aria-hidden="true"></a></th>
            <th>Aktionen</th>
        </tr>
        <?php foreach ($this->reports as $report):
                  $reportId = $report->id();
                  $traineeId = $report->traineeId();
                  $profile = $this->profileService->findProfileByUserId($traineeId);
                  $user = $this->userService->findUserById($traineeId);?>
            <tr>
                <td><a href="/user/viewProfile?userId=<?php echo $user->id(); ?>"><?php echo $profile->forename() . ' ' . $profile->surname(); ?></a></td>
                <td><?php echo substr($report->content(), 0, 20); ?></td>
                <td><?php echo $this->viewHelper->getTranslationForCategory($report->category()); ?></td>
                <td><?php echo $report->date(); ?></td>
                <td><?php echo $report->calendarWeek(); ?></td>
                <td><?php echo $this->viewHelper->getTranslationForStatus($report->status()); ?></td>
                <td><?php echo count($this->commentService->findCommentsByReportId($reportId)); ?></td>
                <td>

                    <form action="/report/viewReport" method="POST">
                      <input type="hidden" id="reportId" name="reportId" value="<?php echo $reportId; ?>"/>
                      <input type="hidden" id="traineeId" name="traineeId" value="<?php echo $traineeId; ?>"/>
                      <button type="submit" class="btn-link glyphicon glyphicon-eye-open"></button>
                    </form>

                </td>
            </tr>
        <?php endforeach ?>
    </table>
</div>

<div>
    <?php if ($this->reports === []): ?>
        <label>Keine Berichte gefunden</label>
    <?php endif; ?>
</div></br>
