<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2019/1/16
 * Time: 9:47 AM
 */

namespace CarPay;

use CarPay\Api\Order;
use CarPay\Api\User;
use CarPay\Core\CarPayException;

class CarClient
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var CarClient
     */
    public static $instance;

    /**
     * CarClient constructor.
     * @param array $config
     * @throws CarPayException
     */
    private function __construct(array $config)
    {
        if (!$config) {
            throw new CarPayException("配置信息初始化失败");
        }
        $this->config = $config;
    }

    /**
     * 初始化实例
     * @param array $config
     * @return CarClient
     */
    public static function init(array $config)
    {
        if (!self::$instance) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**
     * 获取配置信息
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * 获取车主平台用户实例
     * @return User
     */
    public function user(): User
    {
        if ($this->user) {
            return $this->user;
        }
        return $this->user = new User($this->getConfig());
    }

    /**
     * 获取车主平台订单实例
     * @return Order
     */
    public function order(): Order
    {
        if ($this->order) {
            return $this->order();
        }
        return $this->order = new Order($this->getConfig());
    }

}