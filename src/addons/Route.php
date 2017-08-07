<?php

namespace think\addons;

use think\Hook;
use think\Request;

/**
 * 插件执行默认控制器
 * @package think\addons
 */
class Route extends Controller
{

    /**
     * 插件执行
     */
    public function execute()
    {
        if (!empty($this->addon) && !empty($this->controller) && !empty($this->action))
        {
            // 获取类的命名空间
            $class = get_addon_class($this->addon, 'controller', $this->controller);
            if (class_exists($class))
            {
                $model = new $class();
                if ($model === false)
                {
                    abort(500, lang('addon init fail'));
                }
                // 调用操作
                if (!method_exists($model, $this->action))
                {
                    abort(500, lang('Controller Class Method Not Exists'));
                }
                $request = Request::instance();
                $request->controller($this->controller);
                $request->action($this->action);
                // 监听addons_init
                Hook::listen('addons_init', $this);
                return call_user_func_array([$model, $this->action], [$request]);
            }
            else
            {
                abort(500, lang('Controller Class Not Exists'));
            }
        }
        abort(500, lang('addon cannot name or action'));
    }

}
