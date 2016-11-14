<form action="/report/search" method="POST">
    <div class="input-group input-group-md col-md-3 col-md-offset-9">
        <input type="text" name="text" class="form-control">
        <span type="submit" class="input-group-addon"><span class=" glyphicon glyphicon-search"></span></span>
    </div></br>
</form>

<table class="table table-hover">
    <tr>
        <th>Name</th>
        <th>Teaser</th>
        <th>Erstellungsdatum</th>
        <th>KW</th>
        <th>Status</th>
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
            <td><?php echo $report->date(); ?></td>
            <td><?php echo $report->calendarWeek(); ?></td>
            <td><?php echo $this->viewHelper->getTranslationForStatus($report->status()); ?></td>
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
