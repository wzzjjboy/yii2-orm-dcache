<?php

namespace yii2\orm_dcache\logic\invoice;
use yii2\orm_dcache\OrderInvoice;
use yii2\orm_dcache\RelBusinessOrderInvoice;
use common\models\RelOldInvoice;
use yii\base\BaseObject;

class FillRelation extends BaseObject {


    public function run($sn, &$msg) {
        $orderInvoice = OrderInvoice::findByOrderSn($sn);
        if (empty($orderInvoice)){
            $msg = "订单{$sn}查询不到!";
            return false;
        }

        if($orderInvoice->relOldInvoice && $orderInvoice->relOldInvoice->order_sn){
            $msg = "订单{$sn}存在关联关系!";
            return false;
        }

        if (empty($orderInvoice)){
            $msg = "订单{$sn}不存在!";
            return false;
        }
        if (empty($orderInvoice->sale_id)){
            $msg = "订单{$sn}第三方ID为空!";
            return false;
        }

        /** @var RelBusinessOrderInvoice $bOrderInvoice */
        $bOrderInvoice = RelBusinessOrderInvoice::find()->where(['client_id' => $orderInvoice->client_id, 'b_order_id' => $orderInvoice->sale_id])->one();
        if (empty($bOrderInvoice)){
            $msg = "订单{$sn}在开票中心为空!";
            return false;
        }
        $model = new RelOldInvoice();
        $model->old_order_sn = $orderInvoice->order_sn;
        $model->order_sn = strval($bOrderInvoice->order_sn);
        $model->is_new_ticket = 1;
        $model->invoice_id = $bOrderInvoice->invoice_id;
        $model->save();
        if ($model->hasErrors()){
            $err = current($model->getFirstErrors());
            $data = json_encode($model->toArray(), JSON_UNESCAPED_UNICODE);
            $msg = "写入订单{$sn}的关联关系失败：{$err}, 数据为:$data";
        }
        return true;
    }
}