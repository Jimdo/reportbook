<div class="row">
    <ul class="nav nav-tabs">
        <li role="presentation"><a href="/report/list">Listenansicht</a></li>
        <li role="presentation" class="active"><a href="/report/calendar">Kalenderansicht</a></li>
    </ul>
</div>

</br>

<div class"col-sm-10" style="float:right;">
    <div class="dropdown">
      <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <?php echo $this->users[0]['name']; ?>
        <span class="caret"></span>
      </button>
      <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
         <?php foreach ($this->users as $user): ?>
             <li><a href="/report/calendar?userId=<?php echo $user['id']?>"><?php echo $user['name']; ?></a></li>
         <?php endforeach; ?>
      </ul>
    </div>
</div>

<div style="width:800px; margin:0 auto;">
    <div class="col-sm-offset-5">

        <ul class="nav nav-pills">
          <li><button class="btn btn-default btn-sm glyphicon glyphicon-chevron-left"></button></li>
          <li><button class="btn-md" style="border: none; background: transparent; padding-top: 3px;"><font size="4">2017</font></button></li>
          <li><button class="btn btn-default btn-sm glyphicon glyphicon-chevron-right"></button></li>
        </ul>

    </div>
</div>

</br>

<div class="row">
    <?php for ($i=1; $i < 13; $i++): ?>
        <div class="col-md-3" style="padding: 5px;">
            <div style="border: 1px solid #BDBDBD; border-radius: 5px; padding-left: 5px; padding-right: 5px;  height: 300px;">
                <p class="text-center" style="padding-top: 10px;"><b><?php echo $this->months[$i - 1]; ?></b></p>
                <?php $this->viewHelper->showMonth($i, 2017, $this->cwInfo); ?>
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
