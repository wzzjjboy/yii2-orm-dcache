<?php

namespace yii2\orm_dcache;

use yii2\orm_dcache\base\ActiveRecord;
use yii2\orm_dcache\common\RelationTrait;
use Yii;

/**
 * This is the model class for table "b_rel_bussiness_user_invoice_0".
 *
 * @property string $b_user_id 第三方用户标识
 * @property string $order_sn
 * @property string $invoice_id
 * @property int $client_id 商户id
 * @property string $ref_id 冗余字段，关联ID，如开发者id
 * @property int $invoice_status
 * @property int $created_at
 * @property int $updated_at
 */
class RelBusinessUserInvoice extends ActiveRecord
{
    use RelationTrait;



    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['b_user_id', 'order_sn', 'invoice_id'], 'required'],
            [['client_id', 'invoice_status', 'created_at', 'updated_at'], 'integer'],
            [['b_user_id', 'ref_id'], 'string', 'max' => 50],
            [['order_sn', 'invoice_id'], 'string', 'max' => 30],
//            [['order_sn', 'invoice_id'], 'unique', 'targetAttribute' => ['order_sn', 'invoice_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'b_user_id' => '第三方用户标识',
            'order_sn' => 'Order Sn',
            'invoice_id' => 'Invoice ID',
            'client_id' => '商户id',
            'ref_id' => '冗余字段，关联ID，如开发者id',
            'invoice_status' => 'Invoice Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getMainKeyField(): string {
        return 'b_user_id';
    }

    public static function getModelName(): string {
        return 'center2relBussinessUserInvoice';
    }

    public static function getTableName(): string {
        return 'b_rel_bussiness_user_invoice_0';
    }
}
