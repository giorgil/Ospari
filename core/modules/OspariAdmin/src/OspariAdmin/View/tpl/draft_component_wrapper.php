<?php
     if(!isset($cmp)){
         $cmp = $this->component;
     }
      $componentType = $cmp->getType();
      $componentID = $cmp->id;
?>
<div data-component-type="<?php echo $componentType->name; ?>" class="draft-component component-<?php echo $componentType->name; ?>" id="draft-component-<?php echo $componentID ?>">
<?php include __DIR__ . '/draft_cmp.php'; ?>
</div>