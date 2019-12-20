<?php

namespace yii2\orm_dcache\common;

use Yii;
use ReflectionClass;
use yii\base\InlineAction;
use PHPUnit\Framework\Exception;
use yii\base\InvalidConfigException;
use yii\web\TooManyRequestsHttpException;

class ToolsHelper
{
    /**
     * 获取唯一标识 长度
     * @param int $length
     * @return string
     */
    public static function createUniqueNumber($length = 10)
    {
        $secret = substr(md5(uniqid() . mt_rand(10000, 99999)), 0, $length);//生成20位
        return $secret;
    }

    private static function errorJson($msg = '')
    {
        self::asJsonFail('9000', $msg);
    }

    /*
     * 给定一个日期，获取其本月的第一天
     **/
    public static function getCurMonthFirstDay($date) {
        return date('Y-m-01', strtotime($date));
    }

    /*
     * 给定一个日期，获取其本月的倒数第二天
     **/
    public static function getCurMonthLastDay($date) {
        return date('Y-m-d', strtotime(date('Y-m-01', strtotime($date)) . ' +1 month -1 day'));
    }

    /**
     * 写日志(新方法)
     * 调用方式   writelog2(['msg'=>'说明文字','order_info'=>[]]);
     *
     * @param array $data
     */
    public static function writelog($data = [])
    {
        if (!is_array($data)){
            $data = [$data];
        }
        $date = date('Ym');
        $dir = Yii::$app->basePath . "/runtime/logs/" . $date . '/';
        if (!is_dir($dir)) {
            @mkdir($dir);
        }
        $logName = $dir . date("Ymd") . ".log";
        $date = date("Y-m-d H:i:s") . "=>";
        $debug_backtrace = debug_backtrace();
        $res = [
            'file' => $debug_backtrace[0]['file'],
        ];
        $res = array_merge($res, $data);
        file_put_contents($logName, $date . print_r($res, true) . "\r\n", FILE_APPEND);//var_export改为print_r
    }

    /**
     * 截取多字符长度方法
     *
     * @param string $string 需要处理的字符串
     * @param string $start 截取的开始位置
     * @param string $length 截取的结束位置
     * @param string $encode 设置的字符编码
     *
     * @return string           返回截取的字符串
     */
    public static function sub_string($string, $start = '', $length = '', $encode = 'UTF-8')
    {
        if (!strlen($string)) {
            return $string;
        }
        $start = (int)$start;
        $length = (int)$length ? $length : strlen($string);
        $string = @iconv(mb_detect_encoding($string), $encode, $string);
        return mb_substr($string, $start, $length, $encode);
    }

    /**
     * 将字符串参数变为数组
     *
     * @param $query
     *
     * @return array array (size=10)
     * 'm' => string 'content' (length=7)
     * 'c' => string 'index' (length=5)
     * 'a' => string 'lists' (length=5)
     * 'catid' => string '6' (length=1)
     * 'area' => string '0' (length=1)
     * 'author' => string '0' (length=1)
     * 'h' => string '0' (length=1)
     * 'region' => string '0' (length=1)
     * 's' => string '1' (length=1)
     * 'page' => string '1' (length=1)
     */
    public static function convertUrlQuery($query)
    {
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;
    }

    /**
     * 将参数变为字符串
     *
     * @param $array_query
     *
     * @return string string 'm=content&c=index&a=lists&catid=6&area=0&author=0&h=0®ion=0&s=1&page=1' (length=73)
     */
    public static function getUrlQuery($array_query)
    {
        $tmp = array();
        foreach ($array_query as $k => $param) {
            $tmp[] = $k . '=' . $param;
        }
        $params = implode('&', $tmp);
        return $params;
    }

