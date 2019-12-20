<?php


namespace yii2\orm_dcache\base;


use Yii;
use yii2\orm_dcache\common\Http;
use yii2\orm_dcache\common\ToolsHelper;

abstract class ActiveRecord  extends \yii\db\ActiveRecord {

    abstract public static function getUKey(): array ;

    abstract public  function getMainKeyField(): string ;

    abstract public static function getModelName(): string ;

    abstract public static function getTableName(): string ;

    abstract public static function getDbName(): string ;

    protected  function getMainKeyVal(){
        return $this->getAttribute($this->getMainKeyField());
    }

    /**
     * @return object|\yii\db\Connection|null
     * @throws \yii\base\InvalidConfigException
     */
    public static function getDb() {
        return Yii::$app->get(static::getDbName());
    }

    public static function tableName() {
        return static::getTableName();
    }

    /**
     * @param mixed ...$args
     * @return object|\yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public static function find(...$args) {
        return Yii::createObject(ActiveQuery::className(), [get_called_class()]);
    }

    public function delete()
    {
        $path = '/data_proxy/del_mkv';
        $desc ="delete";
        $url = Yii::$app->params['dcache_data_center']['url'] . $path;
        $data = $this->getDataForSave();
        if (isset($data['value'])) {
            unset($data['value']);
        }
        $jsonData = json_encode($data);
        $ret  = (new Http())->post($url, $jsonData);
        $response = json_decode($ret['data'], true);
        $tab_name = static::tableName();
        if (isset($response['code']) && $response['code'] != 0) {
            $maiKey = $this->getMainKeyField();
            $this->addError(is_array($maiKey) ? current($maiKey) : $maiKey,$response['message'] ?? $tab_name.'数据删除异常');
            return false;
        }
        return true;
    }

    public function save($runValidation = true, $attributeNames = null) {

        if (!$this->validate()){
            return false;
        }
        if ($this->getIsNewRecord()) {
            $path = '/data_proxy/insert_mkv';
            $desc ="insert";
        } else {
            $path = '/data_proxy/update_mkv';
            $desc ="update";
        }

        $data = $this->getDataForSave();
        $url = Yii::$app->params['dcache_data_center']['url'] . $path;
        $jsonData = json_encode($data);
        $ret  = (new Http())->post($url, $jsonData);
        $response = json_decode($ret['data'], true);
        ToolsHelper::writelog(["save data to dCache" => ['url' =>$url, 'input' => $jsonData, 'response' => $response]]);
        $tab_name = static::tableName();
        if (isset($response['code']) && $response['code'] != 0) {
            ToolsHelper::writelog(['同步订单数据异常:' . $desc => 'error', 'tab_name' => $tab_name, 'message' => $response['message'] ?? '数据同步异常']);
            $maiKey = $this->getMainKeyField();
            $this->addError(is_array($maiKey) ? current($maiKey) : $maiKey,$response['message'] ?? $tab_name.'数据同步异常');
            return false;
        }
        ToolsHelper::writelog(['同步订单数据正常:' . $desc => 'ok', 'tab_name' => $tab_name]);
        return true;
    }

    protected function getDataForSave()
    {
        $data = [
            'head' => self::getHeadData(),
        ];
        $mainKeyVal = $this->getMainKeyVal();
        $mainKeyVal = strval($mainKeyVal);
        if ($this->getIsNewRecord()){
            $data['data'] = [
                'mainKey' => $mainKeyVal,
                'mpValue' => $this->getDataForInsert(),
            ];
        } else {
            $data['mainKey'] = $mainKeyVal;
            $data['value'] = $this->getValForUpdate();
            $data['cond'] = $this->getConditionForUpdate();
        }

        return $data;
    }


    private function getValForUpdate(){
        $field = [];
        $fields = $this->toArray();
        foreach ($this->getMAndUKey() as $k => $u) {
            unset($fields[$u]);
        }
        foreach ($fields as $key => $value)
        {
            $field[] = [
                'fieldName' => $key,
                'op'    => 0,
                'value' => $this->formatVal($value, $key),
            ];
        }
        return $field;
    }

    private function getMAndUKey()
    {
        $mainKeyVal = (array)$this->getMainKeyField();
        $uKeys = $this->getUKey();
        return array_merge($mainKeyVal, $uKeys);
    }


    private function getConditionForUpdate()
    {
        $uKeys = $this->getUKey();
        $result = [];
        foreach ($uKeys as $uKey){
            $uVal = $this->getAttribute($uKey);
            $result[] = [
                'fieldName' => $uKey,
                'op'    => 3,
                'value' => $this->formatVal($uVal, $uKey),
            ];
        }
        return $result;
    }

    public static function getHeadData()
    {
        $module = static::getModelName();
        $time = time();
        return [
            'moduleName' => $module,
            'random'     => strval($time),
            'caller'     => ActiveQuery::DCACHE_CALLER,
            'time_stamp' => $time,
            'sig'        => md5($module . $time . ActiveQuery::DCACHE_CALLER . $time . \Yii::$app->params['dcache_data_center']['key']),
        ];
    }

    protected function getDataForInsert()
    {
        $mpValue = [];
        $module = static::getModelName();

        foreach ($this->toArray() as $key => $val) {
            if (in_array($module,['center2invoiceItem'])  && $key == 'id') {
                continue;
            }
            $mpValue[] = [
                'fieldName' => $key,
                'op'        => 0,
                'value'     => $this->formatVal($val, $key),
            ];
        }

        return $mpValue;
    }

    /**
     * @param $val
     * @param $key
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    private function formatVal($val, $key)
    {

        $column = self::getDb()->schema->getTableSchema(self::tableName())->getColumn($key);
        $dbType = $column->type;

        $defaultValue = $column->defaultValue;

        if (in_array($dbType, ['decimal', 'integer', 'float', 'bigint', 'tinyint', 'double', 'smallint'])){
            if ($this->isEmpty($val)){
                return "0";
            }
            return strval($val);
        } elseif ($this->isEmpty($val) && in_array($key, ['preferential_policy_flag', 'drawer_id', 'store_id', 'client_id'])){
            return '0';
        } elseif (in_array($dbType, ['string']) && $val === "0"){
            return "0";
        }

        if ($this->isEmpty($val)){
            return strval($defaultValue);
        }

        return strval($val);
    }

    private function isEmpty($val) {
        return empty($val) || in_array(trim($val), ["''", '""']);
    }
}