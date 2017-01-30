
<label> Benutzeranfragen </label>
<table class="table table-hover">
    <tr>
        <th>Vorname</th>
        <th>Nachname</th>
        <th>E-Mail</th>
        <th>Rolle</th>
        <th>Status</th>
        <th>Aktionen</th>
    </tr>
    <?php foreach ($this->users as $user):
        $profile = $this->profileService->findProfileByUserId($user->id());
        $userEmail = $user->email(); ?>
        <tr>
            <td><?php echo $profile->forename(); ?></td>
            <td><?php echo $profile->surname(); ?></td>
            <td><?php echo $userEmail; ?></td>
            <td><?php echo $this->viewHelper->getTranslationForRole($user->roleName()); ?></td>
            <td><?php echo $this->viewHelper->getTranslationForStatus($user->roleStatus()); ?></td>
            <td>
                <form action="/user/changeStatus" method="POST">
                  <input type="hidden" id="email" name="email" value="<?php echo $userEmail; ?>"/>
                  <button type="submit" id="approve" name="action" class="btn-link glyphicon glyphicon-ok" value="approve"></button>
                  <button type="submit" id="disapprove" name="action" class="btn-link glyphicon glyphicon-remove" value="disapprove"></button>
                </form>
            </td>
        </tr>
    <?php endforeach ?>
</table>

<label> Registrierte Benutzer </label>
<table class="table table-hover">
    <tr>
        <th>Vorname</th>
        <th>Nachname</th>
        <th>Benutzername</th>
        <th>E-Mail</th>
        <th>Rolle</th>
        <th>Aktionen</th>
    </tr>
    <?php foreach ($this->approvedUsers as $user):
        $profile = $this->profileService->findProfileByUserId($user->id());
        $userEmail = $user->email(); ?>
        <tr>
            <td><?php echo $profile->forename(); ?></td>
            <td><?php echo $profile->surname(); ?></td>
            <td><?php echo $user->username(); ?></td>
            <td><?php echo $userEmail; ?></td>
            <td><?php echo $this->viewHelper->getTranslationForRole($user->roleName()); ?></td>
            <td>
                <form action="/user/deleteUser" method="POST">
                  <input type="hidden" id="email" name="email" value="<?php echo $userEmail; ?>"/>
                  <button type="submit" id="deleteUser" name="action" class="btn-link glyphicon glyphicon-trash" value="deleteUser"></button>
                </form>
            </td>
        </tr>
    <?php endforeach ?>
</table>
