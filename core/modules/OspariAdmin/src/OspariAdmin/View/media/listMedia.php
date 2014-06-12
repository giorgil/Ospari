<?php
$title = 'Media Liberary';
$this->title = $title;

$this->setJS(OSPARI_URL . '/assets-admin/js/dropzone.js');
$this->setCSS(OSPARI_URL . '/assets-admin/css/dropzone.css');

$req = $this->req;


$mediaItems = $this->items;
$draft_id = $req->getInt('draft_id');

/**
 * !!! not the current user_id
 */
$user_id = $req->getInt('user_id');

?>
<div class="col-md-12">
    <h1><?php echo $title; ?> </h1>
    <div id="media-panel">
        <div class="media-item" id="media-item-upload"><a href="#" class="btn btn-danger btn-sm" onclick=" return open_modal();">Upload more images</a></div>
        <?php foreach ($mediaItems as $item) : ?>
            <?php include __DIR__ . '/media_item.php'; ?>
        <?php endforeach; ?>
    </div>
</div>
<br>

<script>

function set_user_image(obj, media_id) {
        var h = $(obj).html();
        $(obj).html('<i class="fa fa-spinner fa-spin"></i>');
        cb = function(res) {
            if (res.sucess) {
                window.location = res.url;
            } else {
                $(obj).html(h);
                bootbox.alert(res.message);
            }

        }
        var data = {"media_id": media_id, "user_id":<?php echo $user_id; ?>};
        $.post('/<?php echo OSPARI_ADMIN_PATH ?>/media-lib/set-user-image', data, cb);
        return false;
    }
    
    function set_user_cover(obj, media_id) {
        var h = $(obj).html();
        $(obj).html('<i class="fa fa-spinner fa-spin"></i>');
        cb = function(res) {
            if (res.sucess) {
                window.location = res.url;
            } else {
                $(obj).html(h);
                bootbox.alert(res.message);
            }

        }
        var data = {"media_id": media_id, "user_id":<?php echo $user_id; ?>};
        $.post('/<?php echo OSPARI_ADMIN_PATH ?>/media-lib/set-user-cover', data, cb);
        return false;
    }
    
    function set_draft_cover(obj, media_id) {
        var h = $(obj).html();
        $(obj).html('<i class="fa fa-spinner fa-spin"></i>');
        cb = function(res) {
            if (res.sucess) {
                window.location = res.url;
            } else {
                $(obj).html(h);
                bootbox.alert(res.message);
            }

        }
        var data = {"media_id": media_id, "draft_id":<?php echo $draft_id; ?>};
        $.post('/<?php echo OSPARI_ADMIN_PATH ?>/media-lib/set-draft-cover', data, cb);
        return false;
    }

    function set_blog_cover(obj, media_id) {
        var h = $(obj).html();
        $(obj).html('<i class="fa fa-spinner fa-spin"></i>');
        cb = function(res) {
            if (res.sucess) {
                window.location = res.url;
            } else {
                $(obj).html(h);
                bootbox.alert(res.message);
            }

        }
        var data = {"media_id": media_id};
        $.post('/<?php echo OSPARI_ADMIN_PATH ?>/media-lib/set-blog-cover', data, cb);
        return false;
    }
    
    function set_blog_logo(obj, media_id) {
        var h = $(obj).html();
        $(obj).html('<i class="fa fa-spinner fa-spin"></i>');
        cb = function(res) {
            if (res.sucess) {
                window.location = res.url;
            } else {
                $(obj).html(h);
                bootbox.alert(res.message);
            }

        }
        var data = {"media_id": media_id};
        $.post('/<?php echo OSPARI_ADMIN_PATH ?>/media-lib/set-blog-logo', data, cb);
        return false;
    }

    function open_modal() {
        bootbox.dialog({
            message: '<div style="clear:both;" id="clear-dropzone"></div><div class="dropzone" id="dropzone"></div>',
            title: 'Upload Image'
        });
        init_drop_zone();
        return false;
    }
    function init_drop_zone() {
        $("div#dropzone").dropzone(
                {
                    url: '/<?php echo OSPARI_ADMIN_PATH ?>/media-lib/upload?draft_id=<?php echo $draft_id; ?>',
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
                            if (json.success) {
                                $('#media-item-upload').after(json.html);
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


    $(document).ready(
            function() {
                $('#media-panel .thumb-popover').popover({"html": true, "trigger": "hover", "placement": "auto", "container": "body"})
            }
    );

</script>
