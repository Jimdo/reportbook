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
        $userEmail = $user->email()?>
        <tr>
            <td><?php echo $user->forename(); ?></td>
            <td><?php echo $user->surname(); ?></td>
            <td><?php echo $userEmail; ?></td>
            <td><?php echo $user->roleName(); ?></td>
            <td><?php echo $user->roleStatus(); ?></td>
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