<?php

namespace yii2\orm_dcache;

use yii2\orm_dcache\base\ActiveRecord;
use yii2\orm_dcache\common\OrderTrait;
use Yii;

/**
 * This is the model class for table "i_invoice_0".
 *
 * @property string $invoice_id 发票ID
 * @property string $order_sn 订单编号
 * @property int $state 票据状态 1开票中 2开票失败 3已开票待签章 4签章失败 5开票成功 6待作废 7作废失败 8作废成功 //1开票中,2已开票,待签章,3开票成功,4开票失败,5签章失败
 * @property int $user_id C端用户id
 * @property int $client_id 对应销方商户
 * @property string $sell_name 销方商户名称
 * @property string $sell_tax_code 销方纳税人识别号(税号)
 * @property string $sell_bank_name 销方银行名称(中国银行武汉市宝丰支行)
 * @property string $sell_bank_account 销方银行账号( 802721140108091001)
 * @property string $sell_address 销方地址(武汉市硚口区沿河大道103号)
 * @property string $sell_phone 销方电话(027-89281112)
 * @property string $sell_drawer 销方开票人
 * @property string $sell_reviewer 销方复核人
 * @property string $sell_payee 销方收款人
 * @property string $buy_name 买方商户名称
 * @property string $buy_tax_code 买方纳税人识别号(税号)
 * @property string $buy_bank_name 买方银行名称(中国银行武汉市宝丰支行)
 * @property string $buy_bank_account 买方银行账号(802721140108091001)
 * @property string $buy_address 买方地址(武汉市硚口区沿河大道103号)
 * @property string $buy_phone 买方电话(027-89281112)
 * @property string $buy_email 买方邮箱(10000@qq.com)
 * @property string $amount_no_tax 合计不含税金额,单位是元
 * @property string $amount_tax 合计税额,单位是元
 * @property string $amount_has_tax 合计含税金额(用户支付金额)
 * @property int $ticket_date 时间戳,航信返回的不正规开票日期20170620160222
 * @property string $old_ticket_sn 原发票号码34749752(红票才会有该数据)
 * @property string $old_ticket_code 原发票代码042001600211(红票才会有该数据)
 * @property string $ticket_sn 发票号码 18301259
 * @property string $ticket_code 发票代码 4200163320
 * @property int $is_red 发票类型(0 蓝票 1 红票)
 * @property int $has_red 0 表示未冲红 1表示已冲红
 * @property string $pdf_url 内部pdf url
 * @property string $source_pdf_url 第三方pdf的url地址(51发票地址等)
 * @property string $invoice_type_code 发票种类编码（004:增值税专用发票，007:增值税普通发票，026：增值税电子发票，025：增值税卷式发票, 032：数字发票 ，默认为为026）
 * @property string $fail_msg 失败原因(远程调用失败后保存该信息)
 * @property string $cipher_text 防伪密文
 * @property string $check_code 校验码59634070141208992478
 * @property string $machine_no 机器编号
 * @property string $terminal_no 终端号 航信：分机号|自研百望:盘号|百望云：开票点编码
 * @property string $channel 开票的渠道名称(对应gp_invoice_platform的code)
 * @property int $trade_type 行业分类 1:通信; 2:餐饮; 3:交通; 4:支付平台; 5:票务/旅游; 0:其他
 * @property string $special_invoice_kind 特殊票种标识(成品油:08，其他票种为空)
 * @property int $taxaction_way 征税方式；0：普通征税1：减按计征 2：差额征税
 * @property string $deduction 扣除额,两位小数，单位元
 * @property string $remark 备注
 * @property string $legal_person_name 注册企业法人代表名称，签章使用
 * @property int $source 发票来源：0:开票、1:扫码、2:卡包导入、3:插卡、4:邮箱导入
 * @property string $platform_code 平台编码
 * @property string $platform_name 平台名称
 * @property string $pay_id 支付流水号
 * @property string $tx_hash 区块链发票交易哈希，开票成功后才有
 * @property string $etr_data 预留字段
 * @property string $asset_id 区块链发票资产id，开票成功后才有
 * @property int $created_at 创建时间戳
 * @property int $updated_at 更新时间戳
 */
