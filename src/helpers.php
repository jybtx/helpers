<?php

if ( !function_exists('input_clear_str') )
{
    /**
     * [对提交的编辑内容进行处理]
     * @Author jybtx
     * @date   2021-01-25
     * @param  [type]     $params [description]
     * @param  [type]     $length [description]
     * @return [type]             [description]
     */
    function input_clear_str( $params, $length = 120 )
    {
        $params = trim($params);
        $params = strip_tags($params); //去掉HTML、XML 以及 PHP 的标签
        $params = safe_replace($params); // html标记转换
        $params = add_special_char($params);
        $params = remove_xss($params); //过滤XSS脚本
        $params = substr($params, 0, $length);
        return $params;
    }
}

if ( !function_exists('remove_xss') )
{
    /**
     * [xss过滤函数]
     * @Author jybtx
     * @date   2021-01-25
     * @param  [type]     $string [description]
     * @return [type]             [description]
     */
    function remove_xss($string)
    {
        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);
        $parm1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
        $parm2 = array('onabort', 'onactivate', 'alert', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $parm  = array_merge($parm1, $parm2);
        for ($i = 0; $i < sizeof($parm); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($parm[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[x|X]0([9][a][b]);?)?';
                    $pattern .= '|(&#0([9][10][13]);?)?';
                    $pattern .= ')?';
                }
                $pattern .= $parm[$i][$j];
            }
            $pattern .= '/i';
            $string = preg_replace($pattern, '', $string);
        }
        return $string;
    }
}

if ( !function_exists('add_special_char') )
{
    /**
     * [数据清理,防注入]
     * @Author jybtx
     * @date   2021-01-25
     * @param  [type]     $params [description]
     */
    function add_special_char($params)
    {
        if (preg_match("/\b(select|insert|update|delete|truncate|drop)\b/i", $params)) {
            $params = preg_replace("/\b(select|insert|update|delete|truncate|drop)\b/i", '', $params);
        }
        return $params;
    }
}

if ( !function_exists('safe_replace') )
{
    /**
     * [安全过滤函数]
     * @Author jybtx
     * @date   2021-01-25
     * @param  [type]     $string [description]
     * @return [type]             [description]
     */
    function safe_replace($string)
    {
        $string = str_replace('%20', '', $string);
        $string = str_replace('%27', '', $string);
        $string = str_replace('%2527', '', $string);
        $string = str_replace('*', '', $string);
        $string = str_replace('"', '&quot;', $string);
        $string = str_replace("'", '', $string);
        $string = str_replace('"', '', $string);
        $string = str_replace(';', '', $string);
        $string = str_replace('<', '&lt;', $string);
        $string = str_replace('>', '&gt;', $string);
        $string = str_replace("{", '', $string);
        $string = str_replace('}', '', $string);
        $string = str_replace('\\', '', $string);
        return $string;
    }
}
if ( !function_exists('get_client_real_ip') )
{
    /**
     * [获取客户端真实 IP]
     * @Author jybtx
     * @date   2021-01-25
     * @return [type]     [description]
     */
    function get_client_real_ip()
    {
        $realip = false;

        if(isset($_SERVER["HTTP_CDN_SRC_IP"]))
        {
            $realip = $_SERVER["HTTP_CDN_SRC_IP"]; //网宿CDN 真实IP

        }else{

            if (isset($_SERVER)){

                if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){

                    $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];

                } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {

                    $realip = $_SERVER["HTTP_CLIENT_IP"];

                } else {

                    $realip = $_SERVER["REMOTE_ADDR"];
                }
            } else {
                
                if (getenv("HTTP_X_FORWARDED_FOR")){

                    $realip = getenv("HTTP_X_FORWARDED_FOR");

                } else if (getenv("HTTP_CLIENT_IP")) {

                    $realip = getenv("HTTP_CLIENT_IP");

                } else {

                    $realip = getenv("REMOTE_ADDR");

                }
            }
        }
        return $realip;
    }
}

if ( !function_exists('hidden_string') )
{
    /**
     * [隐藏字符串]
     * @author jybtx <jyhilichuan@163.com>
     * @date   2021-01-28
     * @param  [type]     $string [字符串]
     * @param  [type]     $start  [开始位置]
     * @param  [type]     $length [结束位置]
     * @return [type]             [description]
     */
    function hidden_string($string,$start = 3,$length = -3)
    {
        $len = strlen($string);
        $len = $len - ($start + $length);
        $str = str_repeat('*', $len);
        return substr_replace( $string, $str, $start, $length );
    }
}

if ( !function_exists('array_remove_element') ) {
    /**
     * [删除数组中的指定元素]
     * @author jybtx <jyhilichuan@163.com>
     * @date   2021-02-18
     * @param  [type]     &$arr    [description]
     * @param  [type]     $element [description]
     * @return [type]              [description]
     */
    function array_remove_element(&$arr, $element): array
    {
        if ( in_array($element, $arr) ) {
            array_splice($arr, array_search($element, $arr), 1);
        }
        return $arr;
    }
}

if ( !function_exists('two_array_to_string') ) {
    /**
     * [二维数组转字符串]
     * @author jybtx <jyhilichuan@163.com>
     * @date   2021-02-18
     * @param  array      $params [description]
     * @return [type]             [description]
     */
    function two_array_to_string(array $params)
    {
        if ( is_array($params) ) {
            return implode(',', array_map('tdaToString', $params));
        }
        return '';
    }
}

if ( !function_exists('two_array_to_one_array') ) {
    /**
     * [用array_reduce()实现二维转一维]
     * [array_merge把相同字符串键名的数组覆盖合并，所以必须先用array_value取出值后，再合并]
     * @author jybtx <jyhilichuan@163.com>
     * @date   2021-02-18
     * @param  array      $params [description]
     * @return [type]             [description]
     */
    function two_array_to_one_array(array $params): array
    {
        $result = array_reduce($params, function($result, $item){
            return array_merge($result, array_values($item));
        }, []);
        return $result;
    }
}

if ( !function_exists('multid_array_to_one_array') ) {
    /**
     * [多维数组转一维数组]
     * @author jybtx <jyhilichuan@163.com>
     * @date   2021-02-18
     * @param  array      $params [description]
     * @return [type]             [description]
     */
    function multid_array_to_one_array(array $params): array
    {
        $result = [];
        array_walk_recursive($params, function ($value) use (&$result) {
            array_push($result, $value);
        });

        return $result;
    }
}

if ( !function_exists('deep_array_to_one_array') ) {
    /**
     * [任何多维数组都能转一维数组]
     * @author jybtx <jyhilichuan@163.com>
     * @date   2021-02-18
     * @param  array      $params [description]
     * @return [type]             [description]
     */
    function deep_array_to_one_array(array $params): array 
    {
        $result = [];
        array_walk_recursive($params, function ($value) use (&$result) {
            array_push($result, $value);
        });

        return $result;
    }
}