    /**
     * 返回业务逻辑错误
     * @param $code
     * @param $msg
     * @throws InvalidConfigException
     */
    public static function showError($code, $msg = null)
    {
        $globalErrCode = Yii::$app->params['errorCodes']['global'];
        if (array_key_exists($code, $globalErrCode)) {
            $msg = $globalErrCode[$code];
        } else {
            $code = self::calcErrorCode($code);
        }
        if (is_null($msg)) {
            throw new InvalidConfigException('未配置错码消息');
        }
        self::asJsonFail($code, $msg);
    }


    public static function calcErrorCode($code)
    {
        /** @var InlineAction $action */
        $action = Yii::$app->controller->action;
        static $_reflectionClass = [];
        $key = $code;
        $actionName = $_reflectionClass[$key][2] = lcfirst(substr($action->actionMethod, strlen('action')));
        /** @var ReflectionClass $controllerReflectionClass */
        $controllerReflectionClass = $_reflectionClass[$key][3] = new ReflectionClass ($action->controller);
        $controllerName = $_reflectionClass[$key][4] = lcfirst(strstr($controllerReflectionClass->getShortName(), 'Controller', true));

        $config = Yii::$app->params['errorCodes']['item'];
        $errorConfig = Yii::$app->params['errorCodes']['config'];

        if (!($controllerCode = isset($config[$controllerName]) ? $config[$controllerName][0] : null)) {
            throw new InvalidConfigException('未配置controller错误码');
        }

        if (!($controllerLength = isset($errorConfig['controllerLength']) ? $errorConfig['controllerLength'] : null)) {
            throw new InvalidConfigException('未配置controller的错误码长度');
        }

        $controllerLengthItem = $config[$controllerName][1];
        if (!($methodCode = isset($controllerLengthItem[$_reflectionClass[$key][2]]) ? $controllerLengthItem[$actionName] : null)) {
            throw new InvalidConfigException('未配置method错误码');
        }

        if (!($methodLength = isset($errorConfig['methodLength']) ? $errorConfig['methodLength'] : null)) {
            throw new InvalidConfigException('未配置method错误码长度');
        }

        if (!($fieldLength = isset($errorConfig['fieldLength']) ? $errorConfig['fieldLength'] : null)) {
            throw new InvalidConfigException('未配置field错误码长度');
        }


        return self::buildErrorCode($controllerCode, $controllerLength, $methodCode, $methodLength, $code, $fieldLength);
    }

    private static function buildErrorCode($controllerCode, $controllerLength, $methodCode, $methodLength, $fieldCode, $fieldLength)
    {
        $controllerCode = str_pad($controllerCode, $controllerLength, '0', STR_PAD_LEFT);
        $methodCode = str_pad($methodCode, $methodLength, '0', STR_PAD_LEFT);
        $fieldCode = str_pad($fieldCode, $fieldLength, '0', STR_PAD_LEFT);
        $code = implode([$controllerCode, $methodCode, $fieldCode]);
        return $code;
    }


    private static $hasSend = false;

    private static function asJson($data = [])
    {
        /*if (!self::$hasSend) {
            header('Content-type: application/json');
            self::$hasSend = true;
        }*/
        $json = json_encode($data);
        echo $json;
        exit;
    }

    private static function dataFormat($errCode, $msg = 'ok', $data = [])
    {
        return ['code' => $errCode, 'msg' => $msg, 'data' => $data];
    }

    public static function asJsonSuccess($data = [], $msg = '')
    {
        $data = self::dataFormat(200, $msg, $data);
        self::asJson($data);
    }

    public static function asJsonFail($errCode, $errMsg, $data = [])
    {
        $data = self::dataFormat($errCode, $errMsg, $data);
        self::asJson($data);
    }

    /**
     * 生成随机数 （数字和字母）
     *
     * @param string $pre 前缀
     * @param string $suffix 后缀
     *
     * @return string
     * @since    1.0
     * @author   ELine
     */
    public static function makeRandCode($pre = '', $suffix = '')
    {
        return $pre . base_convert(time() . substr(microtime(), 2, 6) . rand(10, 99), 10, 16) . $suffix;
    }

