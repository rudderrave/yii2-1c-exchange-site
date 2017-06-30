<?php


class m170626_103131_init extends \carono\yii2installer\Migration
{
    public function newTables()
    {
        return [
            'group' => [
                'id' => self::primaryKey(),
                'name' => self::string()->comment('Наименование группы'),
                'parent_id' => self::foreignKey('group'),
                'accounting_id' => self::string()->unique(),
                'created_at' => self::dateTime(),
                'updated_at' => self::dateTime(),
                'is_active' => self::boolean()->notNull()->defaultValue(true),
            ],
            'warehouse' => [
                'id' => self::primaryKey(),
                'name' => self::string()->comment('Наименование склада'),
                'accounting_id' => self::string()->unique(),
            ],
            'requisite' => [
                'id' => self::primaryKey(),
                'name' => self::string(),
            ],
            'property' => [
                'id' => self::primaryKey(),
                'name' => self::string(),
                'accounting_id' => self::string()->unique(),
            ],
            'product' => [
                'id' => self::primaryKey(),
                'name' => self::string()->comment('Наименование товара'),
                'article' => self::string()->comment('Артикул'),
                'description' => self::string()->comment('Описание товара'),
                'accounting_id' => self::string()->unique(),
                'group_id' => self::foreignKey('group'),
                'created_at' => self::dateTime(),
                'updated_at' => self::dateTime(),
                'is_active' => self::boolean()->notNull()->defaultValue(true),
                'images' => self::pivot('file_upload', 'image_id'),
                'requisite' => self::pivot('requisite'),
                'properties' => self::pivot('property'),
            ],
            'price_type' => [
                'id' => self::primaryKey(),
                'accounting_id' => self::string()->unique(),
                'name' => self::string()->comment('Наименование типа цены'),
                'currency' => self::string()->comment('Валюта'),
            ],
            'property_value' => [
                'id' => self::primaryKey(),
                'property_id' => self::foreignKey('property'),
                'name' => self::string(),
                'accounting_id' => self::string()->unique(),
            ],
            'price' => [
                'id' => self::primaryKey(),
                'performance' => self::string(),
                'value' => self::decimal(10, 2)->comment('Цена за единицу'),
                'currency' => self::string()->comment('Валюта'),
                'rate' => self::float()->comment('Коэффициент'),
                'type_id' => self::foreignKey('price_type'),
            ],
            'offer' => [
                'id' => self::primaryKey(),
                'name' => self::string(),
                'accounting_id' => self::string()->unique(),
                'product_id' => self::foreignKey('product'),
                'remnant' => self::decimal(10, 3)->comment('Остаток (количество)'),
                'warehouses' => self::pivot('warehouse'),
                'prices' => self::pivot('price'),
                'is_active' => self::boolean()->notNull()->defaultValue(true)
            ],
            'order_status' => [
                'id' => self::primaryKey(),
                'name' => self::string()
            ],
            'order' => [
                'id' => self::primaryKey(),
                'user_id' => self::integer(),
                'created_at' => self::dateTime(),
                'updated_at' => self::dateTime(),
                'status_id' => self::foreignKey('order_status'),
                'sum' => self::decimal(10, 2),
                'offers' => self::pivot('offer')
            ]
        ];
    }

    public function newColumns()
    {
        return [
            'pv_product_requisite' => [
                'value' => self::string(1024),
            ],
            'pv_product_properties' => [
                'property_value_id' => self::foreignKey('property_value'),
            ],
            'pv_order_offers' => [
                'count' => self::decimal(10, 3),
                'sum' => self::decimal(10, 2),
                'price_type_id' => self::foreignKey('price_type')
            ],
        ];
    }

    public function safeUp()
    {
        \carono\yii2installer\InstallController::migrate('@vendor/carono/yii2-file-upload/migrations/m161228_100819_init');
        $this->upNewTables();
        $this->upNewColumns();
        $statuses = [
            ['name' => 'Согласован'],
            ['name' => 'Не согласован'],
            ['name' => 'Закрыт'],
        ];
        foreach ($statuses as $status) {
            $this->insert('order_status', $status);
        }
    }

    public function safeDown()
    {
        $this->downNewColumns();
        $this->downNewTables();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170626_103131_init cannot be reverted.\n";

        return false;
    }
    */
}
