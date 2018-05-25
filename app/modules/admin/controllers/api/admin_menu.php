<?php
/**
 * Created by PhpStorm.
 * User: river
 * Date: 14-11-18
 * Time: 下午2:03
 */
class Admin_menu extends API_Controller
{
    protected $model_name = 'admin';
    public function menu_list_get()
    {
        if($this->app['type'] == APPTYPE_SITE) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $sort = $this->get('sort') ? $this->get('sort') : 'listorder';
        $order = $this->get('order') ? $this->get('order') : 'asc';
        $menu_type = $this->get('menu_type') ? $this->get('menu_type') : 'admin';

        $where = '(1=1) ';

        if($menu_type == 'admin'){
            $menu_table = 'admin_node_model';

        }elseif($menu_type == 'shoper'){
            $menu_table = 'shoper_node_model';
        }elseif($menu_type == 'siter'){
            $menu_table = 'siter_node_model';
        }

        $options = array();

        $this->load->model($menu_table);

        $response['data'] = $this
            ->$menu_table
            ->where($where)
            ->order_by('parent_id' , 'asc')
            ->order_by($sort, $order)
            ->find_all($options);
        //echo $this->db->last_query();

        $response['ret'] = 0;
        $this->response($response);
    }

    public function add_menu_post()
    {
        if($this->app['type'] == APPTYPE_SITE) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $data['menu_type'] = $this->post('menu_type') ? $this->post('menu_type') : 'admin';
        if($data['menu_type'] == 'admin'){
            $menu_table = 'admin_node_model';
        }elseif($data['menu_type'] == 'shoper'){
            $menu_table = 'shoper_node_model';
        }elseif($data['menu_type'] == 'siter'){
            $menu_table = 'siter_node_model';
        }else{
            $this->response(array('ret' => 500, 'msg' => '表参数错误'), 500);
        }

        $data['parent_id'] = $this->post('parent_id') ? $this->post('parent_id') : 0;
        $data['name'] = trim($this->post('name'));
        $data['alias']  = trim($this->post('alias'));
        $data['url']  = trim($this->post('url'));
        $data['icon'] = trim($this->post('icon'));
        $data['listorder'] = $this->post('listorder');

        //var_dump($data['listorder']);die;

        if(!$data['name']) {
            $this->response(array('ret' => 801, 'msg' => '菜单名称不能为空'));
        }
        if(!$data['alias']) {
            $this->response(array('ret' => 802, 'msg' => '英文名称不能为空'));
        }
        if(!$data['url']) {
            $this->response(array('ret' => 803, 'msg' => 'URL不能为空'));
        }

        $this->load->model($menu_table);
        if(!$this->$menu_table->add($data)) {
            $this->response(array('ret' => 500, 'msg' => '系统错误'));
        }
        $this->response(array('ret' => 0), 201);
    }

    public function edit_menu_put()
    {
        if($this->app['type'] == APPTYPE_SITE) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $id = $this->put('id');

        $data['menu_type'] = $this->post('menu_type') ? $this->post('menu_type') : 'admin';
        if($data['menu_type'] == 'admin'){
            $menu_table = 'admin_node_model';
        }elseif($data['menu_type'] == 'shoper'){
            $menu_table = 'shoper_node_model';
        }elseif($data['menu_type'] == 'siter'){
            $menu_table = 'siter_node_model';
        }else{
            $this->response(array('ret' => 500, 'msg' => '表参数错误'), 500);
        }

        $data['name'] = trim($this->post('name'));
        $data['alias']  = trim($this->post('alias'));
        $data['url']  = trim($this->post('url'));
        $data['icon'] = trim($this->post('icon'));
        $data['listorder'] = $this->post('listorder');

        if(!$data['name']) {
            $this->response(array('ret' => 801, 'msg' => '菜单名称不能为空'));
        }
        if(!$data['alias']) {
            $this->response(array('ret' => 802, 'msg' => '英文名称不能为空'));
        }
        if(!$data['url']) {
            $this->response(array('ret' => 803, 'msg' => 'URL不能为空'));
        }

        $this->load->model($menu_table);
        if(!$this->$menu_table->where(array('id' => $id))->edit($data)) {
            $this->response(array('ret' => 500, 'msg' => '系统错误'));
        }
        $this->response(array('ret' => 0), 201);
    }

    public function del_menu_delete()
    {
        if($this->app['type'] == APPTYPE_SITE) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $id = intval($this->get('id'));
        $menu_type = $this->get('menu_type') ? $this->get('menu_type') : 'admin';
        if($menu_type == 'admin'){
            $menu_table = 'admin_node_model';
        }elseif($menu_type == 'shoper'){
            $menu_table = 'shoper_node_model';
        }elseif($menu_type == 'siter'){
            $menu_table = 'siter_node_model';
        }else{
            $this->response(array('ret' => 500, 'msg' => '表参数错误'), 500);
        }

        $this->load->model($menu_table);

        //判断是否有下级菜单
        $menus = $this->$menu_table->where(array('parent_id' => $id))->count();
        if($menus > 0) {
            $this->response(array('ret' => 417, 'msg' => '对不起，当前菜单存在下级菜单，无法删除!'));
        }

        $menu = $this->$menu_table->where(array('id' => $id))->find();
        if($menu){
            $this->$menu_table->where(array('id' => $id))->delete();
        }else{
            $this->response(array('ret' => 500, 'msg' => '参数错误'), 500);
        }
        $this->response(array('ret' => 0));
    }

    public function edit_listorder_put()
    {
        if($this->app['type'] != APPTYPE_ADMIN) {
            $this->response(array('ret' => 403, 'msg' => '权限不足'), 403);
        }

        $menu_type = $this->put('menu_type');
        if($menu_type == 'admin'){
            $menu_table = 'admin_node_model';
        }elseif($menu_type == 'shoper'){
            $menu_table = 'shoper_node_model';
        }elseif($menu_type == 'siter'){
            $menu_table = 'siter_node_model';
        }

        $id = $this->put('id');

        $this->load->model($menu_table);
        if($id){
            $where['id'] = $id;
            $data['listorder'] = $this->put('listorder') ? $this->put('listorder') : 0;
            if($this->$menu_table->where($where)->edit($data)) {
                $this->response(array('ret' => 0));
            }
            $this->response(array('ret' => 500, 'msg' => '修改失败'));
        }
        $this->response(array('ret' => 500, 'msg' => '参数错误'));
    }
}