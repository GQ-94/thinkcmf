<?php

namespace app\user\controller;

use app\user\model\ProjectStudyModel;
use app\user\model\SchoolListModel;
use app\user\model\TeacherModel;
use cmf\controller\AdminBaseController;
use think\Model;
use think\Request;

/**
 * Class AdminTeacherController
 * @package app\user\controller
 */
class AdminTeacherController extends AdminBaseController
{
    /**
     * @var Model
     */
    private $model;

    /**
     *
     */
    protected function _initialize()
    {
        // TODO  开课前提醒
        $this->model = new TeacherModel();
    }


    /**
     * 导师列表
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // TODO 开放时间展示
        $where = [];
        $param = $this->request->param();
        if (!empty($param['user_status'])) {
            $where['user_status'] = $param['user_status'];
        }

        if (!empty($param['keyword'])) {
            $where['username|nickname'] = ['like', "%{$param['keyword']}%"];
        }

        $project_study_model = new ProjectStudyModel();
        $list = $this->model->where($where)->order('create_time', 'desc')->paginate();
        foreach ($list as &$item) {
            $item['student_sum'] = $project_study_model->where("teacher_id", $item['id'])->where("study_status", 1)->group("student_id")->count();
        }

        $list->appends($param);
        $this->assign('list', $list);
        $this->assign('page', $list->render());
        return $this->fetch();
    }


    /**
     * 修改当前用户状态
     * @param $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function status($id)
    {
        $data = $this->model->field('user_status')->find($id);
        $data->user_status = $data->user_status == 1 ? 2 : 1;

        if ($data->save()) {
            $this->success(($data->user_status == 1 ? "启用" : "停用") . "成功!");
        } else {
            $this->error("操作失败!");
        }
    }


    public function edit($id)
    {
        $info = $this->model->get($id);
        if (empty($info)) {
            $this->error("导师不存在", "user/admin_teacher/index");
        }

        $this->assign('tags', []);
        $this->assign('school_list', SchoolListModel::get_list());
        $this->assign('info', $info);
        return $this->fetch();
    }
}
