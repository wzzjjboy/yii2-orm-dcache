<?php

namespace yii2\orm_dcache;


use yii2\orm_dcache\base\ActiveRecord;
use yii2\orm_dcache\common\OrderTrait;

/**
 * This is the model class for table "o_order_0".
 *
 * @property string $order_sn 订单编号
 * @property string $sale_id 小票号(商户生成)
 * @property int $provider_id 服务商id(冗余)
 * @property int $user_id 用户id(冗余)
 * @property string $b_user_id 第三方用户ID
 * @property int $client_id 商户id
 * @property string $name 订单名称
 * @property int $time 订单生成日期
 * @property int $type 类型：1支付，2开票，3支付+开票
 * @property string $amount 订单总金额
 * @property string $remark 商户订单备注信息(json存储)
 * @property int $source 订单开具来源(1:外部开票平台开票;2:内部开票平台开票
 * @property int $scene 订单场景：1固定码，2动态码
 * @property int $status 订单状态：1有效，2无效
 * @property string $fail_msg 订单失败原因
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Order extends ActiveRecord
{
    use OrderTrait;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_sn'], 'required'],
            [['order_sn', 'provider_id', 'user_id', 'client_id', 'time', 'type', 'source', 'scene', 'status', 'created_at', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['sale_id'], 'string', 'max' => 50],
            [['b_user_id', 'name'], 'string', 'max' => 30],
            [['remark'], 'string', 'max' => 1024],
            [['fail_msg'], 'string', 'max' => 150],
//            [['order_sn'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_sn' => '订单编号',
            'sale_id' => '小票号(商户生成)',
            'provider_id' => '服务商id(冗余)',
            'user_id' => '用户id(冗余)',
            'b_user_id' => '第三方用户ID',
            'client_id' => '商户id',
            'name' => '订单名称',
            'time' => '订单生成日期',
            'type' => '类型：1支付，2开票，3支付+开票',
            'amount' => '订单总金额',
            'remark' => '商户订单备注信息(json存储)',
            'source' => '订单开具来源(1:外部开票平台开票;2:内部开票平台开票',
            'scene' => '订单场景：1固定码，2动态码',
            'status' => '订单状态：1有效，2无效',
            'fail_msg' => '订单失败原因',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public static function getUKey(): array {
        return ['client_id'];
    }

    public static function getModelName(): string {
        return 'center2order';
    }

    public static function getTableName(): string {
        return 'o_order_0';
    }
}
