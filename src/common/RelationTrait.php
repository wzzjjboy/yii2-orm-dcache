<?php


namespace yii2\orm_dcache\common;

trait RelationTrait {

    public static function getDbName(): string {
        return "db_tars_relationship";
    }

    public static function getUKey(): array {
        return ["order_sn", "invoice_id"];
    }
}