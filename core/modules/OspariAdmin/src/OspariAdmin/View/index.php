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
<?php if( $draftPager->count() == 0 ): ?>
    <p>Welcome to Ospari. <a href="<?php echo OSPARI_ADMIN_PATH.'/draft/create' ?>" class="bold"><i class="fa fa-plus"></i> Write your first post.</a></p>
<?php else: ?>
  
    <div class="row">
        <div class="col-lg-8">
            <h3>My Drafts</h3>
            <table class="table table-striped table-responsive table-hover">
                <?php foreach ( $draftPager->getItems() as $draft ): ?>
                <tr>
                    <td><a href="<?php echo $draft->getEditUrl(); ?>"><?php echo $this->escape( $draft->title? $draft->title:'No Title') ?></a></td>
                    <td><?php echo $draft->edited_at ?></td>
                    <td><?php if( $draft->isPublished() ) {echo 'published'; }else{ echo 'draft'; }  ?></td>
                    <td style="width:18%">
                        <div class="btn-group">
                            <?php if($draft->isPublished()):?>
                            <a href="#" class="btn btn-default"><i class="fa fa-eye-slash"></i> Unpublish</a>
                            <?php endif; ?>
                            <a href="#" class="btn btn-default"><i class="fa fa-trash-o"></i> Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </table>

                <?php 

                $draftPager->toPagination()->toHtml('?page=');

                ?>
        </div>
        <div class="col-lg-4">
            <h3>
                Most Viewed Posts
            </h3>
            <table class="table table-striped table-responsive table-hover">
                <thead>
                    <tr>
                        <th>Post</th>
                        <th>View Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($mostViewedPosts->count()>0): ?>
                    <?php foreach ( $mostViewedPosts as $post ): ?>
                    <tr>
                        <td><a href="<?php echo $post->getUrl(); ?>"><?php echo $this->escape( $post->title? $post->title:'No Title') ?></a></td>
                        <td><?php echo $post->view_count ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php  else:?>
                      No Post Published yet!
                    <?php endif;?>
                </tbody>
            </table>
        </div>
    </div>   
<?php endif; ?>
</div>

