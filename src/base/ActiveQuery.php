<?php


namespace yii2\orm_dcache\base;


use Yii;
use yii\db\Exception;
use yii2\orm_dcache\common\Http;

class ActiveQuery extends \yii\db\ActiveQuery {

    public $mainKey;

    const DCACHE_CALLER = "php_developer";

    /**
     * @param null $db
     * @return array|\yii\db\ActiveRecord[]
     * @throws Exception
     */
    public function all($db = null) {
        return $this->request(0, 100);
    }

    /**
     * @param int $batchSize
     * @param null $db
     * @return object|\yii\db\BatchQueryResult
     * @throws \yii\base\InvalidConfigException
     */
    public function batch($batchSize = 100, $db = null) {
        return Yii::createObject([
            'class'     => DataIterator::class,
            'batchSize' => $batchSize,
            'query'     => $this,
        ]);
    }

    /**
     * @param null $db
     * @return array|mixed|\yii\db\ActiveRecord|null
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function one($db = null) {
        $rsp = $this->request(0, 1);
        return $rsp[0] ?? null;
    }

    /**
     * @param int $start
     * @param int $end
     * @return array
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function request($start = 0, $end = 100) {
        $mk = trim(Yii::createObject($this->modelClass)->getMainKeyField());
        $where = $this->where;
        if (!($this->mainKey = $this->checkMainKey($where, $mk))) {
            throw new Exception("empty main key for :" . get_class($this));
        } else {
            unset($where[$mk]);
        }
        $condition = $this->getCondition($start, $end);
        $header = $this->getHeader();
        $data = [
            'head'    => $header,
            'field'   => '*',
            'mainKey' => strval($this->mainKey),
            'cond'    => $condition,
        ];
        $url = \Yii::$app->params['dcache_data_center']['url'].'/data_proxy/get_mkv';
        $jsonData = json_encode($data);
        $ret = (new Http())->post($url, $jsonData);
        $data = json_decode($ret['data'], true);
        $rows = $one = [];
        if (isset($data['code']) && $data['code'] == 0) {
            if (!isset($data['data'])) {
                return [];
            }
            foreach ($data['data'] as $index => $row) {
                $one = [];
                foreach ($row['field'] as $content) {
                    $one[$content['name']] = $content['value'];
                }
                $rows[] = $one;
            }
            $models = $this->populate($rows);
            return $models;
        } else {
            throw new \Exception("query from decache exception : {$data['message']}");
        }
    }

    private function getCondition($start = 0, $end = 100) {
        $where = $this->where;
        if (empty($where)) {
            return null;
        }
        $result = [];

        foreach ($where as $uKey => $value) {
            $result[] = [
                'fieldName' => $uKey,
                'op'        => 3,
                'value'     => $value == 0 ? "0" : strval($value),
            ];
        }
        $result[] = [
            'fieldName' => 'limit',
            'op'        => 9,
            'value'     => "{$start}:{$end}",
        ];
        return $result;
    }

    private function getHeader() {
        $model = $this->modelClass;
        $module = $model::getModelName();
        $time = time();
        return [
            'moduleName' => $module,
            'random'     => strval($time),
            'caller'     => self::DCACHE_CALLER,
            'time_stamp' => $time,
            'sig'        => md5($module . $time . self::DCACHE_CALLER . $time . \Yii::$app->params['dcache_data_center']['key']),
        ];
    }

    private function checkMainKey($where, string $mk) {
        foreach ($where as $ck => $v) {
            if (is_array($v)) {
                if ($k = $this->checkMainKey($v, $mk)) {
                    return $k;
                }
            }
            if ($mk && false !== stripos($ck, $mk)) {
                return $v;
            }
        }
        return false;
    }
}