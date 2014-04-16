<?php
?>

<div id="draft-components">

            <?php foreach ($this->components as $cmp): ?>
                <?php  $hasHandle = false;?>
                <?php include __DIR__ . '/../../../../OspariAdmin/src/OspariAdmin/View/tpl/draft_component.php'; ?>
                
            <?php endforeach; ?>

</div>