<?php
namespace app\chat;

use Swoole\Coroutine\Client;
use think\console\Output;
use function Swoole\Coroutine\run;
use Swoole\Coroutine;

class TcpClient
{
    static $conn;
    public $output;
    function __construct(Output $output)
    {
        $this->output = $output;
        run([$this, 'run']);
    }

    function run()
    {
        // $output = $this->output;
        $conn = self::$conn = new Client(SWOOLE_SOCK_TCP | SWOOLE_KEEP);
        if (!self::$conn->connect('host.docker.internal', 8282, 0.5)) {
            echo "服务器正在维护中，请您稍后再试\n";
            die;
        }

        Coroutine::create([$this, 'recvmsg']); //定时任务，也可以用Timer实现
        Coroutine::create([$this, 'main']);
        Coroutine::create([$this, 'keepAlive']);
    }
    function main()
    {
    }


    function recvmsg()
    {
        while (true) {
            if (!self::$conn->isConnected()) {
                break;
            }
            $data = self::$conn->recv();
            if (strlen($data) > 0) {
                echo "\t\n******************************************************\t\n";
                echo $data;
                echo "\t\n******************************************************\t\n";
            }
            else {
                if ($data === '') {
                    // 全等于空 直接关闭连接
                    self::$conn->close();
                    $this->output->writeln("服务器已关闭连接，请您退出应用");
                    break;
                }
                else {
                    if ($data === false) {
                        // 可以自行根据业务逻辑和错误码进行处理，例如：
                        // 如果超时时则不关闭连接，其他情况直接关闭连接
                        if (self::$conn->errCode !== SOCKET_ETIMEDOUT) {
                            self::$conn->close();
                            break;
                        }
                    }
                    else {
                        self::$conn->close();
                        break;
                    }
                }
            }
            \Co::sleep(0.1);
        }

    }
    /**
     * 给服务器发心跳包
     */
    function keepAlive()
    {
        while (true) {
            if (!self::$conn->isConnected()) {
                break;
            }
            \Co::sleep(3);
            self::$conn->send(json_encode(['action' => 'heartbeat', "pong pong pong"]));
        }
    }
}