<?php

namespace app\Admin\controller;

use think\Exception;
use think\worker\Server;
use Workerman\Connection\TcpConnection;
use Workerman\Lib\Timer;

/**
 *定义一个心跳间隔30秒
 */
define('HEARTBEAT_TIME', 55);

/**
 *检查连接的间隔时间
 */
define('CHECK_HEARTBEAT_TIME', 1);

/**
 * Class WorkerController
 * @package app\Admin\controller
 */
class WorkerController extends Server
{
    /**
     * @var string
     */
    protected $port = '1234';
    /**
     * @var int
     */
    protected $processes = 1;

    /**
     * @var array
     */
    protected $connections = [];


    /**
     *
     */
    protected function init()
    {
        $this->worker->name = 'wx_chat';
    }


    /**
     * @param TcpConnection $connection
     */
    public function onConnect(TcpConnection $connection)
    {
        $connection->onWebSocketConnect = function (TcpConnection $connection, $header) {
            if (isset($_SERVER['HTTP_SEC_WEBSOCKET_PROTOCOL'])) {
                if (!$this->userAuthentication(trim($_SERVER['HTTP_SEC_WEBSOCKET_PROTOCOL']))) {
                    $connection->uid = null;
                    $connection->send(json_encode(['message' => '身份验证失败']));
                    return $connection->close();
                }
                $connection->uid                     = $_SERVER['HTTP_SEC_WEBSOCKET_PROTOCOL'];
                $this->connections[$connection->uid] = $connection;
                $protocols                           = explode(',', $_SERVER['HTTP_SEC_WEBSOCKET_PROTOCOL']);
                $connection->headers                 = ['Sec-WebSocket-Protocol: ' . $protocols[0]];
                // echo $connection->uid . ' --> 登录' . PHP_EOL;
            } else {
                $connection->uid = null;
                $connection->send(json_encode(['message' => '身份验证失败']));
                return $connection->close();
            }
        };
    }


    /**
     * @param TcpConnection $connection
     * @param $data
     */
    public function onMessage(TcpConnection $connection, $data)
    {
        /**
         * {"action":"send_private_msg","params":{"user_id":94113786,"message":"你好！我是小豆！","message_type":"text"},"echo":1648451782}
         * {"action":"send_group_msg","params":{"group_id":340893636,"message":"大家好！我是小豆！","message_type":"file"},"echo":1648451782}
         */
        // 设置当前连接最后一次发送消息的时间
        $connection->lastMessageTime = time();

        if ($data) {
            $data = json_decode($data, true);
            // 判断单聊还是群聊
            if ($data['message_type'] == 'single_chat') {
                // 已读未读标记
                $read_message_flag = false;
                // 发送信息到客户端
                if (isset($this->connections[$data['to']])) {
                    $this->connections[$data['to']]->send($data['message']);
                    $read_message_flag = true;
                }
                $this->writeChatHistory();
            } else {
                // 群发给在线的用户
                foreach ($this->connections as $connection) {
                    $connection->send($data['message']);
                    $this->writeChatHistory();
                }
            }
        }
    }


    /**
     * @param TcpConnection $connection
     */
    public function onClose(TcpConnection $connection)
    {
        if (isset($this->connections[$connection->uid])) {
            // echo $connection->uid . ' --> 退出' . PHP_EOL;
            unset($this->connections[$connection->uid]);
        }
    }


    /**
     * @param TcpConnection $connection
     * @param $code
     * @param $msg
     */
    public function onError(TcpConnection $connection, $code, $msg)
    {
        echo "error $code $msg\n";
    }


    /**
     * @param $worker
     */
    public function onWorkerStart($worker)
    {
        // 心跳检测业务
        Timer::add(CHECK_HEARTBEAT_TIME, function () {
            $now = time();
            foreach ($this->connections as $connection) {
                // 给新连接连接设置最后一次发送消息的时间
                if (empty($connection->lastMessageTime)) {
                    $connection->lastMessageTime = $now;
                    continue;
                }

                // 上次通讯时间间隔大于心跳间隔，则认为客户端已经下线，关闭连接
                if ($now - $connection->lastMessageTime > HEARTBEAT_TIME) {
                    $connection->close();
                }
            }
        });
    }


    /**
     * @param $open_id
     * @return int
     */
    private function userAuthentication($open_id)
    {
        try {
            $count = (new \app\user\model\TeacherModel)->where(['open_id' => $open_id])->count();
        } catch (Exception $exception) {
            $count = 0;
        }
        return (int)$count;
    }


    private function writeChatHistory()
    {
        // TODO
    }
}