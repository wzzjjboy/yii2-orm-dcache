<?php

namespace yii2\orm_dcache;

use yii2\orm_dcache\base\ActiveRecord;
use yii2\orm_dcache\common\RelationTrait;
use Yii;

/**
 * This is the model class for table "b_rel_bussiness_order_invoice_0".
 *
 * @property string $b_order_id
 * @property string $ref_id
 * @property int $client_id
 * @property int $order_sn
 * @property string $invoice_id
 * @property int $invoice_status
 * @property int $created_at
 * @property int $updated_at
 */
class RelBusinessOrderInvoice extends ActiveRecord
{
    use RelationTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['b_order_id', 'order_sn', 'invoice_id', 'client_id'], 'required'],
            [['order_sn', 'invoice_status', 'created_at', 'updated_at', 'client_id'], 'integer'],
            [['b_order_id', 'ref_id'], 'string', 'max' => 50],
            [['invoice_id'], 'string', 'max' => 30],
//            [['order_sn', 'invoice_id', 'b_order_id'], 'unique', 'targetAttribute' => ['order_sn', 'invoice_id', 'b_order_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'b_order_id' => 'B Order ID',
            'order_sn' => 'Order Sn',
            'client_id' => 'Client ID',
            'invoice_id' => 'Invoice ID',
            'invoice_status' => 'Invoice Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'ref_id' => 'ref_id',
        ];
    }

    public static function getModelName(): string {
        return "center2relBussinessOrderInvoice";
    }

    public static function getTableName(): string {
        return "b_rel_bussiness_order_invoice_0";
    }


    public function getMainKeyField(): string {
        return 'b_order_id';
    }
}
