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