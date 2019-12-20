<?php
/**
 * Created by PhpStorm.
 * User: alan
 * Date: 17-12-14
 * Time: 下午3:15
 */

namespace console\controllers;


use yii\console\Controller;
use yii2\orm_dcache\Invoice;
use yii2\orm_dcache\InvoiceItem;
use yii2\orm_dcache\Order;
use yii2\orm_dcache\OrderItem;
use yii2\orm_dcache\RelBusinessOrderInvoice;
use yii2\orm_dcache\RelBusinessUserInvoice;
use yii2\orm_dcache\RelMerchantInvoice;
use yii2\orm_dcache\RelUserInvoice;

class TestController  extends Controller
{
    public function actionHello()
    {
        print_r("hello");
    }

    public function actionInvoice($orderSn)
    {
       print_r(Invoice::find()->where(['order_sn' =>$orderSn])->asArray(false)->one());
    }

    public function actionInvoiceItem($orderSn)
    {
       print_r(InvoiceItem::find()->where(['order_sn' => $orderSn])->asArray()->all());
    }

    public function actionOrder($orderSn)
    {
       print_r(Order::find()->where(['order_sn' => $orderSn])->asArray()->all());
    }

    public function actionOrderItem($orderSn)
    {
       print_r(OrderItem::find()->where(['order_sn' => $orderSn])->asArray()->all());
    }

    public function actionRelMerchantInvoice($clientId, $orderSn)
    {
       print_r(RelMerchantInvoice::find()->where(['client_id' => $clientId,'order_sn' => $orderSn])->asArray()->all());
    }

    public function actionRelUserInvoice($userId, $orderSn)
    {
       print_r(RelUserInvoice::find()->where(['user_id' => $userId,'order_sn' => $orderSn])->asArray()->all());
    }

    public function actionRelBusinessOrderInvoice($saleId, $orderSn)
    {
       print_r(RelBusinessOrderInvoice::find()->where(['b_order_id' => $saleId,'order_sn' => $orderSn])->asArray()->all());
    }

    public function actionRelBusinessUserInvoice($bUserId, $orderSn)
    {
       print_r(RelBusinessUserInvoice::find()->where(['b_user_id' => $bUserId,'order_sn' => $orderSn])->asArray()->all());
    }

    public function actionUpdate($orderSn)
    {
        /** @var Order $order */
        $order = Order::find()->where(['order_sn' => $orderSn])->asArray(false)->one();
        if (empty($order)){
            die("查询失败");
        }
        print_r($order->toArray());
        $order->b_user_id = "1234";
        $saveRsp = $order->save();
        if (!$saveRsp){
            print_r($order->getErrors());
        } else {
            $order = Order::find()->where(['order_sn' => $orderSn])->asArray(true)->one();
            print_r($order);
        }
    }

    public function actionDelete($orderSn)
    {
        /** @var Order $order */
        $order = Order::find()->where(['order_sn' => $orderSn])->asArray(false)->one();
        if (empty($order)){
            die("查询失败");
        }
        $order->delete();
        $order = Order::find()->where(['order_sn' => $orderSn])->asArray(true)->one();
        print_r($order);
    }
}


