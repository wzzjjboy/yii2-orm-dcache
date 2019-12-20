<?php

namespace yii2\orm_dcache;

use yii2\orm_dcache\base\ActiveRecord;
use Yii;

/**
 * This is the model class for table "t_rel_tax_invoice_0".
 *
 * @property int $invoice_id
 * @property int $order_sn
 * @property string $ticket_sn
 * @property string $ticket_code 发票代码
 * @property int $ticket_date 开票日期
 * @property int $created_at
 */
class RelTaxInvoice extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoice_id', 'order_sn', 'ticket_sn', 'ticket_code'], 'required'],
            [['invoice_id', 'order_sn', 'ticket_date', 'created_at'], 'integer'],
            [['ticket_sn', 'ticket_code'], 'string', 'max' => 20],
            [['invoice_id', 'order_sn'], 'unique', 'targetAttribute' => ['invoice_id', 'order_sn']],
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
            'ticket_sn' => 'Ticket Sn',
            'ticket_code' => '发票代码',
            'ticket_date' => '开票日期',
            'created_at' => 'Created At',
        ];
    }

    public static function getUKey(): array {
        return ['ticket_code'];
    }

    public function getMainKeyField(): string {
        return 'ticket_sn';
    }

    public static function getModelName(): string {
        return 'center2relTaxInvoice';
    }

    public static function getTableName(): string {
        return 't_rel_tax_invoice_0';
    }

    public static function getDbName(): string {
        return 'db_tars_relationship';
    }
}
