<?php
     if(!isset($cmp)){
         $cmp = $this->component;
     }
      $componentType = $cmp->getType();
      $componentID = $cmp->id;
?>


    <div  id="draft-component-content-<?php echo $componentID ?>">
        <div class="component-comment" id="draft-component-comment-<?php echo $componentID ?>"><?= $cmp->comment ?></div>
        <?php if ($componentType->name == 'image'): ?>

            <div id="draft-component-image-<?php echo $componentID ?>">
                <img src="<?php echo OSPARI_URL . '/content/upload/' . $cmp->code; ?>" >
            </div>
        <?php else: ?>
            <div class="component-code" id="draft-component-code-<?php echo $componentID ?>"><?= $cmp->code ?></div>
        <?php endif; ?>
    </div>

    <div class="draft-components-handle">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"  title="Config"><i class="fa fa-2x fa-cog"></i></a>
        <ul class="dropdown-menu">
            <li><a href="#" data-component-id="<?php echo $cmp->id; ?>" class="draft-components-handle-edit" title="edit" onclick=" return genericEditor.editClick(this);"><i class="fa fa-edit"></i> edit</a></li>
        </ul>
    </div>

