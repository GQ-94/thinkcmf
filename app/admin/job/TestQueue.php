<?php

namespace app\Admin\job;

use think\Queue;
use think\queue\Job;

/**
 * Class TestQueue
 * @package app\Admin\job
 *
 * // examples
 * $job_queue_name         = "TestQueue";
 * $job_handler_class_name = 'app\Admin\job\TestQueue';
 * Queue::push($job_handler_class_name, 'Current_time:' . date('Y-m-d H:i:s'), $job_queue_name);
 *
 * // cli
 * 单次监听
 * php think queue:work --queue TestQueue --sleep 3
 * 长期监听
 * php think queue:listen --queue TestQueue --sleep 3
 */
class TestQueue
{

    public function fire(Job $job, $data)
    {
        $is_job_done = true; // do something
        if ($is_job_done) {
            // 如果任务执行成功后 删除任务，不然这个任务会重复执行，直到达到最大重试次数后失败后，执行failed方法
            $job->delete();
        } else {
            if ($job->attempts() > 3) {
                // 通过这个方法可以检查这个任务已经重试了几次了
                $job->delete();
                // 也可以重新发布这个任务 $delay为延迟时间，表示该任务延迟2秒后再执行
                // $job->release(2);
            }
        }
    }
}