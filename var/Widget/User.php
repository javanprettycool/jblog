<?php
/**
 * Author: Javan
 * Date: 2016/1/28
 * Description:
 */
class User extends  Widget
{
    /**
     * 用户
     *
     * @access private
     * @var array
     */
    private $_user;

    /**
     * 是否已经登录
     *
     * @access private
     * @var boolean
     */
    private $_hasLogin = NULL;

    /**
     * 全局选项
     *
     * @access protected
     * @var Widget_Options
     */
    protected $options;

    /**
     * 数据库对象
     *
     * @access protected
     * @var Typecho_Db
     */
    protected $db;

    /**
     * 用户组
     *
     * @access public
     * @var array
     */

    /**
     * 用户组
     *
     * @access public
     * @var array
     */
    public $groups = array(
        'administrator' => 0,
        'editor'		=> 1,
        'contributor'	=> 2,
        'subscriber'	=> 3,
        'visitor'		=> 4
    );

    /**
     * 构造函数,初始化组件
     *
     * @access public
     * @param mixed $request request对象
     * @param mixed $response response对象
     * @param mixed $params 参数列表
     */
    public function __construct($request, $response, $params = NULL)
    {
        parent::__construct($request, $response, $params);

        /** 初始化数据库 */
        $this->db = Typecho_Db::get();
        $this->options = $this->widget('Widget_Options');
    }

    /**
     * 执行函数
     *
     * @access public
     * @return void
     */
    public function execute()
    {
        if ($this->hasLogin()) {
            $rows = $this->db->fetchAll($this->db->select()
                ->from('table.options')->where('user = ?', $this->_user['uid']));

            $this->push($this->_user);

            foreach ($rows as $row) {
                $this->options->__set($row['name'], $row['value']);
            }

            //更新最后活动时间
            $this->db->query($this->db
                ->update('table.users')
                ->rows(array('activated' => $this->options->gmtTime))
                ->where('uid = ?', $this->_user['uid']));
        }
    }


    /**
     * 判断用户是否已经登录
     *
     * @access public
     * @return boolean
     */
    public function hasLogin()
    {
        return false;
    }

}