class Invoice extends ActiveRecord
{
    use OrderTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoice_id', 'sell_name', 'buy_name'], 'required'],
            [['invoice_id', 'order_sn', 'state', 'user_id', 'client_id', 'ticket_date', 'is_red', 'has_red', 'trade_type', 'taxaction_way', 'source', 'created_at', 'updated_at'], 'integer'],
            [['amount_no_tax', 'amount_tax', 'amount_has_tax', 'deduction'], 'number'],
            [['sell_name', 'sell_bank_name', 'sell_address', 'buy_name', 'buy_bank_name', 'buy_address', 'fail_msg', 'cipher_text'], 'string', 'max' => 255],
            [['sell_tax_code', 'sell_phone', 'buy_tax_code', 'buy_phone', 'machine_no', 'channel'], 'string', 'max' => 30],
            [['sell_bank_account', 'buy_bank_account', 'buy_email', 'legal_person_name'], 'string', 'max' => 100],
            [['sell_drawer', 'sell_reviewer', 'sell_payee', 'check_code', 'terminal_no'], 'string', 'max' => 50],
            [['old_ticket_sn', 'old_ticket_code', 'ticket_sn', 'ticket_code', 'special_invoice_kind'], 'string', 'max' => 20],
            [['pdf_url'], 'string', 'max' => 300],
            [['source_pdf_url'], 'string', 'max' => 500],
            [['invoice_type_code'], 'string', 'max' => 5],
            [['remark'], 'string', 'max' => 200],
            [['platform_code', 'platform_name', 'pay_id'], 'string', 'max' => 32],
            [['tx_hash', 'asset_id'], 'string', 'max' => 128],
            [['etr_data'], 'string', 'max' => 512],
            [['invoice_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'invoice_id' => '发票ID',
            'order_sn' => '订单编号',
            'state' => '票据状态 1开票中 2开票失败 3已开票待签章 4签章失败 5开票成功 6待作废 7作废失败 8作废成功 //1开票中,2已开票,待签章,3开票成功,4开票失败,5签章失败',
            'user_id' => 'C端用户id',
            'client_id' => '对应销方商户',
            'sell_name' => '销方商户名称',
            'sell_tax_code' => '销方纳税人识别号(税号)',
            'sell_bank_name' => '销方银行名称(中国银行武汉市宝丰支行)',
            'sell_bank_account' => '销方银行账号( 802721140108091001)',
            'sell_address' => '销方地址(武汉市硚口区沿河大道103号)',
            'sell_phone' => '销方电话(027-89281112)',
            'sell_drawer' => '销方开票人',
            'sell_reviewer' => '销方复核人',
            'sell_payee' => '销方收款人',
            'buy_name' => '买方商户名称',
            'buy_tax_code' => '买方纳税人识别号(税号)',
            'buy_bank_name' => '买方银行名称(中国银行武汉市宝丰支行)',
            'buy_bank_account' => '买方银行账号(802721140108091001)',
            'buy_address' => '买方地址(武汉市硚口区沿河大道103号)',
            'buy_phone' => '买方电话(027-89281112)',
            'buy_email' => '买方邮箱(10000@qq.com)',
            'amount_no_tax' => '合计不含税金额,单位是元',
            'amount_tax' => '合计税额,单位是元',
            'amount_has_tax' => '合计含税金额(用户支付金额)',
            'ticket_date' => '时间戳,航信返回的不正规开票日期20170620160222',
            'old_ticket_sn' => '原发票号码34749752(红票才会有该数据)',
            'old_ticket_code' => '原发票代码042001600211(红票才会有该数据)',
            'ticket_sn' => '发票号码 18301259',
            'ticket_code' => '发票代码 4200163320',
            'is_red' => '发票类型(0 蓝票 1 红票)',
            'has_red' => '0 表示未冲红 1表示已冲红',
            'pdf_url' => '内部pdf url',
            'source_pdf_url' => '第三方pdf的url地址(51发票地址等)',
            'invoice_type_code' => '发票种类编码（004:增值税专用发票，007:增值税普通发票，026：增值税电子发票，025：增值税卷式发票, 032：数字发票 ，默认为为026）',
            'fail_msg' => '失败原因(远程调用失败后保存该信息)',
            'cipher_text' => '防伪密文',
            'check_code' => '校验码59634070141208992478',
            'machine_no' => '机器编号',
            'terminal_no' => '终端号航信：分机号|自研百望:盘号|百望云：开票点编码',
            'channel' => '开票的渠道名称(对应gp_invoice_platform的code)',
            'trade_type' => '行业分类 1:通信; 2:餐饮; 3:交通; 4:支付平台; 5:票务/旅游; 0:其他',
            'special_invoice_kind' => '特殊票种标识(成品油:08，其他票种为空)',
            'taxaction_way' => '征税方式；0：普通征税1：减按计征 2：差额征税',
            'deduction' => '扣除额,两位小数，单位元',
            'remark' => '备注',
            'legal_person_name' => '注册企业法人代表名称，签章使用',
            'source' => '发票来源：0:开票、1:扫码、2:卡包导入、3:插卡、4:邮箱导入',
            'platform_code' => '平台编码',
            'platform_name' => '平台名称',
            'pay_id' => '支付流水号',
            'tx_hash' => '区块链发票交易哈希，开票成功后才有',
            'etr_data' => '预留字段',
            'asset_id' => '区块链发票资产id，开票成功后才有',
            'created_at' => '创建时间戳',
            'updated_at' => '更新时间戳',
        ];
    }

    public static function getUKey(): array {
        return ["invoice_id"];
    }

    public static function getTableName(): string {
        return "i_invoice_0";
    }

    public static function getModelName(): string {
        return 'center2invoice';
    }
}
