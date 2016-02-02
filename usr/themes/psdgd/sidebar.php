
    <div class="grid_4" id="sidebar">

        <div class="widget">
                	<form id="search" method="post" action="/">
                	<div><input type="text" name="s" class="text" size="20" /> <input type="submit" class="submit" value="<?php _e('Search'); ?>" /></div>
					</form>
	    </div>
		
		<?php if (empty($this->options->sidebarBlock) || in_array('ShowRecentPosts', $this->options->sidebarBlock)): ?>
	    <div class="widget">
			<h3><?php _e('RECENT POSTS'); ?></h3>
            <ul>
                <?php $this->widget('Widget_Contents_Post_Recent')
                ->parse('<li><a href="{permalink}">{title}</a></li>'); ?>
            </ul>
	    </div>
        <?php endif; ?>
    
        <?php if (empty($this->options->sidebarBlock) || in_array('ShowRecentComments', $this->options->sidebarBlock)): ?>
	    <div class="widget">
			<h3><?php _e('RECENT COMMENTS'); ?></h3>
            <ul>
            <?php $this->widget('Widget_Comments_Recent')->to($comments); ?>
            <?php while($comments->next()): ?>
                <li><a href="<?php $comments->permalink(); ?>"><?php $comments->author(false); ?></a>: <?php $comments->excerpt(50, '...'); ?></li>
            <?php endwhile; ?>
            </ul>
	    </div>
        <?php endif; ?>

        <?php if (empty($this->options->sidebarBlock) || in_array('ShowCategory', $this->options->sidebarBlock)): ?>
        <div class="widget">
			<h3><?php _e('CATEGORIES'); ?></h3>
            <ul>
                <?php $this->widget('Widget_Metas_Category_List')
                ->parse('<li><a href="{permalink}">{name}</a> ({count})</li>'); ?>
            </ul>
		</div>
        <?php endif; ?>

        <?php if (empty($this->options->sidebarBlock) || in_array('ShowArchive', $this->options->sidebarBlock)): ?>
        <div class="widget">
			<h3><?php _e('ARCHIVES'); ?></h3>
            <ul>
                <?php $this->widget('Widget_Contents_Post_Date', 'type=month&format=F Y')
                ->parse('<li><a href="{permalink}">{date}</a></li>'); ?>
            </ul>
		</div>
        <?php endif; ?>

        <?php if (empty($this->options->sidebarBlock) || in_array('ShowOther', $this->options->sidebarBlock)): ?>
		<div class="widget">
			<h3><?php _e('META'); ?></h3>
            <ul>
                <?php if($this->user->hasLogin()): ?>
					<li class="last"><a href="<?php $this->options->adminUrl(); ?>"><?php _e('进入后台'); ?> (<?php $this->user->screenName(); ?>)</a></li>
                    <li><a href="<?php $this->options->logoutUrl(); ?>"><?php _e('退出'); ?></a></li>
                <?php else: ?>
                    <li class="last"><a href="<?php $this->options->adminUrl('login.php'); ?>"><?php _e('登录'); ?></a></li>
                <?php endif; ?>
                <li><a href="http://validator.w3.org/check/referer">Valid XHTML</a></li>
                <li><a href="http://www.typecho.org">Typecho</a></li>
            </ul>
		</div>
        <?php endif; ?>

    </div><!-- end #sidebar -->
