<?php
use Goteo\Core\View,
    Goteo\Library\Text;
?>
<div class="widget user-mates">
    <h3 class="supertitle"><?php echo Text::get('profile-sharing_interests-header'); ?></h3>
    <div class="users">
        <ul>
        <?php $c=1; // limitado a 6 sharemates en el lateral
        foreach ($this['shares'] as $mate): ?>
            <li>
                <div class="user">
                    <div class="avatar"><a href="/user/<?php echo htmlspecialchars($mate->user) ?>"><img src="/image/<?php echo $mate->avatar->id ?>/43/43" /></a></div>
                    <h4><a href="/user/<?php echo htmlspecialchars($mate->user) ?>"><?php echo htmlspecialchars($mate->user) ?></a></h4>
                    <span class="projects"><?php echo Text::get('regular-projects'); ?> (<?php echo $mate->projects ?>)</span>
                    <span class="invests"><?php echo Text::get('regular-investing'); ?> (<?php echo $mate->invests ?>)</span>
                </div>
            </li>
        <?php if ($c>5) break; else $c++;
        endforeach ?>
        </ul>
    </div>
    <a class="more" href="/user/profile/<?php echo $this['user']->id ?>/sharemates"><?php echo Text::get('regular-see_more'); ?></a>
</div>
