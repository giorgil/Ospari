<?php
     if(!isset($cmp)){
         $cmp = $this->component;
     }
      $componentType = $cmp->getType();
      $componentID = $cmp->id;
?>
<div data-component-type="<?php echo $componentType->name; ?>" class="component component-<?php echo $componentType->name; ?>" id="component-<?php echo $componentID ?>">
<?php include __DIR__ . '/draft_component_content.php'; ?>
</div>