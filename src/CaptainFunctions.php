<?php

declare(strict_types=1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerInterface;

use Hyperf\Contract\ConfigInterface;

use Captainbi\Hyperf\Constants\Constant;
use Captainbi\Hyperf\Job\PublicJob;
use Captainbi\Hyperf\Util\Queue;
use Hyperf\Utils\Arr;

use Hyperf\HttpServer\Contract\RequestInterface;

if (!function_exists('getApplicationContainer')) {
    /**
     * Return a Application Container.
     *
     * @return ContainerInterface|null
     */
    function getApplicationContainer()
    {

        if (!ApplicationContext::hasContainer()) {
            return null;
        }

        return ApplicationContext::getContainer();
    }
}

if (!function_exists('getConfigInterface')) {
    /**
     * Return a ConfigInterface.
     *
     * @return ConfigInterface
     */
    function getConfigInterface(): ConfigInterface
    {
        //通过应用容器 获取配置类对象
        return getApplicationContainer()->get(ConfigInterface::class);
    }
}

if (!function_exists('getJobData')) {
    /**
     * 获取 job 执行配置数据
     * @param \Closure|object|string|func|mixed $callback
     * @param string $method
     * @param array $parameters
     * @param null|array $request
     * @param array $extData
     * @return array
     */
    function getJobData($callback, string $method='', array $parameters=[], $request = null, array $extData = []) {
        return Arr::collapse([
            [
                Constant::SERVICE_KEY => $callback,
                Constant::METHOD_KEY => $method,
                Constant::PARAMETERS_KEY => $parameters,
                Constant::REQUEST_DATA_KEY => $request ?? getApplicationContainer()->get(RequestInterface::class)->all(),
            ],
            $extData
        ]);
    }
}

if (!function_exists('pushQueue')) {
    /**
     * Push a new job onto the queue.
     *
     * @param  string|object|array  $job
     * @param  mixed   $data
     * @param  string|null  $channel 队列 channel
     * @return mixed
     */
    function pushQueue($job, $data = '', $channel = null)
    {
        $delay = data_get($job, Constant::QUEUE_DELAY, 0);//延迟时间 单位：秒

        try {
            $connection = data_get($job, Constant::QUEUE_CONNECTION);
            $channel = $channel !== null ? $channel : data_get($job, Constant::QUEUE_CHANNEL);

            if (is_array($job)) {
                $data = [Constant::RESPONSE_DATA_KEY => $job];
                $job = PublicJob::class;
            }

            return Queue::push($job, $data, $delay, $connection, $channel);

        } catch (\Exception $exc) {

        }

        return false;
    }
}

if (!function_exists('getInternalIp')) {
    /**
     * 获取服务器ip.
     * @return string|\RuntimeException
     */
    function getInternalIp(): string
    {
        //获取本服务的host
        $host = config('services.rpc_service_provider.local.host', null);
        if ($host !== null) {
            return $host;
        }

        $ips = swoole_get_local_ip();
        if (is_array($ips) && !empty($ips)) {
            return current($ips);
        }
        /** @var mixed|string $ip */
        $ip = gethostbyname(gethostname());
        if (is_string($ip)) {
            return $ip;
        }
        throw new \RuntimeException('Can not get the internal IP.');
    }
}


