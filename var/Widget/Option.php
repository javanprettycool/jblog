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
        $this->db->fectchAll($this->db->select()->from('table.options')->where('user = 0'), array($this, 'push'));

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

    }

    public function push(array $value)
    {
        $this->row[$value['name']] = $value['value'];
    }
}