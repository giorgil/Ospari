<?php
$title = 'Update Ospari';
$this->title = $title;
echo "<h1>{$title}</h1>";

$isUptoDate = $this->isUptoDate;
?>

<?php if ($isUptoDate) : ?>
    <p>Your Ospari version is up to date.</p>
    <p><a href="#" class="btn btn-warning update-btn">Update anyway</a></p>
<?php else: ?>
    <p>Your Ospari version not up to date.</p>
    <p><a href="#" class="btn btn-warning update-btn">Update now</a></p>
<?php endif; ?>

<script>
    $(document).ready(
            function() {
                $('.update-btn').on('click', function() {

                    cb = function(res) {
                        if (res.success) {
                            $(this).html('<i class="fa fa-ok"></i> Ospari successfully updated.');
                        } else {
                            bootbox.alert(res.message);
                        }
                    };
                    
                    $.post(location.pathname, {}, cb);

                });
            }
    );
</script>

