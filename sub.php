<?php
    $key_md5 = 'md5.txt';//在这里填写存储AccessKey MD5文件的绝对路径，例如 /www/example.com/md5.txt
    $file = 'sub.txt';//在这里填写存储普通线路订阅源文件的绝对路径，例如 /www/example.com/sub.txt
    $file_premium = 'sub2.txt';//在这里填写存储 premium 线路订阅源文件的绝对路径，例如 /www/example.com/sub2.txt
    $file_ncm = 'ncm.txt';//在这里填写存储 ncm 线路订阅源文件的绝对路径，例如 /www/example.com/ncm.txt

    function removeComment($content){
        // (\/\*.*\*\/)|(#.*?\n)|(\/\/.*?\n)|
        return preg_replace("/(<!--[\w\W\r\n]*?-->)/s", '', str_replace(array("\r\n", "\r"), "\n", $content));
    };
    function removeVmess($content){
        // (\/\*.*\*\/)|(#.*?\n)|(\/\/.*?\n)|
        return preg_replace("~\nvmess://.*~", '', $content);
    };
    function removeVless($content){
        // (\/\*.*\*\/)|(#.*?\n)|(\/\/.*?\n)|
        return preg_replace("~\nvless://.*~", '', $content);
    };
    function removeSSR($content){
        // (\/\*.*\*\/)|(#.*?\n)|(\/\/.*?\n)|
        return preg_replace("~\nssr://.*~", '', $content);
    };
    function removeSS($content){
        // (\/\*.*\*\/)|(#.*?\n)|(\/\/.*?\n)|
        return preg_replace("~\nss://.*~", '', $content);
    };
    $url_query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    if(strpos($url_query, 'key')!==false){
        parse_str($url_query, $key_from_url);
    } else {
        $key_from_url = array("key"=>"");
    };
    $key_md5_number = file_get_contents($key_md5, "r");
    if (($key_md5_number == md5($key_from_url["key"])) or ($key_md5_number == "")) {//判断url中是否传入key
        if (strpos(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), 'level=premium')!==false) {
           $str = file_get_contents($file_premium, "r") or die("Unable to open file!");
        }
        else if (strpos(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), 'level=ncm')!==false) {
            $str = file_get_contents($file_ncm, "r") or die("Unable to open file!");
        }
        else {
            $str = file_get_contents($file, "r") or die("Unable to open file!");
        }

        if (strpos(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), 'type=ss')!==false) {
           echo base64_encode(removeVmess(removeVless(removeComment($str))));
        }
        else if (strpos(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), 'type=vmess')!==false) {
           echo base64_encode(removeSSR(removeSS(removeVless(removeComment($str)))));
        }
        else if (strpos(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), 'type=vless')!==false) {
            echo base64_encode(removeSSR(removeSS(removeVmess(removeComment($str)))));
        }
        else if (strpos(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), 'type=specical')!==false) {
            echo base64_encode(removeSSR(removeSS(removeVmess(removeVless(removeComment($str))))));
        }
        else if (strpos(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), 'type=qx')!==false) {
            echo base64_encode(removeVless(removeComment($str)));
        }
        else {
            echo base64_encode(removeComment($str));
        };
    };
?>
