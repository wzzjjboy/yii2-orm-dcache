<?php


namespace yii2\orm_dcache\common;

trait OrderTrait {

    public static function getDbName(): string {
        return "db_tars_order";
    }

    public function getMainKeyField(): string {
        return 'order_sn';
    }

//    public static function getUKey(): array {
//        return ["order_sn", "invoice_id"];
//    }
}