<?php

namespace yii2\orm_dcache;

use yii2\orm_dcache\base\ActiveRecord;
use yii2\orm_dcache\common\OrderTrait;
use function PHPSTORM_META\elementType;
use Yii;

/**
 * This is the model class for table "i_invoice_item_0".
 *
 * @property int $id 自增ID
 * @property string $invoice_id 发票ID
 * @property string $order_sn 订单编号
 * @property int $line_no 行号，第一个明细行行号为1
 * @property int $line_type 发票行性质。 0正常行、1折扣行、2被折扣行
 * @property string $name 品名
 * @property string $sub_name 品名
 * @property string $quantity 数量
 * @property string $price 原始单价，8位小数，单位是元
 * @property string $discount 折扣金额，单位是元
 * @property string $total_price 金额
 * @property string $code 税分类编码
 * @property string $models 商品规格
 * @property string $unit 单位名称
 * @property string $zero_tax_rate_flag 零税率标识. 
 * @property string $preferential_policy_flag 优惠政策标志　空：不使用，1:使用
 * @property string $vat_sepcial_manager 增值税特殊管理 放到 开票item// itempreferential_policy_flag 优惠政策标识位 1 时必填
 * @property string $tax_prefer_descrip 税收优惠政策内容
 * @property string $tax_rate 税率，范围0-1
 * @property string $tax_amount 税额（精确到2位）
 * @property int $created_at
 * @property int $updated_at
 */
class InvoiceItem extends ActiveRecord
{
    use OrderTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoice_id', 'line_no', 'tax_rate', 'tax_amount'], 'required'],
            [['invoice_id', 'order_sn', 'line_no', 'line_type', 'created_at', 'updated_at'], 'integer'],
            [['discount', 'total_price', 'tax_rate', 'tax_amount'], 'number'],
            [['name', 'sub_name'], 'string', 'max' => 100],
            [['quantity', 'price', 'code', 'models'], 'string', 'max' => 40],
            [['unit'], 'string', 'max' => 20],
            [['zero_tax_rate_flag'], 'string', 'max' => 10],
            [['preferential_policy_flag'], 'string', 'max' => 6],
            [['vat_sepcial_manager'], 'string', 'max' => 200],
            [['tax_prefer_descrip'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增ID',
            'invoice_id' => '发票ID',
            'order_sn' => '订单编号',
            'line_no' => '行号，第一个明细行行号为1',
            'line_type' => '发票行性质。 0正常行、1折扣行、2被折扣行',
            'name' => '品名',
            'sub_name' => '品名',
            'quantity' => '数量',
            'price' => '原始单价，8位小数，单位是元',
            'discount' => '折扣金额，单位是元',
            'total_price' => '金额',
            'code' => '税分类编码',
            'models' => '商品规格',
            'unit' => '单位名称',
            'zero_tax_rate_flag' => '零税率标识. ',
            'preferential_policy_flag' => '优惠政策标志　空：不使用，1:使用',
            'vat_sepcial_manager' => '增值税特殊管理 放到 开票item// itempreferential_policy_flag 优惠政策标识位 1 时必填',
            'tax_prefer_descrip' => '税收优惠政策内容',
            'tax_rate' => '税率，范围0-1',
            'tax_amount' => '税额（精确到2位）',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function getUKey(): array {
        return ['line_no', 'invoice_id'];
    }

    public static function getModelName(): string {
        return 'center2invoiceItem';
    }

    public static function getTableName(): string {
        return 'i_invoice_item_0';
    }
}
