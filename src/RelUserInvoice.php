<?php

namespace yii2\orm_dcache;

use yii2\orm_dcache\base\ActiveRecord;
use yii2\orm_dcache\common\RelationTrait;
use Yii;

/**
 * This is the model class for table "u_rel_user_inovice_0".
 *
 * @property int $user_id 用户id
 * @property int $invoice_id 发票id
 * @property int $order_sn  
 * @property int $invoice_status 开票状态。 0|待开，1|开票中
 * @property int $audit_status 审核状态。 0|待审核，1|审核通过，2|审核不通过
 * @property int $order_status 订单状态，同订单表，冗余字段
 * @property int $source 发票来源：0|开票，1|扫码 2卡包导入
 * @property int $scene 订单场景：1固定码，2动态码。含义与订单表的同名字段相同
 * @property int $created_at
 * @property int $updated_at
 * @property int $del_status 删除状态。 0|未删除，1|已删除
 * @property int $invoice_tag 发票标签，业务方定义数值。 0:其他 1:餐饮 2:酒店住宿 3:出行（交通） 4:办公 5:通信 6:快递
 */
class RelUserInvoice extends ActiveRecord
{
    use RelationTrait;

    const AUDIT_STATUS_WAIT = 0;
    const AUDIT_STATUS_PASS = 1;
    const AUDIT_STATUS_DENY = 2;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'invoice_id', 'order_sn', 'invoice_status', 'updated_at'], 'required'],
            [['user_id', 'invoice_id', 'order_sn', 'invoice_status', 'audit_status', 'order_status', 'source', 'scene', 'created_at', 'updated_at', 'del_status', 'invoice_tag'], 'integer'],
//            [['invoice_id', 'order_sn'], 'unique', 'targetAttribute' => ['invoice_id', 'order_sn']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户id',
            'invoice_id' => '发票id',
            'order_sn' => ' ',
            'invoice_status' => '开票状态。 0|待开，1|开票中',
            'audit_status' => '审核状态。 0|待审核，1|审核通过，2|审核不通过',
            'order_status' => '订单状态，同订单表，冗余字段',
            'source' => '发票来源：0|开票，1|扫码 2卡包导入',
            'scene' => '订单场景：1固定码，2动态码。含义与订单表的同名字段相同',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'del_status' => '删除状态。 0|未删除，1|已删除',
            'invoice_tag' => '发票标签，业务方定义数值。 0:其他 1:餐饮 2:酒店住宿 3:出行（交通） 4:办公 5:通信 6:快递',
        ];
    }

    public function getMainKeyField(): string {
        return 'user_id';
    }

    public static function getModelName(): string {
        return 'center2relUserInvoice';
    }

    public static function getTableName(): string {
        return 'u_rel_user_inovice_0';
    }
}