    /**
     * 将图片编码写入图片文件
     *
     * @param $base64_string
     * @param $output_file
     *
     * @return mixed
     * @since    1.0
     * @author   ELine
     */
    public static function base64ToImg($base64_string, $output_file)
    {
        $ifp = fopen($output_file, "wb");
        fwrite($ifp, base64_decode($base64_string));
        fclose($ifp);
        return ($output_file);
    }

    /**
     * 判断字符串是否为空
     * @param $str
     * @return bool
     */
    public static function toBool($str)
    {
        if (empty($str) || $str == '0' || $str == 'null' || $str == 'false') {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 检测是否是纳税人识别号
     * 1.不严格验证的话，之验证是否是数字和英文字母
     * 2.严格验证的话， 国家税号为 15， 18， 20位
     * @param      $taxpayer        税号
     * @param bool $is_standard 是否严格验证
     * @return int
     * @since    1.0
     * @author   ELine
     */
    public static function isTaxpayerNo($taxpayer, $is_standard = false)
    {
        if ($is_standard) {
            $preg = '/^((\d{6}[0-9A-Z]{9})|([0-9A-Za-z]{2}\d{6}[0-9A-Za-z]{10}))$/';
        } else {
            $preg = '/^[0-9a-zA-Z]+$/';
        }
        if (preg_match_all($preg, $taxpayer, $match)) {
            return $taxpayer;
        } else {
            return false;
        }
    }

    /**
     * 生成请求流水号
     */
    public function generateSerialNo()
    {
        return time() . mt_rand(10000, 99999999);
    }

    /**
     * @param $file
     * @return string
     */
    public static function fileToBase64($file)
    {
        if ($fp = fopen($file, 'r')) {
            $content = fread($fp, filesize($file));
            fclose($fp);
            return base64_encode($content);
        } else {
            throw new Exception('文件打开错误:' . $file);
        }
    }

    /**
     * 获取毫秒级的时间戳
     * @return float
     */
    public static function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }

    /**
     *
     * @param $str
     * @return mixed
     */
    public static function myTrim($str)
    {
        $search = array(" ", "　", "\n", "\r", "\t");
        $replace = array("", "", "", "", "");
        return str_replace($search, $replace, $str);
    }

    /**
     * 空值转换为空串
     * @param $arr
     * @return array|string
     */
    public static function unsetNull($arr)
    {
        if ($arr !== null) {
            if (is_array($arr)) {
                if (!empty($arr)) {
                    foreach ($arr as $key => $value) {
                        if ($value === null) {
                            $arr[$key] = '';
                        } else {
                            $arr[$key] = self::unsetNull($value);
                        }
                    }
                } else {
                    $arr = '';
                }
            } else {
                if ($arr === null) {
                    $arr = '';
                }
            }
        } else {
            $arr = '';
        }
        return $arr;
    }

    /**
     * 压缩文件
     * @param $zipFile
     * @param $fileArr
     * @return mixed
     */
    public static function zipFiles($zipFile, $fileArr)
    {
        $zip = new \ZipArchive;
        $zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);//创建一个空的zip文件
        foreach ($fileArr as $file) {
            $ch = curl_init($file);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            $output = curl_exec($ch);
            $zip->addFromString(basename($file), $output);
        }
        $zip->close();
        return $zipFile;
    }

    /**
     * 下载文件
     * $url如: http://kpserverdev-1251506165.cossh.myqcloud.com/upload/licence/licence_1514947184.png
     * @param $url
     * @return string
     */
    public function downLoadFile($url, $ext = 'png')
    {
        $file_dir = Yii::$app->getRuntimePath() . '/' . 'upload';
        $file_path = $file_dir . '/' . uniqid() . '.' . $ext;
        if (!is_dir($file_dir)){
            mkdir($file_dir);
        }
        $result = (new Http())->get($url);
        if ($result['data']) {
            //存放文件
            file_put_contents($file_path, $result['data']);
        } else {
            $file_path = '';
        }
        return $file_path;
    }

