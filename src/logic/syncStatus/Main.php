<?php


namespace common\components\newTicketCenter\logic\SyncStatus;


use yii2\orm_dcache\Invoice;
use yii2\orm_dcache\OrderInvoice;
use yii2\orm_dcache\RelBusinessOrderInvoice;
use common\models\RelOldInvoice;

class Main {

    public $fromNew = false;

    public $fromOld = false;

    /**
     * @var OrderInvoice
     */
    public $orderInvoice;

    private $data;

    public function run()
    {
        if (empty($this->orderInvoice)){
            $this->exit("发票模型为空");
        } elseif($this->orderInvoice->isSuccess()){
            $this->exit("订单:{$this->getOrderInvoice()->order_sn}已经成功状态了");
        }
        if (empty($this->fromNew) && empty($this->fromOld)){
            $this->fromOldSync();
            if($this->getOrderInvoice()->isSuccess()){
                $this->exit("发票:{$this->orderInvoice->order_sn}同步成功!");
            }
            $this->fromNewSync();
            if($this->getOrderInvoice()->isSuccess()){
                $this->exit("发票:{$this->orderInvoice->order_sn}同步成功!");
            }
            $this->exit("发票:{$this->orderInvoice->order_sn}同步失败!");
        }
        if ($this->fromOld){
            $this->fromOldSync();
        }
        if ($this->fromNew){
            $this->fromNewSync();
        }
    }

    private function exit($msg) {
        echo date("Y-m-d H:i:s") . print_r($msg, true);
        die(1);
    }


    private function getOrderInvoice($fresh = false) {
        if ($fresh){
            $this->orderInvoice = OrderInvoice::findByOrderSn($this->orderInvoice->order_sn);
        }
        return $this->orderInvoice;
    }

    private function fromOldSync() {
        $this->getDataFromOld() && $this->syncData();
    }

    private function fromNewSync() {
        $this->getDataFromNew() && $this->syncData();
    }

    private function getDataFromOld() {
        if (empty($this->data)){
            return false;
        }
    }

    private function syncData() {

        $this->clear();
    }

    private function clear() {
        $this->data = [];
    }

    private function getDataFromNew() {
        if (empty($this->data)){
            return false;
        }
        if (empty($this->getOrderInvoice()->relOldInvoice)){
            $rel = $this->relationFromNew();
            if (!$rel){
                return false;
            }
            if (!$this->saveRelation($rel)){
                return false;
            }
        }
        if (empty($this->getOrderInvoice()->relOldInvoice)){
            $this->log("订单:{$this->getOrderInvoice()->order_sn}获取关联关系失败");
            return false;
        }
        $invoice = Invoice::find()->where(['order_sn' => $this->getOrderInvoice()->relOldInvoice->order_sn, 'invoice_id' => $this->getOrderInvoice()->relOldInvoice->invoice_id])->one();
        if (empty($invoice)){
            $this->log("订单:{$this->getOrderInvoice()->order_sn}在开票中心的发票不存在");
            return false;
        }

        return $this->formatData($invoice);
    }

    private function relationFromNew() {
        $saleId = $this->getOrderInvoice()->sale_id;
        if (!$this->getOrderInvoice()->sale_id || $this->getOrderInvoice()->sale_id == $this->getOrderInvoice()->order_sn){
            $this->log("订单:{$this->getOrderInvoice()->order_sn}第三方订单号为空，无效从新开票中心获取order_sn");
            return false;
        }
        /** @var RelBusinessOrderInvoice $bOrderInvoice */
        $bOrderInvoice = RelBusinessOrderInvoice::find()->where(['sale_id' => $saleId])->andWhere(['client_id' => $this->getOrderInvoice()->client_id])->one();
        if (empty($bOrderInvoice)){
            $this->log("订单:{$this->getOrderInvoice()->order_sn}新开票中心获取为空");
            return false;
        }

        return [$bOrderInvoice->order_sn, $bOrderInvoice->invoice_id];
    }

    private function saveRelation($rel) {
        list($orderSn, $invoiceId) = $rel;
        $m = new RelOldInvoice;
        $m->order_sn = $orderSn;
        $m->invoice_id = $invoiceId;
        $m->old_order_sn = $this->orderInvoice->order_sn;
        return $m->save();
    }

    private function formatData(array $invoice) {
        return [];
    }

    private function log(string $msg) {
        echo date("Y-m-d H:i:s") . print_r($msg, true) . PHP_EOL;
    }
}