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
                    <a href="/user/approve?email=<?php echo $userEmail; ?>"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></a>
                    <a href="/user/disapprove?email=<?php echo $userEmail; ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
            </td>
        </tr>
    <?php endforeach ?>
</table>
