<?php
$itle = 'New Post';
$this->title = $title;

$form = $this->form;
?>

<div class="row">
    <div class="col-lg-6 col-lg-offset-3">
        <form role="form" action="<?php echo $form->getAction(); ?>">
            <div class="form-group">
                <label for="draft-title-input">Title</label>
                <input type="text" class="form-control" d="draft-title-input">
            </div>
            <div class="form-group">
                <label for="draft-content-textarea">Excerpt (Executive summary)</label>
                <textarea rows="10" autofocus="autofocus" id="draft-content-textarea" cols="8" name="content" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary pull-right">Next</button>
        </form>
    </div>

</div>
