<?php

namespace common\helpers;

/**
 * 封装一些常用的网络操作函数
 * @author 付学宝
 */
abstract class NetHelper
{
    /**
     * 错误编码
     */
    public static $errCode = 0;
    /**
     * 错误信息,无错误为''
     */
    public static $errMsg  = '';

    /**
     * 清除错误信息,在每个函数的开始调用
     */
    private static function clearError()
    {
        self::$errCode = 0;
        self::$errMsg   = '';
    }

    /**
     * 对socket_read的封装,支持多个包的传播,此函数针对TcpServer的
     * 前8个字节为消息的长度
     * 接下来的4个字节为错误编码
     * 接下来的是正文
     * @param socket      socket句柄
     * @param int maxLength   能接收数据的字符串长度
     *
     * @return string 正确返回读取的数据,错误返回false
     */
    public static function tcpSocketRead(&$socket, $maxLength)
    {
        self::clearError();
        $str = @socket_read($socket, 10240);
        if ($str === false){
            self::$errCode = 10102;
            self::$errMsg  = @socket_strerror(@socket_last_error($socket));
            return false;
        }
        if (strlen($str) < 8) {
            self::$errCode = 10102;
            self::$errMsg  = 'bad tcp bag';
            return false;
        }
        $len = trim(substr($str, 0, 8));
        if(!is_numeric($len)){
            self::$errCode = 10104;
            self::$errMsg  = 'bad tcp bag';
            return false;
        }
        if ($len > $maxLength) {
            self::$errCode = 10105;
            self::$errMsg  = 'tcp bag too big';
            return false;
        }
        $message = substr($str, 8);
        $n = strlen($message);
        if ($n == $len) {
            return $message;
        }
        if ($n > $len) {
            self::$errCode = 10104;
            self::$errMsg  = 'bad tcp bag';
            return false;
        }

        while ($len > $n)
        {
            $tmp = @socket_read($socket, 10240);
            if ($tmp === false) {
                self::$errCode = 10102;
                self::$errMsg  = @socket_strerror(@socket_last_error($socket));
                return false;
            }
            $message .= $tmp;
            $n = strlen($message);
            unset($tmp);
        }
        if ($n != $len) {
            self::$errCode = 10105;
            self::$errMsg  = 'bad tcp bag';
            return false;
        }
        return $message;
    }

    /**
     * 对socket_write的封装,支持多个包的传播,此函数针对TcpServer的
     *
     * @param socket    socket句柄
     * @param string message    需要发送的消息
     *
     * @return bool 正确返回true,错误返回false
     */
    public static function tcpSocketWrite(&$socket, $message)
    {
        self::clearError();
        $len     = strlen($message);
        $padStr  = str_pad($len, 8, ' ', STR_PAD_RIGHT);
        $message = $padStr.$message;
        $len = $len + 8;
        $n = @socket_write($socket, $message, $len);
        if($n === false) {
            self::$errCode = 10103;
            self::$errMsg  = @socket_strerror(@socket_last_error($socket));
            return false;
        }
        while ($n < $len) {
            $tmp   = substr($message, $n);
            $tmp_n = @socket_write($socket, $tmp, ($len - $n));
            if ($tmp_n === false) {
                self::$errCode = 10103;
                self::$errMsg  = @socket_strerror(@socket_last_error($socket));
                return false;
            }
            $n += $tmp_n;
        }
        if ($n == $len) {
            return true;
        }
        return false;
    }

