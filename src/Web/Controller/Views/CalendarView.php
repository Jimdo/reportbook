<div class="row">
    <!-- <?php var_dump($this->yearArr); ?> -->
<?php for ($i=0; $i < 12; $i++): ?>
    <div class="col-md-3" style="padding: 5px;">
    <div style="border: 1px solid #BDBDBD; border-radius: 5px; padding-left: 5px; padding-right: 5px;">
        <p class="text-center"><b><?php echo $this->months[$i]; ?></b></p>
        <table class="table-condensed table-bordered table-striped table table-curved">
            <thead>
                <tr>
                    <th>Mo</th>
                    <th>Di</th>
                    <th>Mi</th>
                    <th>Do</th>
                    <th>Fr</th>
                    <th>Sa</th>
                    <th>So</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->yearArr['2017'][$this->months[$i]] as $day => $name): ?>
                    <tr>
                            <!-- <?php echo($day . '=' . $name); ?> -->
                            <?php if ($name === 'Mon'): ?>
                                <td><?php echo $day; ?></td>
                            <?php else: ?>
                                <td></td>
                            <?php endif; ?>
                            <?php if ($name === 'Tue'): ?>
                                <td><?php echo $day; ?></td>
                            <?php else: ?>
                                <td></td>
                            <?php endif; ?>
                            <?php if ($name === 'Wed'): ?>
                                <td><?php echo $day; ?></td>
                            <?php else: ?>
                                <td></td>
                            <?php endif; ?>
                            <?php if ($name === 'Thu'): ?>
                                <td><?php echo $day; ?></td>
                            <?php else: ?>
                                <td></td>
                            <?php endif; ?>
                            <?php if ($name === 'Fri'): ?>
                                <td><?php echo $day; ?></td>
                            <?php else: ?>
                                <td></td>
                            <?php endif; ?>
                            <?php if ($name === 'Sat'): ?>
                                <td><?php echo $day; ?></td>
                            <?php else: ?>
                                <td></td>
                            <?php endif; ?>
                            <?php if ($name === 'Sun'): ?>
                                <td><?php echo $day; ?></td>
                            <?php else: ?>
                                <td></td>
                            <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </div>
<?php endfor; ?>
</div>
