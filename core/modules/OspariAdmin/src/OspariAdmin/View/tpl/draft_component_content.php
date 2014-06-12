<?php


if (!isset($cmp)) {
    $cmp = $this->component;
    $hasHandle = $this->hasHandle;
}
if(!isset($hasHandle)){
    $hasHandle = TRUE;
}
$componentType = $cmp->getType();
$typeName = $componentType->name;
$componentID = $cmp->id;
$use_iFrame = $this->use_iFrame;
?>


<div  id="component-content-<?php echo $componentID ?>">
    <div class="component-comment component-comment-<?php echo $typeName; ?>" id="component-comment-<?php echo $componentID ?>"><?= $cmp->comment ?></div>
    <?php if ($componentType->name == 'image'): ?>

        <div class="component-image-image" id="draft-component-image-<?php echo $componentID ?>">
            <img src="<?php echo OSPARI_URL . '/content/upload/' . $cmp->code; ?>" >
        </div>
    <?php else: ?>
        <div class="component-code component-code-<?php echo $typeName; ?>" id="component-code-<?php echo $componentID ?>">
            <?php
            if ($componentType->short_name == 'Facebook' && $use_iFrame  ) : ?>
               <iframe id="iframe-<?php echo $componentID ?>" width="466px" height="1000px" frameborder="0" allowtransparency="true" src="/<?php echo OSPARI_ADMIN_PATH; ?>/component/embed/<?php echo $cmp->id; ?>'"  style="border: none; width: 466px; height: 1000px;"></iframe>';
              <?php else: ?>
                <?= $cmp->code; ?>
            <?php endif; ?>
        </div><br>
    <?php endif; ?>
</div>

 <?php if ($hasHandle): ?>
<div class="component-handle">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown"  title="Config"><i class="fa fa-2x fa-th-list"></i></a>
    <ul class="dropdown-menu">
        <li><a href="#" data-component-id="<?php echo $cmp->id; ?>" class="component-handle-edit" title="edit" onclick=" return OspariAdmin.editClick(this);"><i class="fa fa-edit"></i> edit</a></li>
        <li><a href="#" data-component-id="<?php echo $cmp->id; ?>" class="component-handle-delete" title="edit" onclick=" return OspariAdmin.deleteComponent(this);"><i class="fa fa-trash-o"></i> delete</a></li>
    </ul>
</div>
 <?php endif; ?>

