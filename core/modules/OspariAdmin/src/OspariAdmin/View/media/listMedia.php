<?php
$title = 'Media Liberary';
$this->title = $title;

$this->setJS(OSPARI_URL . '/assets-admin/js/dropzone.js');
$this->setCSS(OSPARI_URL . '/assets-admin/css/dropzone.css');

$mediaItems = $this->items;
$draft_id = $this->draft_id;
?>
<div class="col-md-12" id="media-panel">
    <?php foreach ($mediaItems as $item) : ?>
        <?php include __DIR__.'/../tpl/media_item.php'; ?>
    <?php endforeach; ?>
    
</div>
<br>
    <div class="col-md-offset-1 col-md-3">
        <a href="#" class="btn btn-default btn-block btn-sm" onclick=" return open_modal();">Upload more images</a>
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

function open_modal(){
  bootbox.dialog({
      message:'<div style="clear:both;" id="clear-dropzone"></div><div class="dropzone" id="dropzone"></div>',
      title:'Upload Image'
  });
  init_drop_zone();
  return false;
}
function init_drop_zone() {
        $("div#dropzone").dropzone(
                {
                    url:'/<?php echo OSPARI_ADMIN_PATH ?>/media-lib/upload' ,
                    parallelUploads: 1,
                    maxFilesize: 1,
                    paramName: 'image',
                    uploadMultiple: false,
                    thumbnailWidth: 400,
                    thumbnailHeight: 300,
                    maxFiles: 1,
                    addRemoveLinks: false,
                    init: function() {
                        this.on("error", function(file, message) {
                            this.removeFile(file);
                            bootbox.alert(message);
                        });
                        this.on('sending', function(file, xhr, formData) {

                        });
                        this.on("success", function(file, json, xmlHttp) {
                            if(json.success){
                                $('#media-panel').prepend(json.html);
                                bootbox.hideAll();
                                return;
                            }
                            bootbox.hideAll();
                            bootbox.alert(josn.message);
                        });
                        this.on("maxfilesexceeded", function(file) {
                            this.removeFile(file);
                        });
                    }
                }
        );
    }
</script>
  