<?php
$title = 'New Post';
$this->title = $title;

$form = $this->form;

$this->setJS(OSPARI_URL . '/assets-admin/js/bootstrap3-wysihtml5.all.min.js');
$this->setCSS(OSPARI_URL . '/assets-admin/css/bootstrap3-wysihtml5.min.css');
?>

<div class="row">
    <div class="col-lg-6 col-lg-offset-3">
        <form role="form" action="<?php echo $form->getAction(); ?>" method="post">
            <div class="form-group">
                <label for="draft-title-input">Title</label>
                <input type="text" class="form-control" id="draft-title-input" name="title">
            </div>
            <div class="form-group">
                <label for="draft-content-textarea">Excerpt (Executive summary)</label>
                <textarea rows="10" autofocus="autofocus" id="draft-content-textarea" cols="8" name="content" class="form-control"></textarea>
            </div>
            <input type="hidden" name="state" value="0">
            <button type="submit" class="btn btn-primary pull-right">Next</button>
        </form>
    </div>

</div>
<script>
    $(function (){
        $('#draft-content-textarea').wysihtml5();
    });
</script>