    /**
     * 数字金额转中文大写金额(仅能转换到分）
     * @param $num
     * @return string
     */
    public static function num_to_rmb($num)
    {
        $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角圆拾佰仟万拾佰仟亿";
        //精确到分后面就不要了，所以只留两个小数位
        $num = round($num, 2);
        //将数字转化为整数
        //$num = $num * 100;
        $num = bcmul($num,100);
        if (strlen($num) > 10) {
            return false;
        }
        $i = 0;
        $c = "";
        while (1) {
            if ($i == 0) {
                //获取最后一位数字
                $n = substr($num, strlen($num) - 1, 1);
            } else {
                $n = $num % 10;
            }
            //每次将最后一位数字转化为中文
            $p1 = substr($c1, 3 * $n, 3);
            $p2 = substr($c2, 3 * $i, 3);
            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '圆'))) {
                $c = $p1 . $p2 . $c;
            } else {
                $c = $p1 . $c;
            }
            $i = $i + 1;
            //去掉数字最后一位了
            $num = $num / 10;
            $num = (int)$num;
            //结束循环
            if ($num == 0) {
                break;
            }
        }
        $j = 0;
        $slen = strlen($c);
        while ($j < $slen) {
            //utf8一个汉字相当3个字符
            $m = substr($c, $j, 6);
            //处理数字中很多0的情况,每次循环去掉一个汉字“零”
            if ($m == '零圆' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left = substr($c, 0, $j);
                $right = substr($c, $j + 3);
                $c = $left . $right;
                $j = $j - 3;
                $slen = $slen - 3;
            }
            $j = $j + 3;
        }
        //这个是为了去掉类似23.0中最后一个“零”字
        if (substr($c, strlen($c) - 3, 3) == '零') {
            $c = substr($c, 0, strlen($c) - 3);
        }
        //将处理的汉字加上“整”
        if(!strpos($c,'角') && !strpos($c,'分')){
            $c = $c.'整';
        }

        if (empty($c)) {
            return "零圆";
        } else {
            return $c;
        }
    }

    /**
     * 获取字符串真实长度（中文字符作两个字节计算）
     * @param $string
     * @return bool|float
     */
    public static function getStringRealLength($string)
    {
        if ($string && is_string($string)) {
            return (strlen($string) + mb_strlen($string, "UTF8")) / 2;
        }

        return 0;
    }

    /**
     * 处理简单的post请求数据
     * @param string $key 参数必须要post传的
     * @return array
     */
    public static function getPostData($key = null)
    {
        $params = Yii::$app->request->post();
        if(empty($params)){
            $params = self::parseData(Yii::$app->request->getRawBody());
        }
        return Arr::get($params, $key);
    }

    /**
     * 解决请求的数据
     * @param string $data 支持JSON和XML格式
     * @return array|string
     */
    private static function parseData($data)
    {
        if (empty($data)){
            return $data;
        }

        if (is_array($data)){
            foreach ($data as $k => $v){
                $data[$k] = self::parseData($v);
            }
            return $data;
        }

        if (is_string($data) && $data = trim($data)){
            if((substr($data, 0, 1) === "{") && (substr($data, -1)) === "}"){
                return self::parseData(json_decode($data, true));
            } elseif ((substr($data, 0, 1) === "<") && (substr($data, -1)) === ">") {
                return self::parseData(simplexml_load_string($data, LIBXML_NOCDATA));
            }
        }

        return $data;
    }

    /**
     * 调用开票中心接口的公用方法
     * @param array $params 请求的参数
     * @param $method
     * @return mixed
     * @throws HttpException
     */
    public static function request($params, $method, $appId, $channel, $privateKey, $path = "/serviceinvoice/index")
    {
        $allParams = [
            "app_id" => $appId,
//            "channel" => $channel,
            "charset" => "utf-8",
            "format" => "json",
            "method" => $method,
            "sign_type" => "RSA",
            "timestamp" => date("Y-m-d H:i:s"),
            "version" => "v1.0",
            "biz_content" => json_encode($params),
        ];

        Sign::generateSign($allParams, $privateKey);
        $hostUrl = Yii::$app->params['rsa_cnf']['invoice']['server_host'] . $path;
        $ret = (new Http())->post($hostUrl, $allParams);
        ToolsHelper::writelog(['METHOD'=>__METHOD__,'LINE'=>__LINE__,'请求地址'=>$hostUrl,'请求参数'=>$allParams]);
        if (isset($ret['data']) && $ret['data'] && is_string($ret['data'])) {
            ToolsHelper::writelog(['METHOD'=>__METHOD__,'LINE'=>__LINE__,'请求结果'=>json_decode($ret['data'], true)]);
            return json_decode($ret['data'], true);
        }
    }

    public static function loadAllowance($key)
    {
        $redis = Yii::$app->redis;
        $allowance = $redis->hget($key,'allowance');
        $allowance_updated_at    = $redis->hget($key,'allowance_updated_at');
        return [$allowance, $allowance_updated_at];
    }

    public static function saveAllowance($allowance, $timestamp,$key)
    {
        $redis = Yii::$app->redis;
        $redis->hset($key,'allowance',$allowance);
        $redis->hset($key,'allowance_updated_at',$timestamp);
    }

    /**
     * 访问速率限制
     * @param $limit
     * @param $window
     * @param $key
     * @throws TooManyRequestsHttpException
     */
    public static function checkRateLimit($limit,$window,$key)
    {
        $current = time();

        list($allowance, $timestamp) = self::loadAllowance($key);

        $allowance += (int) (($current - $timestamp) * $limit / $window);
        if ($allowance > $limit) {
            $allowance = $limit;
        }

        if ($allowance < 1) {
            self::saveAllowance(0, $current,$key);
            throw new TooManyRequestsHttpException('您的请求过于频繁');
        }

        self::saveAllowance($allowance - 1, $current,$key);
    }


    /**
     * c++接口签名
     * @param $params
     * @return string
     */
    public static function getCppSignature($uid, $params)
    {
        $params['key'] = \Yii::$app->params['cpp_api_config']['key'];
        $params['uid'] = $uid;

        $str = '';  //待签名字符串
        //先将参数以其参数名的字典序升序进行排序
        ksort($params);
        //遍历排序后的参数数组中的每一个key/value对
        foreach ($params as $k => $v) {
            //为key/value对生成一个key=value格式的字符串，并拼接到待签名字符串后面
            if(is_array($v)){
                continue;
            }
            $str .= "{$k}={$v}&";
        }
        $str = rtrim($str, '&');
        //通过md5算法为签名字符串生成一个md5签名，该签名就是我们要追加的sign参数值
        return strtoupper(md5($str));
    }

    public static function curlDownFile($img_url, $save_path = '', $filename = '') {
        if (trim($img_url) == '') {
            return false;
        }
        if (trim($save_path) == '') {
            $save_path = './';
        }

        //创建保存目录
        if (!file_exists($save_path) && !mkdir($save_path, 0777, true)) {
            return false;
        }
        if (trim($filename) == '') {
            $img_ext = strrchr($img_url, '.');
            $img_exts = array('.gif', '.jpg', '.png', '.mp4');
            if (!in_array($img_ext, $img_exts)) {
                return false;
            }
            $filename = time() . $img_ext;
        }

        // curl下载文件
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $img_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $img = curl_exec($ch);
        curl_close($ch);

        // 保存文件到制定路径
        file_put_contents(rtrim($save_path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename, $img);
        unset($img, $url);
        return true;
    }

    /**
     * 将XML转为array
     */
    public static function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }
}
