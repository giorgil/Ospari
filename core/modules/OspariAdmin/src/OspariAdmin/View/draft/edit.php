<?php
$this->setJS(OSPARI_URL . '/assets-admin/js/bootstrap3-typeahead.min.js');
$this->setJS(OSPARI_URL . '/assets-admin/js/dropzone.js');
$this->setJS(OSPARI_URL . '/assets-admin/js/bootstrap-tagsinput.min.js');
$this->setJS(OSPARI_URL . '/assets-admin/js/bootstrap3-wysihtml5.all.min.js');
$this->setJS(OSPARI_URL . '/assets-admin/js/flippant.min.js');

$this->setCSS(OSPARI_URL . '/assets-admin/css/dropzone.css');
$this->setCSS(OSPARI_URL . '/assets-admin/css/bootstrap-tagsinput.css');
$this->setCSS(OSPARI_URL . '/assets-admin/css/bootstrap3-wysihtml5.min.css');
$this->setCSS(OSPARI_URL . '/assets-admin/css/flippant.css');

$title = 'Edit';
$this->title = $title;
$draft = $this->draft;
$cmpTypes = $this->cmpTypes;
?>
<div class="col-lg-6 col-lg-offset-1" id="content-preview">

</div>
<div class="row">
    <div class="col-md-6 col-md-offset-1">
        <h1><?php echo $this->escape($draft->title); ?></h1>

        <div>
            <?php echo $draft->content; ?>
        </div>
        <div id="draft-components">

            <?php foreach ($this->components as $cmp): ?>
                
                <?php include __DIR__ . '/../tpl/draft_component_wrapper.php'; ?>
                
                <?php endforeach; ?>

        </div>

        <div id="draft-component-0" class="">

        </div>
        <hr>

        <div id="component-btns">
<?php foreach ($cmpTypes as $type): ?>
                <a href="#" class="btn btn-default" data-component-type="<?= $type['name'] ?>"><?= $type['short_name'] ?></a>
            <?php endforeach; ?>
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
        <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </script>

    <script id="component-image-tpl" type="text/x-handlebars-template">
        <div class="form-group">
        <label for="comment">Comment</label>
        <textarea name="comment" id="component-comment" class="form-control op-component-comment-box" rows="3">{{comment-val}}</textarea>
        </div>
        <label for="embed_code">{{label-text}}</label>
        <div style="clear:both;" id="clear-dropzone"></div><div class="dropzone" id="dropzone"></div>
    </script>



    <script id="component-all-types-tpl" type="text/x-handlebars-template">
        <form role="form" action="{{form-action}}" method="post">
        <div class="form-group">
        <label for="comment">Comment</label>
        <textarea name="comment" id="component-comment" class="form-control op-component-comment-box" rows="3">{{comment-val}}</textarea>
        </div>
        <div class="form-group">
        <label for="embed_code">{{label-text}}</label>
        <textarea name="code" id="component-code" class="form-control op-component-code-box" rows="3">{{code-val}}</textarea>
        </div>

        <input type="hidden" name="type_id" value="{{type-id}}">
        <input type="hidden" name="component_id" value="{{component-id}}">
        <button type="submit" class="btn btn-primary">Submit</button>
        </form>

    </script>

    <script>

        $(document).ready( function(){

        genericEditor.addURL= '/<?php echo OSPARI_ADMIN_PATH . '/draft/' . $draft->id . '/add-component'; ?>';
        genericEditor.editURL= '/<?php echo OSPARI_ADMIN_PATH . '/draft/' . $draft->id . '/edit-component'; ?>';
        genericEditor.uploadURL= '/<?php echo OSPARI_ADMIN_PATH . '/media/upload?draft_id=' . $draft->id; ?>';
        genericEditor.cmpTypes = <?php echo json_encode($cmpTypes); ?>;
        } );     
    </script>