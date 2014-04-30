<?php
$title = 'Media Liberary';
$this->title = $title;

$mediaItems = $this->items;
$draft_id = $this->draft_id;
?>
<div class="col-md-12">

    <?php foreach ($mediaItems as $item) : ?>
        <div class="col-md-3">
            <div class="thumbnail">
                <img src="/content/upload/<?php echo $item->thumb ?>">
                <?php if ($draft_id): ?>
                    <div class="caption">
                        <a href="#" onclick="return set_cover(this, <?php echo $item->id; ?>)" class="btn btn-primary btn-sm">Set as cover</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>


    <?php endforeach; ?>


</div>
<script>

function set_cover(obj, media_id){
    var h = $(obj).html();
    $(obj).html('<i class="fa fa-spinner fa-spin"></i>');
    cb = function( res ){
        if( res.sucess ){
            window.location = res.url;
        }else{
            $(obj).html(h);
            bootbox.alert(res.message);
        }
        
    }
    var data = {"media_id":media_id, "draft_id":<?php echo $draft_id; ?>};
    $.post('/<?php echo OSPARI_ADMIN_PATH ?>/media-lib/set-cover', data, cb );
    return false;
}

</script>
  