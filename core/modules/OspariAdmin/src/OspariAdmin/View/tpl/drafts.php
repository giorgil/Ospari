<?php if ($this->draftPager->getItems()->count() > 0): ?>

    <?php foreach ($this->draftPager->getItems() as $draft): ?>
        <div class="row draft-item" id="row-<?php echo $draft->id ?>"  draft-id="<?php echo $draft->id ?>">
            <div class="col-md-7">
                <a href="<?php echo $draft->getEditUrl(); ?>"><?php echo $this->escape($draft->title ? $draft->title : 'No Title') ?></a>
            </div>
            <div class="col-md-2 edit-date">
                <time><?php echo $draft->edited_at ?></time>
            </div>
            <div class="col-md-2 blog-status">
                <?php
                if ($draft->isPublished()) {
                    echo 'published';
                } else {
                    echo 'draft';
                }
                ?>
            </div>
            <div class="col-md-1 blog-action">
                <?php if ($draft->isPublished()): ?>
                    <a href="#" class="blog-tooltip" data-toggle="tooltip" data-placement="top" title data-original-title="Unpublish" onclick=" return try_unpublish(<?php echo $draft->id ?>, '/<?php echo OSPARI_ADMIN_PATH ?>');"><i class="fa fa-eye-slash"></i></a>
                <?php else: ?>
                    <a href="#" onclick=" return try_delete(<?php echo $draft->id ?>, '/<?php echo OSPARI_ADMIN_PATH ?>');"><i class="fa fa-trash-o"></i></a>
        <?php endif; ?>
            </div>
        </div>
        <br id="break-<?php echo $draft->id ?>" class="draf-line-break">
        <?php endforeach; ?>
    <div id="drafts-pagination">
        <?php
        $uri = new \NZ\Uri(\NZ\HttpRequest::getInstance()->getCurrentUrl());
        $uri->removeParam('page');
        if ($uri->hasParams()) {
            echo $this->draftPager->toPagination()->toHtml($uri->__toString() . '&page=', null, null, 3);
        } else {
            echo $this->draftPager->toPagination()->toHtml($uri->__toString() . '?page=', null, null, 3);
        }
        ?>
    </div>
<?php else: ?>
    <div class="row no-data-found">
        <div class="col-lg-12">
            No data found!
        </div>
    </div>
<?php endif; ?>
<script>
    $(function() {
        $('a', '#drafts-pagination').click(function(e) {
            e.preventDefault();
            loadDrafts($(this).attr('href'));
            //console.log($(this).attr('href'));
        });
    });
</script>