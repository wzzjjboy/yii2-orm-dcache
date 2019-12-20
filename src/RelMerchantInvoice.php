<?php

namespace yii2\orm_dcache;

use yii2\orm_dcache\base\ActiveRecord;
use yii2\orm_dcache\common\RelationTrait;
use Yii;

/**
 * This is the model class for table "m_rel_merchant_invoice_0".
 *
 * @property int $invoice_id
 * @property int $order_sn
 * @property string $client_id
 * @property string $store_id
 * @property string $drawer_id
 * @property int $order_status 订单状态，同订单表，冗余字段
 * @property int $invoice_status
 * @property int $pull_status 0-待拉单，1拉单成功，2已取消，3 拉单失败，4系统异常
 * @property int $audit_status 审核状态。0|未审核；1|确认开票；2|拒绝开票
 * @property string $fail_msg 开票或审核信息
 * @property int $audit_user_id
 * @property int $created_at
 * @property int $updated_at
 */
class RelMerchantInvoice extends ActiveRecord
{
    use RelationTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoice_id', 'order_sn'], 'required'],
            [['invoice_id', 'order_sn', 'order_status', 'invoice_status', 'pull_status', 'audit_status', 'audit_user_id', 'created_at', 'updated_at'], 'integer'],
            [['client_id', 'store_id', 'drawer_id'], 'string', 'max' => 45],
            [['fail_msg'], 'string', 'max' => 255],
//            [['invoice_id', 'order_sn'], 'unique', 'targetAttribute' => ['invoice_id', 'order_sn']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'invoice_id' => 'Invoice ID',
            'order_sn' => 'Order Sn',
            'client_id' => 'Client ID',
            'store_id' => 'Store ID',
            'drawer_id' => 'Drawer ID',
            'order_status' => '订单状态，同订单表，冗余字段',
            'invoice_status' => 'Invoice Status',
            'pull_status' => '0-待拉单，1拉单成功，2已取消，3 拉单失败，4系统异常',
            'audit_status' => '审核状态。0|未审核；1|确认开票；2|拒绝开票',
            'fail_msg' => '开票或审核信息',
            'audit_user_id' => 'Audit User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getMainKeyField(): string {
        return 'client_id';
    }

    public static function getModelName(): string {
        return  'center2relMerchantInvoice';
    }

    public static function getTableName(): string {
        return 'm_rel_merchant_invoice_0';
    }
}
