<?php

namespace yii2\orm_dcache;

use yii2\orm_dcache\base\ActiveRecord;
use yii2\orm_dcache\common\OrderTrait;
use Yii;

/**
 * This is the model class for table "i_invoice_deliver_0".
 *
 * @property int $order_sn 订单号
 * @property int $invoice_id
 * @property string $appkey 开放平台appkey
 * @property string $b_order_id 第三方订单号
 * @property int $client_id 商户id
 * @property string $notify_url 推送地址
 * @property int $last_notify_time 最后推送时间
 * @property int $is_auth 是否授权。 0-未授权， 1-已授权
 * @property string $card_id 卡包模板
 * @property string $card_code 插卡成功标识
 * @property string $card_msg 插卡信息
 * @property string $email 邮箱
 * @property int $email_time 邮件推送时间
 * @property string $mobile 手机号码
 * @property int $mobile_time
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class InvoiceDeliver extends ActiveRecord
{
    use OrderTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_sn', 'invoice_id'], 'required'],
            [['order_sn', 'invoice_id', 'client_id', 'last_notify_time', 'is_auth', 'email_time', 'mobile_time', 'created_at', 'updated_at'], 'integer'],
            [['appkey'], 'string', 'max' => 60],
            [['b_order_id', 'card_id', 'card_code'], 'string', 'max' => 50],
            [['notify_url', 'card_msg'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 100],
            [['mobile'], 'string', 'max' => 25],
            [['order_sn', 'invoice_id'], 'unique', 'targetAttribute' => ['order_sn', 'invoice_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_sn' => '订单号',
            'invoice_id' => 'Invoice ID',
            'appkey' => '开放平台appkey',
            'b_order_id' => '第三方订单号',
            'client_id' => '商户id',
            'notify_url' => '推送地址',
            'last_notify_time' => '最后推送时间',
            'is_auth' => '是否授权。 0-未授权， 1-已授权',
            'card_id' => '卡包模板',
            'card_code' => '插卡成功标识',
            'card_msg' => '插卡信息',
            'email' => '邮箱',
            'email_time' => '邮件推送时间',
            'mobile' => '手机号码',
            'mobile_time' => 'Mobile Time',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public static function getUKey(): array {
        return ['invoice_id'];
    }

    public static function getModelName(): string {
        return 'center2invoiceDeliver';
    }

    public static function getTableName(): string {
        return 'i_invoice_deliver_0';
    }
}
