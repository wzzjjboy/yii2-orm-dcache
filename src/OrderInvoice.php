<?php


namespace yii2\orm_dcache;

use common\logic\Data2NewSys;
use common\logic\syncTicket\New2Old;
use common\models\OrderInvoice as OrderInvoiceAlias;
use Yii;


/**
 * Class OrderInvoice
 * @package yii2\orm_dcache
 * @property RelBusinessOrderInvoice $saleInfo
 * @property array $oldCenterInfo
 * @method  $this self::findByOrderSn($order_sn)
 */
class OrderInvoice  extends OrderInvoiceAlias {

    public static function getSaleInfo($client_id, $sale_id)
    {
//        $bOrderInvoice = RelBusinessOrderInvoice::find()->where(['client_id' => $orderInvoice->client_id, 'b_order_id' => $orderInvoice->sale_id])->one();
        $bOrderInvoice = RelBusinessOrderInvoice::find()->where(['client_id' =>$client_id, 'b_order_id' => $sale_id])->one();
        return $bOrderInvoice;
    }

    /**
     * 老系统订单同步至新开票中心
     * @param $orderSn
     * @param $msg
     * @return bool|mixed
     */
    public static function syncOne($orderSn, &$msg)
    {
        if (empty($orderInvoice = \common\models\MiddleGround\OrderInvoice::findByOrderSn($orderSn))) {
            $msg = "订单号不存在:{$orderSn}";
        }
        return (new Data2NewSys())->handlerOne($orderInvoice);
    }

    public function getOldCenterInfo()
    {
        if (!$this->g_unique_id){
            return '';
        }
        $sql = [];
        foreach ($this->getOldCenterOfTab() as $tab) {
            $sql[] = "SELECT post_data FROM gp_relationship_{$tab} WHERE g_unique_id = '{$this->g_unique_id}'";
        }

        $sql =  implode(" union ", $sql);
        $db = Yii::$app->fp_center;
        $res = $db->createCommand($sql)->queryOne();
        if (empty($res)){
            return [];
        }
        return $res;
    }

    private function getOldCenterOfTab()
    {
        $orderChunk = explode("_", $this->order_sn);
        $time = substr($orderChunk[1], 0, 6);
        $y = substr($time, 0, 4);
        $m = substr($time, 4, 2);
        $times = [];
        $unix = mktime(0,0,0, $m, 1, $y);
        if ($time < "201701"){
            $times[] = "201701";
        } elseif ($time == "201701"){
            $times[] = $time;
            $times[] = date("Ym", strtotime("+1 month", $unix));
        } elseif ($time >= "201912"){
            $times[] = "201912";
            if ($time == "201912"){
                $times[] = date("Ym", strtotime("-1 month", $unix));
            }
        } else{
            $times[] = date("Ym", strtotime("-1 month", $unix));
            $times[] = $time;
            $times[] = date("Ym", strtotime("+1 month", $unix));
        }
        return $times;
    }
}