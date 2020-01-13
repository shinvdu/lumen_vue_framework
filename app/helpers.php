<?php

// 获取当前登录用户
if (! function_exists('auth_user')) {
    /**
     * Get the auth_user.
     *
     * @return mixed
     */
    function auth_user()
    {
        return app('Dingo\Api\Auth\Auth')->user();
    }
}

if (! function_exists('dingo_route')) {
    /**
     * 根据别名获得url.
     *
     * @param string $version
     * @param string $name
     * @param string $params
     *
     * @return string
     */
    function dingo_route($version, $name, $params = [])
    {
        return app('Dingo\Api\Routing\UrlGenerator')
            ->version($version)
            ->route($name, $params);
    }
}

if (! function_exists('trans')) {
    /**
     * Translate the given message.
     *
     * @param string $id
     * @param array  $parameters
     * @param string $domain
     * @param string $locale
     *
     * @return string
     */
    function trans($id = null, $parameters = [], $domain = 'messages', $locale = null)
    {
        if (is_null($id)) {
            return app('translator');
        }

        return app('translator')->trans($id, $parameters, $domain, $locale);
    }
}

// 获取配置信息
if (! function_exists('setting_get')) {
    /**
     * Get the setting.
     *
     * @return mixed
     */
    function variable_get($key, $default = '')
    {
        return app(\App\CodeMachine\Setting\SettingHandler::class)->get($key, $default);
    }
}

