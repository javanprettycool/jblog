<?php
/**
 * Author: Javan
 * Date: 2016/1/25
 * Description:
 */


class Archive extends Contents
{
    /**
     * 调用的风格文件
     *
     * @access private
     * @var string
     */
    private $_themeFile;

    /**
     * 风格目录
     *
     * @access private
     * @var string
     */
    private $_themeDir;

    /**
     * 分页计算对象
     *
     * @access private
     * @var Typecho_Db_Query
     */
    private $_countSql;

    /**
     * 所有文章个数
     *
     * @access private
     * @var integer
     */
    private $_total = false;

    /**
     * 标记是否为从外部调用
     *
     * @access private
     * @var boolean
     */
    private $_invokeFromOutside = false;

    /**
     * 是否由聚合调用
     *
     * @access private
     * @var boolean
     */
    private $_invokeByFeed = false;

    /**
     * 当前页
     *
     * @access private
     * @var integer
     */
    private $_currentPage;

    /**
     * 生成分页的内容
     *
     * @access private
     * @var array
     */
    private $_pageRow = array();

    /**
     * 聚合器对象
     *
     * @access private
     * @var Typecho_Feed
     */
    private $_feed;

    /**
     * RSS 2.0聚合地址
     *
     * @access private
     * @var string
     */
    private $_feedUrl;

    /**
     * RSS 1.0聚合地址
     *
     * @access private
     * @var string
     */
    private $_feedRssUrl;

    /**
     * ATOM 聚合地址
     *
     * @access private
     * @var string
     */
    private $_feedAtomUrl;

    /**
     * 本页关键字
     *
     * @access private
     * @var string
     */
    private $_keywords;

    /**
     * 本页描述
     *
     * @access private
     * @var string
     */
    private $_description;

    /**
     * 聚合类型
     *
     * @access private
     * @var string
     */
    private $_feedType;

    /**
     * 聚合类型
     *
     * @access private
     * @var string
     */
    private $_feedContentType;

    /**
     * 当前feed地址
     *
     * @access private
     * @var string
     */
    private $_currentFeedUrl;

    /**
     * 归档标题
     *
     * @access private
     * @var string
     */
    private $_archiveTitle = NULL;

    /**
     * 归档类型
     *
     * @access private
     * @var string
     */
    private $_archiveType = 'index';

    /**
     * 是否为单一归档
     *
     * @access private
     * @var string
     */
    private $_archiveSingle = false;

    /**
     * 是否为自定义首页, 主要为了标记自定义首页的情况
     *
     * (default value: false)
     *
     * @var boolean
     * @access private
     */
    private $_makeSinglePageAsFrontPage = false;

    /**
     * 归档缩略名
     *
     * @access private
     * @var string
     */
    private $_archiveSlug;

    /**
     * 设置分页对象
     *
     * @access private
     * @var Typecho_Widget_Helper_PageNavigator
     */
    private $_pageNav;


    public function __construct($request, $response, $params = NULL)
    {
        parent::__construct($request, $response, $params);

        $this->config->setDefault(array(
            'pageSize'          =>  $this->options->pageSize,
            'type'              =>  NULL,
            'checkPermalink'    =>  true
        ));

        /** 用于判断是路由调用还是外部调用 */
        if (NULL == $this->config->type) {
            $this->config->type = Router::$current;
        } else {
            $this->_invokeFromOutside = true;
        }

        /** 用于判断是否为feed调用 */
        if ($this->config->isFeed) {
            $this->_invokeByFeed = true;
        }

        /** 初始化皮肤路径 */
        $this->_themeDir = rtrim($this->options->themeFile($this->options->theme, '/'), '/');

        /** 处理feed模式 **/
        if ('feed' == $this->config->type) {}
    }


