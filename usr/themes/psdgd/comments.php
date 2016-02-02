<div id="comments" class="reply">
            <?php $this->comments()->to($comments); ?>
            <?php if ($comments->have()): ?>
			<h4><?php $this->commentsNum(_t('No Reply'), _t('Only 1 Reply'), _t('%d Replies')); ?></h4>
            
            <?php $comments->pageNav(); ?>
            
            <?php $comments->listComments(); ?>
            
            <?php endif; ?>

            <?php if($this->allow('comment')): ?>
            <div id="<?php $this->respondId(); ?>" class="respond">
            
            <div class="cancel-comment-reply">
            <?php $comments->cancelReply(); ?>
            </div>
            
			<h4 class="response"><?php _e('Leave a Reply'); ?></h4>
			<form method="post" action="<?php $this->commentUrl() ?>" id="comment_form">
                <?php if($this->user->hasLogin()): ?>
				<p>Logged in as <a href="<?php $this->options->profileUrl(); ?>"><?php $this->user->screenName(); ?></a>. <a href="<?php $this->options->logoutUrl(); ?>" title="Logout"><?php _e('退出'); ?> &raquo;</a></p>
                <?php else: ?>
				<p>
                    
					<input type="text" name="author" id="author" class="text" size="25" value="<?php $this->remember('author'); ?>" /><label for="author"><?php _e('称呼'); ?><span class="required">*</span></label>
				</p>
				<p>
                    
					<input type="text" name="mail" id="mail" class="text" size="25" value="<?php $this->remember('mail'); ?>" /><label for="mail"><?php _e('电子邮件'); ?><?php if ($this->options->commentsRequireMail): ?><span class="required">*</span><?php endif; ?></label>
				</p>
				<p>
                    
					<input type="text" name="url" id="url" class="text" size="25" value="<?php $this->remember('url'); ?>" /><label for="url"><?php _e('网站'); ?><?php if ($this->options->commentsRequireURL): ?><span class="required">*</span><?php endif; ?></label>
				</p>
                <?php endif; ?>
				<p><textarea rows="5" cols="50" name="text" class="textarea"><?php $this->remember('text'); ?></textarea></p>
				<p><input type="submit" value="<?php _e('Post Comment'); ?>" class="submit" /></p>
			</form>
            </div>
            <?php else: ?>
            <h4><?php _e('Comment Closed'); ?></h4>
            <?php endif; ?>
		</div>
