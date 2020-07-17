<?php

namespace api\user\controller;

use cmf\controller\RestBaseController;

class PublicController extends RestBaseController
{
    public function passwordReset()
    {
        $this->success('Success', __FUNCTION__);
    }
}