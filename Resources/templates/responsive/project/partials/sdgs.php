<?php
$sdgs = $this->project->getSdgs();
if (!empty($sdgs)):
    $sdgs = array_slice($sdgs, 0, 3)
    ?>
    <div class="col-md-4 col-xs-4">
        <h4>Objetivos de desarrollo</h4>
        <article class="card-sdgs">
            <h3>El proyecto ayuda al cumplimiento de los siguientes ODS</h3>
            <?php foreach ($sdgs as $sdg): ?>
                <div class="col-md-4">
                    <img src="<?= $this->asset("img/sdg/sdg{$sdg->id}.svg") ?>" alt="<?= $sdg->name ?>" width="90px">
                </div>
            <?php endforeach; ?>
        </article>
    </div>

<?php endif; ?>