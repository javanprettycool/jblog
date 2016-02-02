<?php
/**
 * Author: Javan
 * Date: 2016/1/26
 * Description:
 */
class Contents extends AbstractContent
{
    /**
     * 查询方法
     *
     * @access public
     * @return Typecho_Db_Query
     */
    public function select()
    {
        return $this->db->select('table.contents.cid', 'table.contents.title', 'table.contents.slug', 'table.contents.created', 'table.contents.authorId',
            'table.contents.modified', 'table.contents.type', 'table.contents.status', 'table.contents.text', 'table.contents.commentsNum', 'table.contents.order',
            'table.contents.template', 'table.contents.password', 'table.contents.allowComment', 'table.contents.allowPing', 'table.contents.allowFeed',
            'table.contents.parent')->from('table.contents');
    }

    /**
     * 获得所有记录数
     *
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function size(Typecho_Db_Query $condition)
    {}

    /**
     * 增加记录方法
     *
     * @access public
     * @param array $rows 字段对应值
     * @return integer
     */
    public function insert(array $rows)
    {}

    /**
     * 更新记录方法
     *
     * @access public
     * @param array $rows 字段对应值
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function update(array $rows, Typecho_Db_Query $condition)
    {}

    /**
     * 删除记录方法
     *
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function delete(Typecho_Db_Query $condition)
    {}

    /**
     * 通用过滤器
     *
     * @access public
     * @param array $value 需要过滤的行数据
     * @return array
     * @throws Typecho_Widget_Exception
     */
    public function filter(array $value)
    {
//        /** 取出所有分类 */
//        $value['categories'] = $this->db->fetchAll($this->db
//            ->select()->from('table.metas')
//            ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
//            ->where('table.relationships.cid = ?', $value['cid'])
//            ->where('table.metas.type = ?', 'category')
//            ->order('table.metas.order', Typecho_Db::SORT_ASC), array($this->widget('Widget_Metas_Category_List'), 'filter'));
        $value['category'] = NULL;
        $value['directory'] = array();

//        /** 取出第一个分类作为slug条件 */
//        if (!empty($value['categories'])) {
//            $value['category'] = $value['categories'][0]['slug'];
//
//            $value['directory'] = $this->widget('Widget_Metas_Category_List')->getAllParents($value['categories'][0]['mid']);
//            $value['directory'][] = $value['category'];
//        }

        $value['date'] = new Date($value['created']);

        /** 生成日期 */
        $value['year'] = $value['date']->year;
        $value['month'] = $value['date']->month;
        $value['day'] = $value['date']->day;

        /** 生成访问权限 */
        $value['hidden'] = false;

        /** 获取路由类型并判断此类型在路由表中是否存在 */
        $type = $value['type'];
        $routeExists = (NULL != Typecho_Router::get($type));

       // $tmpSlug = $value['slug'];
        $tmpCategory = $value['category'];
        $tmpDirectory = $value['directory'];
        //$value['slug'] = urlencode($value['slug']);
        $value['category'] = urlencode($value['category']);
        $value['directory'] = implode('/', array_map('urlencode', $value['directory']));

        /** 生成静态路径 */
        $value['pathinfo'] = $routeExists ? Typecho_Router::url($type, $value) : '#';

        /** 生成静态链接 */
        $value['permalink'] = Typecho_Common::url($value['pathinfo'], $this->options->index);

        /** 处理附件 */
        if ('attachment' == $type) {
            $content = @unserialize($value['text']);

            //增加数据信息
            $value['attachment'] = new Typecho_Config($content);
            $value['attachment']->isImage = in_array($content['type'], array('jpg', 'jpeg', 'gif', 'png', 'tiff', 'bmp'));
            $value['attachment']->url = Widget_Upload::attachmentHandle($value);

            if ($value['attachment']->isImage) {
                $value['text'] = '<img src="' . $value['attachment']->url . '" alt="' .
                    $value['title'] . '" />';
            } else {
                $value['text'] = '<a href="' . $value['attachment']->url . '" title="' .
                    $value['title'] . '">' . $value['title'] . '</a>';
            }
        }

        /** 处理Markdown **/
        $value['isMarkdown'] = (0 === strpos($value['text'], '<!--markdown-->'));
        if ($value['isMarkdown']) {
            $value['text'] = substr($value['text'], 15);
        }

//        /** 生成聚合链接 */
//        /** RSS 2.0 */
//        $value['feedUrl'] = $routeExists ? Typecho_Router::url($type, $value, $this->options->feedUrl) : '#';
//
//        /** RSS 1.0 */
//        $value['feedRssUrl'] = $routeExists ? Typecho_Router::url($type, $value, $this->options->feedRssUrl) : '#';
//
//        /** ATOM 1.0 */
//        $value['feedAtomUrl'] = $routeExists ? Typecho_Router::url($type, $value, $this->options->feedAtomUrl) : '#';

        $value['slug'] = $tmpSlug;
        $value['category'] = $tmpCategory;
        $value['directory'] = $tmpDirectory;

        /** 处理密码保护流程 */
        if (!empty($value['password']) &&
            $value['password'] !== Typecho_Cookie::get('protectPassword') &&
            $value['authorId'] != $this->user->uid &&
            !$this->user->pass('editor', true)) {
            $value['hidden'] = true;

            /** 抛出错误 */
            if ($this->request->isPost() && isset($this->request->protectPassword)) {
                throw new Typecho_Widget_Exception(_t('对不起,您输入的密码错误'), 403);
            }
        }

        $value = $this->pluginHandle(__CLASS__)->filter($value, $this);

        /** 如果访问权限被禁止 */
        if ($value['hidden']) {
            $value['text'] = '<form class="protected" action="' . $this->security->getTokenUrl($value['permalink'])
                . '" method="post">' .
                '<p class="word">' . _t('请输入密码访问') . '</p>' .
                '<p><input type="password" class="text" name="protectPassword" />
            <input type="submit" class="submit" value="' . _t('提交') . '" /></p>' .
                '</form>';

            $value['title'] = _t('此内容被密码保护');
            $value['tags'] = array();
            $value['commentsNum'] = 0;
        }

        return $value;
    }


    /**
     * 将每行的值压入堆栈
     *
     * @access public
     * @param array $value 每行的值
     * @return array
     */
    public function push(array $value)
    {
        $value = $this->filter($value);
        return parent::push($value);
    }
}