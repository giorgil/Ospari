<?php
$this->setJS(OSPARI_URL . '/assets-admin/js/bootstrap3-typeahead.min.js');
$this->setJS(OSPARI_URL . '/assets-admin/js/dropzone.js');
$this->setJS(OSPARI_URL . '/assets-admin/js/bootstrap-tagsinput.min.js');
$this->setJS(OSPARI_URL . '/assets-admin/js/bootstrap3-wysihtml5.all.min.js');


$this->setJS('//platform.twitter.com/widgets.js');
$this->setJS('//connect.facebook.net/en_US/all.js#xfbml=1');
$this->setJS('//apis.google.com/js/plusone.js');


//connect.facebook.net/en_US/all.js#xfbml=1

$this->setCSS(OSPARI_URL . '/assets-admin/css/dropzone.css');
$this->setCSS(OSPARI_URL . '/assets-admin/css/bootstrap-tagsinput.css');
$this->setCSS(OSPARI_URL . '/assets-admin/css/bootstrap3-wysihtml5.min.css');


$title = 'Edit Components';
$this->title = $title;
$draft = $this->draft;
$cmpTypes = $this->cmpTypes;


$metaFormTpl = '<form class="form-horizontal" id="meta-form"  method="post"><div class="row"><div class="col-md-12">';


foreach ($this->metaForm->getElements() as $el) {
    $metaFormTpl.= $el->toHTML_V3('col-md-2', 'col-md-10');
}
$metaFormTpl.= '</div></div></form>';
?>
<div class="col-lg-6 col-lg-offset-1" id="content-preview">

</div>
<div class="row">
    <div class="col-md-6 col-md-offset-1">
        <div id="executive-summary">
            <?php if( $draftCover = $draft->cover ): ?>
            <div id="draft-cover" style="background: url('<?php echo $draftCover; ?>')"></div>
            <?php endif; ?>
            <h1 id="draft-title"><?php echo $this->escape($draft->title); ?></h1>
            <div class="component">
                <?php echo $draft->content; ?>
            </div>
            <div class="component-handle">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"  title="Config"><i class="fa fa-2x fa-th-list"></i></a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo '/' . OSPARI_ADMIN_PATH . '/draft/edit/' . $draft->id ?>" data-draft-id="<?php echo $draft->id; ?>" class="component-handle-edit" title="edit"><i class="fa fa-edit"></i> edit</a></li>
                     <li><a href="<?php echo '/' . OSPARI_ADMIN_PATH . '/media-lib/set-draft-cover/' . $draft->id ?>" title="change cover"><i class="fa fa-star"></i> change cover</a></li>
                </ul>
            </div>
        </div>
        <div id="draft-components">

            <?php foreach ($this->components as $cmp): ?>

                <?php include __DIR__ . '/../tpl/draft_component.php'; ?>

            <?php endforeach; ?>

        </div>

        <div id="component-0" class="">

        </div>
        <hr>


        <div id="component-btns">
            <div class="well well-sm">
                <h4>Add a component</h4>
                <?php foreach ($cmpTypes as $type): ?>
                    <a href="#" class="btn btn-default" data-component-type="<?= $type['name'] ?>"><?= $type['short_name'] ?></a>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
    <div class="col-md-4">
        <div class="form-group pull-right">
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                     <i class="fa fa-cog"></i>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu text-left" id="draft-ul">
                    <li><a href="#" onclick=" return updateSlug('<?php echo $draft->id ?>', '<?php echo OSPARI_ADMIN_PATH ?>', this);" id="edit-slug"><i class="fa fa-edit"></i> Edit Url</a></li>                   
                    <li id="meta-li"><a href="#" onclick=" return addMeta('<?php echo $draft->id ?>', '<?php echo OSPARI_ADMIN_PATH ?>', this);"><i class="fa fa-code"></i>Meta-Tags</a></li>
                </ul>
            </div>
            <button type="button" class="btn btn-danger" onclick=" return try_publish('<?php echo $draft->id ?>', '<?php echo OSPARI_ADMIN_PATH ?>', this);"><?php echo $this->hasPost ? 'Update' : 'Publish' ?></button>
        </div>

        <br>
        <br>
        <div class="form-group">
            <label for="tag-input">Tags</label>
            <input type="text" name="tags" id="tag-input" class="form-control" placeholder="Type something and hit enter" value="<?php echo $this->tags ?>">
            <input type="hidden" name="input-draft-id" id="input-draft-id" value="<?= $draft->id ?>">
        </div>
    </div>
</div>

<script id="component-all-types-tpl-response" type="text/x-handlebars-template">
    <div id="draft-component-{{id}}">
    <p>{{comment}}</p>
    <div>{{code}}</div>
    </div>
</script>

