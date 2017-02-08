<div class="row">
    <ul class="nav nav-tabs">
        <li role="presentation"><a href="/report/list">Listenansicht</a></li>
        <li role="presentation" class="active"><a href="/report/calendar">Kalenderansicht</a></li>
    </ul>
</div>

</br>

<?php if ($this->trainerRole || $this->adminRole): ?>
    <div class"col-sm-10" style="float:right;">
        <div class="dropdown">
          <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">

            <?php
            foreach ($this->users as $user) {
                if ($user['id'] === $this->currentUserId) {
                    echo $user['name'];
                }
            }
            ?>

            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
             <?php foreach ($this->users as $user): ?>
                 <li><a href="/report/calendar?userId=<?php echo $user['id']; ?>"><?php echo $user['name']; ?></a></li>
             <?php endforeach; ?>
          </ul>
        </div>
    </div>
<?php endif; ?>

<div style="width:800px; margin:0 auto;">
    <div class="col-sm-offset-5">

        <ul class="nav nav-pills">
          <li><a href="/report/yearBefore?userId=<?php echo $this->currentUserId; ?>&year=<?php echo $this->year; ?>" class="glyphicon glyphicon-chevron-left" style="color:black;" aria-hidden="true"></a></li>
          <li><button class="btn-md" style="border: none; background: transparent; padding-top: 5px;"><font size="4"><?php echo $this->year; ?></font></button></li>
          <li><a href="/report/yearLater?userId=<?php echo $this->currentUserId; ?>&year=<?php echo $this->year; ?>" class="glyphicon glyphicon-chevron-right" style="color:black;" aria-hidden="true"></a></li>
        </ul>

    </div>
</div>

</br>

<div class="row">
    <?php for ($i=1; $i < 13; $i++): ?>
        <div class="col-md-3" style="padding: 5px;">
            <div style="border: 1px solid #BDBDBD; border-radius: 5px; padding-left: 5px; padding-right: 5px;  height: 280px;">
                <p class="text-center" style="padding-top: 10px;"><b><?php echo $this->months[$i - 1]; ?></b></p>
                <?php $this->viewHelper->showMonth($i, $this->year, $this->cwInfo); ?>
            </div>
        </div>
    <?php endfor; ?>
</div>

</br>

<div class="row" style="display: inline; float:right;">
    <p>
        <span style="color: #E6E6E6">&#9634;</span> Offen &nbsp;&nbsp;&nbsp;&nbsp;
        <span style="color: yellow">&#9608;</span> Eingereicht &nbsp;&nbsp;&nbsp;&nbsp;
        <span style="color: #01DF01">&#9608;</span> Genehmigt &nbsp;&nbsp;&nbsp;&nbsp;
        <span style="color: red">&#9608;</span> Abgelehnt
    </p>
</div>
