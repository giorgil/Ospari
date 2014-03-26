<?php

$title = 'Dashboard';
$draftPager = $this->draftPager;
$mostViewedPosts = $this->mostViewedPosts;
?>
<div class="col-lg-12">
    <?php echo '<h1>'.$title.'</h1>'; ?>
<?php if( !$this->isWritable  ): ?>
    <p class="alert alert-info"> <i class="fa fa-info-circle"></i> Web server cannot write in to the upload folder</p>
<?php endif; ?> 
    <div class="row">
        <div class="col-lg-8">
            <div class="row">
                <div class="col-lg-6">
                    <h3>My Drafts</h3>
                </div>
                <div class="col-lg-6">
                    <div class="navbar-form navbar-right" role="search">
                        <div class="form-group">
                          <input type="text" name="query_string" class="form-control" placeholder="Search">
                        </div>
                        <button type="submit" class="btn btn-default" onclick="return loadDrafts('/<?php echo OSPARI_ADMIN_PATH ?>/drafts');">Search</button>
                     </div>
                </div>
            </div>
            <div id="draft-items-wrapper" style="width:100%;">
            </div>
        </div>
        <div class="col-lg-4">
            <h3>
                Most Viewed Posts
            </h3>
             <?php if($mostViewedPosts->count()>0): ?>
            <table class="table table-striped table-responsive table-hover">
                <thead>
                    <tr>
                        <th>Post</th>
                        <th>View Count</th>
                    </tr>
                </thead>
                <tbody>
                   
                    <?php foreach ( $mostViewedPosts as $post ): ?>
                    <tr id="post-<?php echo $post->draft_id; ?>">
                        <td><a href="<?php echo $post->getUrl(); ?>"><?php echo $this->escape( $post->title? $post->title:'No Title') ?></a></td>
                        <td><?php echo $post->view_count ?></td>
                    </tr>
                    <?php endforeach; ?>
                    
                </tbody>
            </table>
            <?php  else:?>
            No Post Published yet!
          <?php endif;?>
        </div>
    </div>   
</div>
<script id="undoTemplate" type="text/x-handlebars-template" >
            <div id="undo-{id}">
                <p class="text-center text-success">
                            <br/>
                            Draft Deleted
                           <br/>
                 </p>
                 <p class="text-center">
                    <a href="#" onclick="return try_delete.cancel()" class="btn btn-default"><i class="fa fa-undo"></i>Undo</a>
                 </p>
            </div>
</script>
<script>
    $(document).ready(function(){
        $('.blog-tooltip').tooltip();
        loadDrafts();
    });
</script>