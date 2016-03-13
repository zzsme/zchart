<?php
/**
 * Created by PhpStorm.
 * User: zzs
 * Date: 16/3/13
 * Time: 下午10:17
 */

$http = new swoole_http_server('0.0.0.0', 9502);

$http->on('request', function(swoole_http_request $request, swoole_http_response $response) {
    //print_r($request);
    $pathinfo = $request->server['path_info'];
    $filename = __DIR__ . $pathinfo;
    //echo $filename;
    if (is_file($filename)) {
        $ext = pathinfo($pathinfo, PATHINFO_EXTENSION);
        if('php' === $ext){
            ob_start();
            include_once $filename;
            $content = ob_get_contents();
            ob_end_clean();
            $response->end($content);
        } else {
            $mimes = include('mimes.php');
            $response->header("Content-Type", $mimes[$ext]);
            $content = file_get_contents($filename);
            $response->end($content);
        }
    } else {
        $response->status(404);
        $response->end('404 not found');
    }
});

$http->start();