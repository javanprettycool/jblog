<?php
/**
 * 仿wp最新版twentytwelve皮肤
 * 
 * @package www.PSD.gd
 * @author psd.gd
 * @version 2012.09.30
 * @link http://www.psd.gd
 */
 
 $this->need('header.php');
 ?>

    <div class="grid_10" id="content">
	<?php while($this->next()): ?>
        <div class="post">
			<h2 class="entry_title yahei"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h2>			
			<?php $this->content('阅读剩余部分...'); ?>
			<p class="entry_data">
				<a href="<?php $this->permalink() ?>#comments"><?php $this->commentsNum('No Reply', '1 Reply', '%d Replies'); ?></a> , Posted in <?php $this->category(','); ?> on <?php $this->date('F j, Y'); ?> 				
			</p>
        </div>
	<?php endwhile; ?>

    <?php $this->pageNav(); ?>
    </div><!-- end #content-->
	<?php $this->need('sidebar.php'); ?>
	<?php $this->need('footer.php'); ?>
