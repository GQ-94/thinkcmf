<?php

namespace app\user\model;

use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Model;

class SchoolListModel extends Model
{
    /**
     * 获取学校 {分页列表 | 全部列表}
     * @param array $param
     * @return array|\think\Paginator
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function get_list($param = [])
    {
        $model = new SchoolListModel();
        $model->field("id,name")->where(["is_delete" => 0])->order(['name' => "asc"]);
        if (empty($param)) {
            return $model->select()->toArray();
        } else {
            $model->where('name', 'like', "%{$param['keyword']}%");
            return $model->paginate();
        }
    }
}