    public function execute()
    {

        if ($this->have()) {
            return;
        }

        $handles = array(
            'index'                     =>  'indexHandle',
            'index_page'                =>  'indexHandle',
            'archive'                   =>  'error404Handle',
            'archive_page'              =>  'error404Handle',
            404                         =>  'error404Handle',
            'single'                    =>  'singleHandle',
            'page'                      =>  'singleHandle',
            'post'                      =>  'singleHandle',
            'attachment'                =>  'singleHandle',
            'comment_page'              =>  'singleHandle',
            'category'                  =>  'categoryHandle',
            'category_page'             =>  'categoryHandle',
            'tag'                       =>  'tagHandle',
            'tag_page'                  =>  'tagHandle',
            'author'                    =>  'authorHandle',
            'author_page'               =>  'authorHandle',
            'archive_year'              =>  'dateHandle',
            'archive_year_page'         =>  'dateHandle',
            'archive_month'             =>  'dateHandle',
            'archive_month_page'        =>  'dateHandle',
            'archive_day'               =>  'dateHandle',
            'archive_day_page'          =>  'dateHandle',
            'search'                    =>  'searchHandle',
            'search_page'               =>  'searchHandle'
        );

//        /** 处理搜索结果跳转 */
//        if (isset($this->request->s)) {
//            $filterKeywords = $this->request->filter('search')->s;
//
//            /** 跳转到搜索页 */
//            if (NULL != $filterKeywords) {
//                $this->response->redirect(Typecho_Router::url('search',
//                    array('keywords' => urlencode($filterKeywords)), $this->options->index));
//            }
//        }


        $frontPage = $this->options->frontPage;

        if ('recent' != $frontPage && $this->options->frontArchive) {
            $handles['archive'] = 'indexHandle';
            $handles['archive_page'] = 'indexHandle';
            $this->_archiveType = 'front';
        }

        $this->_currentPage = isset($this->request->page) ? $this->request->page : 1;
        $hasPushed = false;

        $selectPlugged = false;

        $select = NULL;

        /** 定时发布功能 */
        if (!$selectPlugged) {
            if ('post' == $this->parameter->type || 'page' == $this->parameter->type) {
                if ($this->user->hasLogin()) {
                    $select = $this->select()->where('table.contents.status = ? OR table.contents.status = ? OR
                            (table.contents.status = ? AND table.contents.authorId = ?)',
                        'publish', 'hidden', 'private', $this->user->uid);
                } else {
                    $select = $this->select()->where('table.contents.status = ? OR table.contents.status = ?',
                        'publish', 'hidden');
                }
            } else {
                if ($this->user->hasLogin()) {
                    $select = $this->select()->where('table.contents.status = ? OR
                            (table.contents.status = ? AND table.contents.authorId = ?)', 'publish', 'private', $this->user->uid);
                } else {
                    $select = $this->select()->where('table.contents.status = ?', 'publish');
                }
            }
            $select->where('table.contents.created < ?', $this->options->gmtTime);
        }


        /** 初始化其它变量 */
//        $this->_feedUrl = $this->options->feedUrl;
//        $this->_feedRssUrl = $this->options->feedRssUrl;
//        $this->_feedAtomUrl = $this->options->feedAtomUrl;
        $this->_keywords = $this->options->keywords;
        $this->_description = $this->options->description;

        if (isset($handles[$this->parameter->type])) {
            $handle = $handles[$this->parameter->type];
            $this->{$handle}($select, $hasPushed);
        } else {
            //$hasPushed = $this->pluginHandle()->handle($this->parameter->type, $this, $select);
        }

        $functionsFile = $this->_themeDir . 'functions.php';
        if (!$this->_invokeFromOutside && file_exists($functionsFile)) {
            require_once $functionsFile;
            if (function_exists('themeInit')) {
                themeInit($this);
            }
        }

        /** 如果已经提前压入则直接返回 */
        if ($hasPushed) {
            return;
        }


        /** 仅输出文章 */
        $this->_countSql = clone $select;

        $select->order('table.contents.created', Typecho_Db::SORT_DESC)
            ->page($this->_currentPage, $this->parameter->pageSize);
        $this->query($select);
    }




    /**
     * 重载select
     *
     * @access public
     * @return void
     */
    public function select()
    {
        if ($this->_feed) {
            // 对feed输出加入限制条件
            return parent::select()->where('table.contents.allowFeed = ?', 1)
                ->where('table.contents.password IS NULL');
        } else {
            return parent::select();
        }
    }

    /**
     * 提交查询
     *
     * @access public
     * @param mixed $select 查询对象
     * @return void
     */
    public function query($select)
    {
        $this->db->fetchAll($select, array($this, 'push'));

    }


    /**
     * 输出视图
     *
     * @access public
     * @return void
     */
    public function render()
    {
        /** 处理静态链接跳转 */
        $this->checkPermalink();

        /** 添加Pingback */
        $this->response->setHeader('X-Pingback', $this->options->xmlRpcUrl);
        $validated = false;

        //~ 自定义模板
        if (!empty($this->_themeFile)) {
            if (file_exists($this->_themeDir . $this->_themeFile)) {
                $validated = true;
            }
        }

        if (!$validated && !empty($this->_archiveType)) {

            //~ 首先找具体路径, 比如 category/default.php
            if (!$validated && !empty($this->_archiveSlug)) {
                $themeFile = $this->_archiveType . '/' . $this->_archiveSlug . '.php';
                if (file_exists($this->_themeDir . $themeFile)) {
                    $this->_themeFile = $themeFile;
                    $validated = true;
                }
            }

            //~ 然后找归档类型路径, 比如 category.php
            if (!$validated) {
                $themeFile = $this->_archiveType . '.php';
                if (file_exists($this->_themeDir . $themeFile)) {
                    $this->_themeFile = $themeFile;
                    $validated = true;
                }
            }

            //针对attachment的hook
            if (!$validated && 'attachment' == $this->_archiveType) {
                if (file_exists($this->_themeDir . 'page.php')) {
                    $this->_themeFile = 'page.php';
                    $validated = true;
                } else if (file_exists($this->_themeDir . 'post.php')) {
                    $this->_themeFile = 'post.php';
                    $validated = true;
                }
            }

            //~ 最后找归档路径, 比如 archive.php 或者 single.php
            if (!$validated && 'index' != $this->_archiveType && 'front' != $this->_archiveType) {
                $themeFile = $this->_archiveSingle ? 'single.php' : 'archive.php';
                if (file_exists($this->_themeDir . $themeFile)) {
                    $this->_themeFile = $themeFile;
                    $validated = true;
                }
            }

            if (!$validated) {
                $themeFile = 'index.php';
                if (file_exists($this->_themeDir . $themeFile)) {
                    $this->_themeFile = $themeFile;
                    $validated = true;
                }
            }
        }

        /** 文件不存在 */
        if (!$validated) {
            Typecho_Common::error(500);
        }


        /** 输出模板 */
        require_once $this->_themeDir . $this->_themeFile;

    }

    /**
     * 检查链接是否正确
     *
     * @access private
     * @return void
     */
    private function checkPermalink()
    {
        $type = $this->parameter->type;

        if (in_array($type, array('index', 'comment_page', 404))
            || $this->_makeSinglePageAsFrontPage    // 自定义首页不处理
            || !$this->parameter->checkPermalink) { // 强制关闭
            return;
        }

        if ($this->_archiveSingle) {
            $permalink = $this->permalink;
        } else {
            $value = array_merge($this->_pageRow, array(
                'page'  =>  $this->_currentPage
            ));

            $path = Typecho_Router::url($type, $value);
            $permalink = Typecho_Common::url($path, $this->options->index);
        }

        $requestUrl = $this->request->getRequestUrl();

        $src = parse_url($permalink);
        $target = parse_url($requestUrl);

        if ($src['host'] != $target['host'] || urldecode($src['path']) != urldecode($target['path'])) {
            $this->response->redirect($permalink, true);
        }
    }

    /**
     * 获取主题文件
     *
     * @access public
     * @param string $fileName 主题文件
     * @return void
     */
    public function need($fileName)
    {
        require $this->_themeDir . $fileName;
    }

}