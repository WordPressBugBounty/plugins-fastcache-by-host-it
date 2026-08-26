<?php

/** @var Document $this */
$tab = $this->container->input->get('tab', 'main');
?>

<div class="wrap">
	<div class="wrap fastcache-header">
	    <h1><?php _e('FastCache', 'fastcache');?><span>/ <?php echo FASTCACHE_VERSION;?></span></h1>
	</div>
    <div class="tab-content">
	    <?php echo $this->getBuffer(); ?>
    </div>
</div>
