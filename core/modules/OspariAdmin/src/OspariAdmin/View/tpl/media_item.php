<?php if(!isset($item)){
    $item = $this->item;
} ?>
<div class="col-md-3">
            <div class="thumbnail">
                <img src="/content/upload/<?php echo $item->thumb ?>">
                <?php if (!empty($draft_id)): ?>
                    <div class="caption">
                        <a href="#" onclick="return set_cover(this, <?php echo $item->id; ?>)" class="btn btn-primary btn-sm">Set as cover</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>