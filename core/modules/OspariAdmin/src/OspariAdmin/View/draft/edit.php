<?php
$title = 'Edit Executive Summary';
$this->title = $title;
$form = $this->form;

$this->setJS(OSPARI_URL . '/assets-admin/js/bootstrap3-wysihtml5.all.min.js');
$this->setCSS(OSPARI_URL . '/assets-admin/css/bootstrap3-wysihtml5.min.css');
$btn_text = 'Save';
$title = $this->req->title;
$text = $this->req->content;
?>
<?php include __DIR__.'/../tpl/exec_summary.php'; ?>

<script>
    $(function (){
        $('#draft-content-textarea').wysihtml5({
            'font-styles': false,
            "blockquote": false,
            "lists": false, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
            "html": false, //Button which allows you to edit the generated HTML. Default false
            "link": true, //Button to insert a link. Default true
            "image": false, //Button to insert an image. Default true,
            "color": false, //Button to change color of font  
        });
    });
</script>