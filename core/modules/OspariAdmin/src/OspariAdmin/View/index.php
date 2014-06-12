<?php
$title = 'Dashboard';
$draftPager = $this->draftPager;
$mostViewedPosts = $this->mostViewedPosts;
?>
<div class="col-lg-12">
    <?php echo '<h1>' . $title . '</h1>'; ?>
    <?php if (!$this->isWritable): ?>
        <p class="alert alert-info"> <i class="fa fa-info-circle"></i> Web server cannot write in to the upload folder</p>
    <?php endif; ?> 
    <div class="row">
        <div class="col-lg-7">
            <div class="panel panel-default">


                <nav class="navbar navbar-default" role="navigation">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="#">Drafts</a>
                    </div>
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <form onsubmit="return loadDrafts('/<?php echo OSPARI_ADMIN_PATH ?>/drafts');" class="navbar-form navbar-right" role="search">
                            <div class="form-group">
                                <input type="search" name="query_string" class="form-control" placeholder="Search">
                            </div>
                            <button type="submit" class="btn btn-default">Submit</button>
                        </form>

                    </div><!-- /.navbar-collapse -->

                </nav>


                <div class="panel-body">

                    <div class="row">
                        <div id="draft-items-wrapper" class="col-md-12"></div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-lg-5">
            <div class="panel panel-default">
                <div class="panel-heading">Most Viewed Posts</div>
                <div class="panel-body">
                    
                    <?php if ($mostViewedPosts->count() > 0): ?>
                        <table class="table table-striped table-responsive table-hover">
                            <thead>
                                <tr>
                                    <th>Post</th>
                                    <th>View Count</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($mostViewedPosts as $post): ?>
                                    <tr id="post-<?php echo $post->draft_id; ?>">
                                        <td><a href="<?php echo $post->getUrl(); ?>"><?php echo $this->escape($post->title ? $post->title : 'No Title') ?></a></td>
                                        <td><?php echo $post->view_count ?></td>
                                    </tr>
                                <?php endforeach; ?>

                            </tbody>
                        </table>
                    <?php else: ?>
                        No Post Published yet!
                    <?php endif; ?>
                </div>
            </div>
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
    <a href="#" onclick="return try_delete.cancel()" class="btn btn-default"><i class="fa fa-undo"></i> Undo</a>
    </p>
    </div>
</script>
<script>
    $(document).ready(function() {
        loadDrafts();
    });
</script>
