<?php
declare(strict_types=1);

// use Swoole\Coroutine;
// use Swoole\Database\PDOConfig;
// use Swoole\Database\PDOPool;
// use Swoole\Runtime;
use Swoole\Coroutine\Client;
use function Swoole\Coroutine\run;

while (true) {
    # code...
    sleep(1);

}
// run(function () {
//     $client = new Client(SWOOLE_SOCK_TCP);
//     if (!$client->connect('host.docker.internal', 8282, 0.5)) {
//         echo "connect failed. Error: {$client->errCode}\n";
//     }
//     $client->send("hello world\n");
//     while ($msg = $client->recv()) {
//         # code...
//         echo $msg;

//     }
//     $client->close();
// });



// const N = 1024;
// Runtime::enableCoroutine();
// $s = microtime(true);
// Coroutine\run(function () {
//     $pool = new PDOPool((new PDOConfig)
//         ->withHost('host.docker.internal')
//         ->withPort(3306)
//         // ->withUnixSocket('/tmp/mysql.sock')
//         ->withDbName('honece_chat')
//         ->withCharset('utf8mb4')
//         ->withUsername('root')
//         ->withPassword('root')
//     );
//     // var_dump($pool->get());
//     for ($n = N; $n--;) {
//         Coroutine::create(function () use ($pool) {
//             $pdo = $pool->get();
//             $statement = $pdo->prepare('SELECT ? + ?');
//             if (!$statement) {
//                 throw new RuntimeException('Prepare failed');
//             }
//             $a = mt_rand(1, 100);
//             $b = mt_rand(1, 100);
//             $result = $statement->execute([$a, $b]);

//             $result = $statement->fetchAll();
//             echo $result;
//             if ($a + $b !== (int)$result[0][0]) {
//                 throw new RuntimeException('Bad result');
//             }
//             $pool->put($pdo);
//         });
//     }
// });
// $s = microtime(true) - $s;
// echo 'Use ' . $s . 's for ' . N . ' queries' . PHP_EOL;
