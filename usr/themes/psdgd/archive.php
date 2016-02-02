<?php $this->need('header.php'); ?>

    <div class="grid_10" id="content">
    <?php if ($this->have()): ?>
	<?php while($this->next()): ?>
       <div class="post">
			<h2 class="entry_title yahei"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h2>			
			<?php $this->content('阅读剩余部分...'); ?>
			<p class="entry_data">
				<a href="<?php $this->permalink() ?>#comments"><?php $this->commentsNum('No Reply', '1 Reply', '%d Replies'); ?></a> , Posted in <?php $this->category(','); ?> on <?php $this->date('F j, Y'); ?> 				
			</p>
        </div>
	<?php endwhile; ?>
    <?php else: ?>
        <div class="post">
            <h2 class="entry_title yahei"><?php _e('没有找到内容'); ?></h2>
        </div>
    <?php endif; ?>

        <ol class="pages clearfix">
            <?php $this->pageNav(); ?>
        </ol>
    </div><!-- end #content-->
	<?php $this->need('sidebar.php'); ?>
	<?php $this->need('footer.php'); ?>
