<?php
namespace Jimdo\Reports;

require 'bootstrap.php';

require 'views/html_start.html';

require 'views/header_user_trainee.html';

require_once '../vendor/autoload.php';

$reportRepository = new ReportFileRepository('../reports');
$service = new ReportBookService($reportRepository);

$list = $service->findByTraineeId('jennyxyz');
?>

<table>
    <tr>
        <th>Teaser</th>
        <th>Erstellungsdatum</th>
        <th>KW</th>
        <th>Status</th>
        <th>Aktionen</th>
        </tr>
    <?php foreach ($list as $item): ?>
            <tr>
            <td><?php echo substr($item->content(), 0, 10); ?></td>
            <td>19.08.2016</td>
            <td>33</td>
            <td><?php echo $item->status(); ?></td>
            <td>
            <ul>
            <li><a href=""<?php echo $item->edit($item->content()); ?></li>
            <li><?php echo $item->delete($item); ?>löschen</li>
            <li><?php echo $item->requestApproval($item); ?>einreichen</li>
            </ul>
            </td>
        </tr>
    <?php endforeach ?>
</table>



<?php require 'views/html_end.html'; ?>
