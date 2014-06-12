<?php
if (!isset($item)) {
    $item = $this->item;
}
?>

<div class="media-item">




    <div class="media-item-image"><a href="#" class="thumb-popover"  data-container="body" data-toggle="popover" data-content="...<img src='/content/upload/<?php echo $item->large ?>'>"><img src="/content/upload/<?php echo $item->thumb ?>"></a></div>

    <div class="media-item-caption">

        <div class="btn-group">
            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                Options <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">

                <?php if (!empty($draft_id)): ?>
                    <li><a href="#" onclick="return set_draft_cover(this, <?php echo $item->id; ?>)">Set as post cover</a></li>

                    <?php elseif (!empty($user_id)): ?>
                    <li><a href="#" onclick="return set_user_cover(this, <?php echo $item->id; ?>)">Set as user cover</a></li>
                    <li><a href="#" onclick="return set_user_image(this, <?php echo $item->id; ?>)">Set as user image</a></li>
                <?php else: ?>
                    <li><a href="#" onclick="return set_blog_cover(this, <?php echo $item->id; ?>)">Set as blog cover</a></li>
                    <li><a href="#" onclick="return set_blog_logo(this, <?php echo $item->id; ?>)">Set as blog logo</a></li>
                <?php endif; ?>


            </ul>
        </div>

    </div>

</div>
