<?php 
  $id = $displayData['id'];
  $text = $displayData['text'];
  $class = $displayData['class'];
  $selector = $displayData['selector'];
  $active = $displayData['active'] ? ' show' : '';
  $parent = $displayData['parent'];
?>
  <div class="card">
    <div class="card-header" id="<?php echo $id ?>-header">
      <h2 class="mb-0">
        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#<?php echo $id ?>" aria-expanded="<?php echo($active ? "true":"false"); ?>" aria-controls="collapseOne">
          <?php echo $text ?>
        </button>
      </h2>
    </div>

    <div id="<?php echo $id ?>" class="collapse<?php echo $active ?>" aria-labelledby="headingOne" data-parent="#<?php echo $selector ?>">
      <div class="card-body">
