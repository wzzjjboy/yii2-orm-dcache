<?php

namespace yii2\orm_dcache;

use yii2\orm_dcache\base\ActiveRecord;
use yii2\orm_dcache\common\OrderTrait;
use Yii;

/**
 * This is the model class for table "o_order_item_0".
 *
 * @property int $id
 * @property string $order_sn 订单编号
 * @property int $line_no 行号，第一个明细行行号为1
 * @property string $source_code 商户自定义编码
 * @property string $unit 单位名称
 * @property string $name 品名
 * @property string $quantity 数量
 * @property string $price 原始单价，8位小数，单位是元
 * @property string $discount 折扣金额，单位是元
 * @property string $total_price 金额，单位是元
 * @property int $created_at
 * @property int $updated_at
 */
class OrderItem extends ActiveRecord
{
    use OrderTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_sn', 'line_no'], 'required'],
            [['id', 'order_sn', 'line_no', 'created_at', 'updated_at'], 'integer'],
            [['quantity', 'price', 'discount', 'total_price'], 'number'],
            [['source_code'], 'string', 'max' => 40],
            [['unit'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 100],
            [['id', 'order_sn'], 'unique', 'targetAttribute' => ['id', 'order_sn']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_sn' => '订单编号',
            'line_no' => '行号，第一个明细行行号为1',
            'source_code' => '商户自定义编码',
            'unit' => '单位名称',
            'name' => '品名',
            'quantity' => '数量',
            'price' => '原始单价，8位小数，单位是元',
            'discount' => '折扣金额，单位是元',
            'total_price' => '金额，单位是元',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function getUKey(): array {
        return ['line_no'];
    }

    public static function getModelName(): string {
        return 'center2orderItem';
    }

    public static function getTableName(): string {
        return 'o_order_item_0';
    }
}