<script id="component-text-tpl-response" type="text/x-handlebars-template">
    <div id="draft-component-{{id}}">
    {{comment}}
    </div>
</script>
<script id="component-img-tpl-response" type="text/x-handlebars-template">
    <div id="draft-component-{{id}}">
    <p>{{comment}}</p>
    <img src="{{src}}" alt="{{alt}}">
    </div>
</script>


<script id="component-text-tpl" type="text/x-handlebars-template">
    <form role="form" action="{{form-action}}" {{onsubmit}} method="post">
    <div class="form-group">
    <label for="comment">{{label-text}}</label>
    <textarea name="comment" id="component-comment" class="form-control op-component-text-box" rows="3">{{comment-val}}</textarea>
    </div>
    <input type="hidden" name="type_id" value="{{type-id}}">
    <input type="hidden" name="component_id" value="{{component-id}}">
    <p class="text-right"><button type="submit" class="btn btn-primary">Add Component</button></p>
    </form>
</script>

<script id="component-image-tpl" type="text/x-handlebars-template">
    <div class="form-group">
    <label for="comment">Comment</label>
    <textarea name="comment" id="component-comment" class="form-control op-component-comment-box" rows="3">{{comment-val}}</textarea>
        <br>
        {{save-btn}}
    </div>
    <label for="embed_code">{{label-text}}</label>
    <div style="clear:both;" id="clear-dropzone"></div><div class="dropzone" id="dropzone"></div>
</script>



<script id="component-all-types-tpl" type="text/x-handlebars-template">
    <form role="form" action="{{form-action}}"  {{onsubmit}} method="post">
    <div class="form-group">
    <label for="comment">Comment</label>
    <textarea name="comment" id="component-comment" class="form-control op-component-comment-box" rows="3">{{comment-val}}</textarea>
    </div>
    <div class="form-group">
    <label for="embed_code">{{label-text}}</label>
    <textarea name="code" id="component-code" required class="form-control op-component-code-box" rows="3">{{code-val}}</textarea>
    </div>

    <input type="hidden" name="type_id" value="{{type-id}}">
    <input type="hidden" name="component_id" value="{{component-id}}">
    <p class="text-right"><button type="submit" class="btn btn-primary">Add Component</button></p>
    </form>

</script>

<script>

    $(document).ready(function() {
        OspariAdmin.amdinPath = '/<?php echo OSPARI_ADMIN_PATH ?>';
        OspariAdmin.addURL = '/<?php echo OSPARI_ADMIN_PATH . '/draft/' . $draft->id . '/add-component'; ?>';
        OspariAdmin.editURL = '/<?php echo OSPARI_ADMIN_PATH . '/draft/' . $draft->id . '/edit-component'; ?>';
        OspariAdmin.uploadURL = '/<?php echo OSPARI_ADMIN_PATH . '/media/upload?draft_id=' . $draft->id; ?>';
        OspariAdmin.cmpTypes = <?php echo json_encode($cmpTypes); ?>;
        OspariAdmin.imgTextUrl= '/<?php echo OSPARI_ADMIN_PATH.'/draft/' . $draft->id . '/image-text/update'; ?>';

        $('#tag-input').tagsinput({
            typeahead: {
                source: function(query) {
                    return $.get('<?php echo OSPARI_URL . '/' . OSPARI_ADMIN_PATH ?>' + '/tags');
                }
            }
        });
        $('#tag-input').bind('itemAdded', function(event) {
            $.post(
                    '<?php echo OSPARI_URL . '/' . OSPARI_ADMIN_PATH ?>' + '/tag/add',
                    {tag: event.item, draft_id: $('#input-draft-id').val()},
            function(json) {
                if (!json.success) {
                    bootbox.alert(json.message);
                }
            },
                    'json'
                    );
        });
        $('#tag-input').bind('itemRemoved', function(event) {
            $.post(
                    '<?php echo OSPARI_URL . '/' . OSPARI_ADMIN_PATH ?>' + '/tag/delete',
                    {tag: event.item, draft_id: $('#input-draft-id').val()},
            function(json) {
                if (!json.success) {
                    bootbox.alert(json.message);
                }
            },
                    'json');
        });
        $('.bootstrap-tagsinput').addClass('col-md-10');
    });
</script>
<script id="meta-form-script" type="text/x-handlebars-template">
<?php echo $metaFormTpl; ?>
</script>
<script id="slug-form-script" type="text/x-handlebars-template">
    <form class="form-horizontal" role="form">
    <div class="form-group">
    <label for="inputPassword" class="col-sm-1 control-label">/post/</label>
    <div class="col-sm-11">
      <input type="text" class="form-control" id="slug" placeholder="Edit slug" value="<?= $this->escape($draft->slug) ?>">
    </div>
  </div>
              
    
    
    </form>
</script>  
