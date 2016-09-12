<table class="table table-hover">
    <tr>
        <th>Vorname</th>
        <th>Nachname</th>
        <th>Rolle</th>
        <th>Status</th>
        <th>Aktionen</th>
    </tr>
    <?php foreach ($this->users as $user):?>
        <tr>
            <td><?php echo $user->forename(); ?></td>
            <td><?php echo $user->surname(); ?></td>
            <td><?php echo $user->roleName(); ?></td>
            <td><?php echo $user->roleStatus(); ?></td>
            <td>
                    <a href="/report/viewReport?reportId=<?php echo $reportId; ?>&traineeId=<?php echo $traineeId; ?>"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a>
            </td>
        </tr>
    <?php endforeach ?>
</table>