    /**
     * 处理简单的tcp发包收包,只适合短包的操作,最大10k
     * 实际上受MTU[Maximum Transmission Unit]限制, 大部分网络设备的MTU都是1500, 故每个包不会超过1500 bytes
     *
     * @param string ip     IP地址
     * @param int port      端口
     * @param string cmd    向server发送命令
     * @param int n         错误重试次数
     * @param int timeout_sec      超时秒
     * @param int timeout_usec     超时u秒
     *
     * @return string 错误返回false,正确返回收到的信息
     */
    public static function tcpCmd($ip, $port, $cmd, $n = 2, $timeout_sec = 2, $timeout_usec = 0, $end_char = "")
    {
        self::clearError();
        $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(!$socket) {
            self::$errCode = 10101;
            self::$errMsg  = @socket_strerror(@socket_last_error());
            return false;
        }
        if(!@socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec"=>$timeout_sec, "usec"=>$timeout_usec))){
            self::$errCode = 10106;
            self::$errMsg  = @socket_strerror(@socket_last_error($socket));
            @socket_close($socket);
            return false;
        }
        if(!@socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>$timeout_sec, "usec"=>$timeout_usec))){
            self::$errCode = 10106;
            self::$errMsg  = @socket_strerror(@socket_last_error($socket));
            @socket_close($socket);
            return false;
        }
        $ret = false;
        for ($i = 0; $i < $n; $i++){
            $ret = @socket_connect($socket, $ip, $port);
            if ($ret == true) break;
        }
        if ($ret === false) {
            self::$errCode = 10107;
            self::$errMsg  = @socket_strerror(@socket_last_error($socket));
            @socket_close($socket);
            return false;
        }
        $len = strlen($cmd);
        $n = 0;
        $tmp = $cmd;
        while ($n < $len){
            $ret = @socket_write($socket, $tmp, $len);
            if ($ret === false) {
                self::$errCode = 10103;
                self::$errMsg  = @socket_strerror(@socket_last_error($socket));
                @socket_close($socket);
                return false;
            }
            $n += $ret;
            if ($n < $len) {
                $tmp = substr($tmp, $ret);
            }
        }

        if (empty($end_char))
        {
            $rev = @socket_read($socket, 10240);
            if ($rev == false) {
                self::$errCode = 10102;
                self::$errMsg  = @socket_strerror(@socket_last_error($socket));
            }
        }
        else
        {
            $rev = "";
            do
            {
                if (false === ($out = @socket_read($socket, 8192)))
                {
                    self::$errCode = 10102;
                    self::$errMsg  = @socket_strerror(@socket_last_error($socket));
                    $rev = "";
                    break;
                }

                $rev .= $out;
            } while (0 == substr_count($out, $end_char));
        }

        @socket_close($socket);
        return $rev;
    }

    /**
     * 处理简单的tcp发包收包,只适合短包的操作,最大10k
     * 实际上受MTU[Maximum Transmission Unit]限制, 大部分网络设备的MTU都是1500, 故每个包不会超过1500 bytes
     *
     * @param string ip     IP地址
     * @param int port      端口
     * @param string cmd    向server发送命令
     * @param int n         错误重试次数
     * @param int timeout_sec      超时秒
     * @param int timeout_usec     超时u秒
     *
     * @return string 错误返回false,正确返回收到的信息
     */
    public static function tcpLongCmd($ip, $port, $cmd, $n = 2, $timeout_sec = 2, $timeout_usec = 0)
    {
        self::clearError();
        $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(!$socket) {
            self::$errCode = 10101;
            self::$errMsg  = @socket_strerror(@socket_last_error());
            return false;
        }
        if(!@socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec"=>$timeout_sec, "usec"=>$timeout_usec))){
            self::$errCode = 10106;
            self::$errMsg  = @socket_strerror(@socket_last_error($socket));
            @socket_close($socket);
            return false;
        }
        if(!@socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>$timeout_sec, "usec"=>$timeout_usec))){
            self::$errCode = 10106;
            self::$errMsg  = @socket_strerror(@socket_last_error($socket));
            @socket_close($socket);
            return false;
        }
        $ret = false;
        for ($i = 0; $i < $n; $i++){
            $ret = @socket_connect($socket, $ip, $port);
            if ($ret == true) break;
        }
        if ($ret === false) {
            self::$errCode = 10107;
            self::$errMsg  = @socket_strerror(@socket_last_error($socket));
            @socket_close($socket);
            return false;
        }
        $len = strlen($cmd);
        $n = 0;
        $tmp = $cmd;
        while ($n < $len){
            $ret = @socket_write($socket, $tmp, $len);
            if ($ret == false) {
                self::$errCode = 10103;
                self::$errMsg  = @socket_strerror(@socket_last_error($socket));
                @socket_close($socket);
                return false;
            }
            $n += $ret;
            if ($n < $len) {
                $tmp = substr($tmp, $ret);
            }
        }

        $tmp = true;
        $rev = "";
        while ( !empty( $tmp ) )
        {
            $tmp = socket_read($socket, 10240);
            $rev = $rev.$tmp;
            /*
            if($tmp[strlen($tmp) - 3] == "\r" && $tmp[strlen($tmp) - 2] == "\n")
            {
                break;
            }
            */
        }
        @socket_close($socket);
        return $rev;
    }


    /**
     * 对socket_recvfrom的封装,支持对包的校验,此函数针对udpServer的
     *
     * @param socket    socket句柄
     * @param string message    需要发送的消息
     * @param string ip ip地址
     * @param int port  端口
     *
     * @return bool 正确返回true,错误返回false
     */
    public static function udpSocketRecvFrom(&$socket, $maxLength, &$ip, &$port)
    {
        self::clearError();
        $n = @socket_recvfrom($socket, $message, $maxLength, 0, $ip, $port);
        if ($n === false){
            self::$errCode = 10104;
            self::$errMsg  = @socket_strerror(@socket_last_error());
            return false;
        }
        if (strlen($message) < 8 || $n < 8) {
            self::$errCode = 10104;
            self::$errMsg  = 'bad udp bag';
            return false;
        }
        $len = trim(substr($message, 0, 8));
        if(!is_numeric($len))
        {
            self::$errCode = 10104;
            self::$errMsg  = 'bad udp bag';
            return false;
        }
        if ($len > $maxLength) {
            self::$errCode = 10105;
            self::$errMsg  = 'udp bag too big';
            return false;
        }
        $message = substr($message, 8);
        $n = strlen($message);
        if ($n == $len) {
            return $message;
        }
        self::$errCode = 10104;
        self::$errMsg  = 'bad udp bag';
        return false;
    }

    /**
     * 对socket_sendto的封装,支持对包的校验,此函数针对udpServer的,加8个字符的长度
     *
     * @param socket    socket句柄
     * @param string message    需要发送的消息
     * @param string ip ip地址
     * @param int port  端口
     *
     * @return bool 正确返回true,错误返回false
     */
    public static function udpSocketSendTo(&$socket, $message, $ip, $port)
    {
        self::clearError();
        $len = strlen($message);
        $padStr  = str_pad($len, 8, ' ', STR_PAD_RIGHT);
        $message = $padStr.$message;
        $len += 8;
        $n = @socket_sendto($socket, $message, $len, 0, $ip, $port);
        if ($n === $len) {
            self::$errCode = 10103;
            self::$errMsg  = @socket_strerror(@socket_last_error($socket));
            return true;
        }
        self::$errCode = 10103;
        self::$errMsg ='udp send error';
        return false;
    }
    /**
     * 正确返回接受到的数据,错误返回false
     *
     * @param string ip    ip地址
     * @param int port    端口
     * @param string cmd    命令字符串
     * @param boolean isResponse    是否需要回复
     * @param int timeout    超时时间
     */
    public static function udpCmd($ip, $port, $cmd, $isResponse=true, $timeout = 2, $utmo = 0)
    {
        self::clearError();
        $socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if (!$socket) {
            self::$errCode = 10101;
            self::$errMsg = @socket_strerror(@socket_last_error());
            return false;
        }
        if(!@socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec"=>$timeout, "usec"=>$utmo))){
            self::$errCode = 10106;
            self::$errMsg  = @socket_strerror(@socket_last_error($socket));
            @socket_close($socket);
            return false;
        }
        if(!@socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>$timeout, "usec"=>$utmo))){
            self::$errCode = 10106;
            self::$errMsg  = @socket_strerror(@socket_last_error($socket));
            @socket_close($socket);
            return false;
        }
        $n = @socket_sendto($socket, $cmd, 10240, 0, $ip, $port);
        if ($n == -1) {
            self::$errCode = 10103;
            self::$errMsg = @socket_strerror(@socket_last_error($socket));
            @socket_close($socket);
            return false;
        }
        if ($isResponse === false) {
            @socket_close($socket);
            return true;
        }
        $ret = @socket_recvfrom($socket, $revBuf, 10240, 0, $ip, $port);
        if ($ret == -1) {
            self::$errCode = 10102;
            self::$errMsg = @socket_strerror(@socket_last_error($socket));
            @socket_close($socket);
            return false;
        }
        @socket_close($socket);
        return $revBuf;
    }

    /**
     * 正确返回接受到的数据,错误返回false
     *
     * @param string ip    ip地址
     * @param int port    端口
     * @param string cmd    命令字符串
     * @param boolean isResponse    是否需要回复
     * @param int timeout    超时时间
     */
    public static function udpPHPCmd($ip, $port, $cmd, $isResponse=true, $timeout = 2, $utmo = 0)
    {
        self::clearError();
        $socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if (!$socket) {
            self::$errCode = 10101;
            self::$errMsg  = @socket_strerror(@socket_last_error());
            return false;
        }
        if(!@socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec"=>$timeout, "usec"=>$utmo))){
            self::$errCode = 10106;
            self::$errMsg  = @socket_strerror(@socket_last_error($socket));
            @socket_close($socket);
            return false;
        }
        if(!@socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>$timeout, "usec"=>$utmo))){
            self::$errCode = 10106;
            self::$errMsg  = @socket_strerror(@socket_last_error($socket));
            @socket_close($socket);
            return false;
        }
        $n = self::udpSocketSendTo($socket, $cmd, $ip, $port);
        if ($n == false) {
            @socket_close($socket);
            return false;
        }
        if ($isResponse === false) {
            return true;
        }
        return self::udpSocketRecvFrom($socket, 10240, $ip, $port);
    }

    /**
     * TcpServer的客户端
     *
     * @param string ip      ip地址
     * @param int    port    端口
     * @param string cmd     命令字符串
     * @param int timeout_sec    超时秒
     * @param int timeout_usec   超时u秒
     *
     * @return  string 正确返回接受到的数据,错误返回false
     */
    public static function tcpPHPCmd($ip, $port, $cmd, $n = 2, $timeout_sec = 2, $timeout_usec = 0)
    {
        self::clearError();
        $hostInfo = " to {$ip}:{$port} ";   // 连接的 ip:port 信息
        $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(!$socket) {
            self::$errCode = 10101;
            self::$errMsg  = @socket_strerror(@socket_last_error()) . $hostInfo;
            return false;
        }
        if(!@socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec"=>$timeout_sec, "usec"=>$timeout_usec))){
            self::$errCode = 10106;
            self::$errMsg = @socket_strerror(@socket_last_error($socket)) . $hostInfo;
            @socket_close($socket);
            return false;
        }
        if(!@socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>$timeout_sec, "usec"=>$timeout_usec))){
            self::$errCode = 10106;
            self::$errMsg = @socket_strerror(@socket_last_error($socket)) . $hostInfo;
            @socket_close($socket);
            return false;
        }
        $ret = false;
        for ($i = 0; $i < $n; $i++){
            $ret = @socket_connect($socket, $ip, $port);
            if ($ret == true) break;
        }
        if ($ret === false) {
            self::$errCode = 10107;
            self::$errMsg  = @socket_strerror(@socket_last_error($socket)) . $hostInfo;
            @socket_close($socket);
            return false;
        }
        $ret = self::tcpSocketWrite($socket, $cmd);
        if ( $ret===false ) {
            @socket_close($socket);
            return false;
        }
        $rev = self::tcpSocketRead($socket, 1024000);
        @socket_close($socket);
        return $rev;
    }

    /**
     * 常用打包函数
     *
     * @author  hickwu
     * @param   mix     $data   需要打包的数据
     * @return  string
     */
    public static function wrap($data) {
        $str = serialize($data);

        return $str;
    }

    /**
     * 常用解包函数
     *
     * @author  hickwu
     * @param   string      $str    需要解包的数据
     * @return  mix         解包失败返回 false，成功返回打包数据
     */
    public static function unwrap($str)
    {
        self::clearError();

        $arr = unserialize($str);

        if ($arr === false) {
            self::$errCode = 10613;
            self::$errMsg  = 'unserialize-err-' . serialize($str);
        }

        return $arr;
    }

    // ##################### cURL 请求相关函数 ####################

    /**
     * 使用 cURL 实现 HTTP GET 请求
     *
     * @param       string          $url, 请求地址
     * @param       string          $host, 服务器 host 名, 默认为空(当一台机器有多个虚拟主机时需要指定 host)
     * @param       int             $timeout, 连接超时时间, 默认为2
     *
     * @return      sting/bool      $data, 为返回数据, 失败返回 false
     */
    public static function cURLHTTPGet($url, $timeout = 2, $host = '', $failOnError = true) {
        self::clearError();

        $header = array('Content-transfer-encoding: text');

        if ( !empty($host) ) {
            $header[] = 'Host: ' . $host;
        }

        $curl_handle = curl_init();

        // 连接超时
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, $timeout);
        // 执行超时
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 3);
        // HTTP返回错误时, 函数直接返回错误
        curl_setopt($curl_handle, CURLOPT_FAILONERROR, $failOnError);
        // 允许重定向
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
        // 允许重定向的最大次数
        curl_setopt($curl_handle, CURLOPT_MAXREDIRS, 2);
        // ssl验证host
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, FALSE); 
        // 返回为字符串
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        // 设置HTTP头
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $header);
        // 指定请求地址
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        // 执行请求
        $response = curl_exec($curl_handle);
        if ( $response === false ) {
            self::$errCode = 10615;
            self::$errMsg = 'cURL errno: ' . curl_errno($curl_handle) . '; error: ' . curl_error($curl_handle);
            // 关闭连接
            curl_close($curl_handle);

            return false;
        }

        // 关闭连接
        curl_close($curl_handle);

        return $response;
    }
    /**
     * 使用 cURL 实现 HTTP POST 请求
     *
     * @param       string          $url, 请求地址
     * @param       string          $post_data, 请求的post数据，一般为经过urlencode 和用&处理后的字符串
     * @param       string          $host, 服务器 host 名, 默认为空(当一台机器有多个虚拟主机时需要指定 host)
     * @param       int             $timeout, 连接超时时间, 默认为2
     *
     * @return      sting/bool      $data, 为返回数据, 失败返回 false
     */
    public static function cURLHTTPPost($url, $post_data, $timeout = 2, $host = '', $header_append = array(), $failOnError = true) {
        self::clearError();

        $data_len = strlen($post_data);
        $header = array('Content-transfer-encoding: text', 'Content-Length: ' . $data_len);
        
        if (!empty($header_append)) {
            foreach ($header_append as $v) {
                $header[] = $v;
            }
        }
        
        if ( !empty($host) ) {
            $header[] = 'Host: ' . $host;
        }

        $curl_handle = curl_init();

        // 连接超时
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, $timeout);
        // 执行超时
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 3);
        // HTTP返回错误时, 函数直接返回错误
        curl_setopt($curl_handle, CURLOPT_FAILONERROR, $failOnError);
        // 允许重定向
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
        // 允许重定向的最大次数
        curl_setopt($curl_handle, CURLOPT_MAXREDIRS, 2);
        // ssl验证host
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, FALSE); 
        // 返回为字符串
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        // 设置HTTP头
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $header);
        // 指定请求地址
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        //设置为post方式
        curl_setopt($curl_handle, CURLOPT_POST, TRUE);
        //post 参数
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $post_data);
        // 执行请求
        $response = curl_exec($curl_handle);
        if ( $response === false ) {
            self::$errCode = 10616;
            self::$errMsg = 'cURL errno: ' . curl_errno($curl_handle) . '; error: ' . curl_error($curl_handle);
            // 关闭连接
            curl_close($curl_handle);

            return false;
        }

        // 关闭连接
        curl_close($curl_handle);

        return $response;
    }

    /**
     * 使用  cURL 获取 HTTP 响应头 code
     *
     * @param       string          $url, 请求地址
     * @param       string          $host, 服务器 host 名, 默认为空(当一台机器有多个虚拟主机时需要指定 host)
     * @param       int             $timeout, 连接超时时间, 默认为2
     *
     * @return      sting/bool      $data, 为返回HTTP 响应code, 失败返回 false
     */
    public static function cURLHTTPHead($url, $timeout = 2, $host = '') {
        self::clearError();

        $header = array('Content-transfer-encoding: binary');

        if ( !empty($host) ) {
            $header[] = 'Host: ' . $host;
        }

        $curl_handle = curl_init();

        // 连接超时
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, $timeout);
        // 执行超时
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 3);
        // HTTP返回错误时, 函数直接返回错误
        curl_setopt($curl_handle, CURLOPT_FAILONERROR, true);
        // 允许重定向
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
        // 允许重定向的最大次数
        curl_setopt($curl_handle, CURLOPT_MAXREDIRS, 2);
        // 返回为字符串
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        // remove body
        curl_setopt($curl_handle, CURLOPT_NOBODY, TRUE);
        // 设置HTTP头
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $header);
        // 指定请求地址
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        // 执行请求
        $response = curl_exec($curl_handle);

        if ( $response === false ) {
            self::$errCode = 10615;
            self::$errMsg = 'cURL errno: ' . curl_errno($curl_handle) . '; error: ' . curl_error($curl_handle);
            // 关闭连接
            curl_close($curl_handle);

            return false;
        }
        $httpCode = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

        // 关闭连接
        curl_close($curl_handle);

        return $httpCode;
    }
    /**
     * 通过IP地址定位用户所在的城市
     *
     * @return  sting/bool      $data, 为返回数据, 失败返回 false
     */
    /*
    public static function getCityByIp($ip)
    {
        $long = ip2long(trim($ip));
        if ( $long === false ) {
            return false;
        }
        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        $timeout = array('sec' => 1, 'usec' => 0);

        $rand = rand(0,3);
        $config = Config::getIP('ip2city', $rand);

        socket_setopt($sock, SOL_SOCKET, SO_RCVTIMEO, $timeout);
        socket_setopt($sock, SOL_SOCKET, SO_SNDTIMEO, $timeout);

        socket_sendto($sock, $long, strlen($long), 0, $config['IP'], $config['PORT']);
        $str = '';
        $n = @socket_recvfrom($sock, $str, 64, 0, $config['IP'], $config['PORT']);
        socket_close($sock);

        if ( $n === false || $n < 0 || empty($str)) return false;
        $items = explode('|', $str);
        if (count($items) != 2) return false;

        //$country  = intval($code >> 16 );
        $city = intval($items[0] % 65536);
        return array('prov' => substr($city,0,2),'city' => $city,'net'  => $items[1]);
    }
    */
    /**
     * 通过IP地址定位用户所在的城市（注：使用即通的IP地址库）
     *
     * @return  sting/bool      $data, 为返回数据, 失败返回 false
     */
    public static function getCityByIp($ip, $time=0)
    {
        $serverNum = 2;
        $rand = rand(0,$serverNum-1);
        $config = Config::getIP('ipagent', ($time == 0) ? $rand : $time%$serverNum);
        if (false === $config) {
            self::$errCode = Config::$errCode;
            self::$errMsg = Config::$errMsg;
            return false;
        }
        // 即通省份ID转易迅省份ID
        $_ProvList = array(
            1 => 11,// 北京市
            2 => 12,// 天津市
            3 => 13,// 河北省
            4 => 14,// 山西省
            5 => 15,// 内蒙古
            6 => 32,// 江苏省
            7 => 34,// 安徽省
            8 => 37,// 山东省
            9 => 21,// 辽宁省
            10 => 22,// 吉林省
            11 => 23,// 黑龙江省
            12 => 31,// 上海市
            13 => 33,// 浙江省
            14 => 36,// 江西省
            15 => 35,// 福建省
            16 => 42,// 湖北省
            17 => 43,// 湖南省
            18 => 41,// 河南省
            19 => 44,// 广东省
            20 => 45,// 广西
            21 => 46,// 海南省
            22 => 50,// 重庆市
            23 => 51,// 四川省
            24 => 52,// 贵州省
            25 => 53,// 云南省
            26 => 54,// 西藏
            27 => 61,// 陕西省
            28 => 62,// 甘肃省
            29 => 64,// 宁夏
            30 => 63,// 青海省
            31 => 65,// 新疆
            32 => 81,// 香港
            33 => 82,// 澳门
            34 => 71,// 台湾省
        );

        $ipLookUpAsk = (pack("NNNNA64", 0, 80, ip2long($ip), 1, 'this is iptest for 51buy!'));
        $ipInfo = NetHelper::udpCmd($config['IP'], $config['PORT'], $ipLookUpAsk, true, 0, 100000);
        if (empty($ipInfo)) {
            if ($time < 3) {
                $time++;
                return self::getCityByIp($ip, $time);
            }
            else {
                self::$errCode = 10617;
                self::$errMsg = 'failed to request ip_agent server: ' . $config['IP'];
                return false;
            }
        }

        $retArray = unpack("Ntype/Nlen/A64info/Nflag/Nidflag/Ncountry/Nprovince/Ncity/Ntown/Naddrtype/Nnetworktype/Nbackbone1/Nbackbone2", $ipInfo);
        if (isset($retArray) &&
            is_array($retArray) &&
            isset($retArray['province']) &&
            isset($retArray['city']) &&
            isset($retArray['town']) &&
            isset($retArray['backbone1'])) {
            $prov = isset($_ProvList[$retArray['province']]) ? $_ProvList[$retArray['province']] : 0;
            $city = $prov . '00';
            $city2 = $retArray['city'];
            $town = $retArray['town'];
            $net = $retArray['backbone1'];
            // 站点定位只用到省份，目前只做ip_agent省份编码的转换，城市默认取省会
            // 即通的IP地址库可以定位 省->市->区 三级，但ID定义与网站端及ERP端不一致，如果后续需要精确定位，
            // 需要ERP侧做更细致的ID映射，目前为站点定位只映射了省份ID
            return array('prov' => $prov, 'city' => $city, 'city2' => $city2, 'town' => $town, 'net'  => $net);
        }
        else {
            return false;
        }
    }
}

//End of script

