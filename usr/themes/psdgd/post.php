<?php $this->need('header.php'); ?>

    <div class="grid_10" id="content">
        <div class="post">
			<h2 class="entry_title yahei"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h2>
			<p class="entry_data data2">
				<a href="<?php $this->permalink() ?>#comments"><?php $this->commentsNum('No Reply', '1 Reply', '%d Replies'); ?></a> , Posted in <?php $this->category(','); ?> on <?php $this->date('F j, Y'); ?> 
			</p>
			<?php $this->content(); ?>
			<p class="tags"><?php _e('标签'); ?>: <?php $this->tags(', ', true, 'NONE'); ?></p>
		</div>

		<?php $this->need('comments.php'); ?>
    </div><!-- end #content-->
	<?php $this->need('sidebar.php'); ?>
	<?php $this->need('footer.php'); ?>
