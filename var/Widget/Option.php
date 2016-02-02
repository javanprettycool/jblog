<?php
/**
 * Author: Javan
 * Date: 2016/1/21
 * Description:
 */

class Option extends Widget{


    protected $db;


    public function __construct($request, $response, $params)
    {
        parent::__construct($request, $response, $params);

        $this->db = Db::get();
    }

    public function execute()
    {
        $this->db->fetchAll($this->db->select()->from('table.options')->where('user = 0'), array($this, 'push'));

        /** 支持皮肤变量重载 */
        if (!empty($this->row['theme:' . $this->row['theme']])) {
            $themeOptions = NULL;

            /** 解析变量 */
            if ($themeOptions = unserialize($this->row['theme:' . $this->row['theme']])) {
                /** 覆盖变量 */
                $this->row = array_merge($this->row, $themeOptions);
            }
        }

        $this->stack[] = &$this->row;

        $this->originalSiteUrl = $this->siteUrl;
        $this->siteUrl = Common::url(NULL, $this->siteUrl);
        $this->plugins = unserialize($this->plugins);

        $this->rootUrl = $this->request->getRequestRoot();

        //admin


        /** 增加对SSL连接的支持 */
        if ($this->request->isSecure() && 0 === strpos($this->siteUrl, 'http://')) {
            $this->siteUrl = substr_replace($this->siteUrl, 'https', 0, 4);
        }

        $this->routingTable = unserialize($this->routingTable);
        if (!isset($this->routingTable[0])) {
            /** 解析路由并缓存 */
//            $parser = new Typecho_Router_Parser($this->routingTable);
//            $parsedRoutingTable = $parser->parse();
//            $this->routingTable = array_merge(array($parsedRoutingTable), $this->routingTable);
//            $this->db->query($this->db->update('table.options')->rows(array('value' => serialize($this->routingTable)))
//                ->where('name = ?', 'routingTable'));
        }
    }

    public function push(array $value)
    {
        $this->row[$value['name']] = $value['value'];
    }

    public function themeFile($theme, $file = '')
    {
        return __ROOT_DIR__ . __THEME_DIR__ . '/' . trim($theme, './') . '/' . trim($file, './');
    }


    public function ___index()
    {
        return Common::url('index.php', $this->rootUrl);
    }
}