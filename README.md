Yii2 DCACHE ORM
================================

1. 安装

   composer require alan/yii2-orm-dcache:dev-master

2. 配置

   - 添加dcache访问的http地址，本工具基于dcache的http访问接口编写

     建议添加到common\config\main-local.php文件添加

     ```php
     //测试环境配置
     'dcache_data_center' => [
             'url' => 'http://10.154.157.157:10003',
             'key' => 'admvir8359MMjukd~644',
     ],
     //正式环境配置
     'dcache_data_center' => [
         'url' => 'https://kpc.wetax.com.cn',
         'key' => 'o4e0-hpoe875wimmv12@7',
     ],
     ```
     
    - 添加dcache表对应的配置(此配置用于查询表结构，用于ORM操作)

      ```php
      //测试和正式环境只要表结构一致就只需要添加一处即可
      'db_tars_order' => [
          'class' => 'yii\db\Connection',
          'dsn' => 'mysql:host=10.105.1.106;port=3306;dbname=order_0',
          'username' => 'gordon',
          'password' => '4qYAEZ6scVNYPLTWRviT',
          'charset' => 'utf8',
          'enableSchemaCache' => true,
          'schemaCacheDuration' => 86400, // time in seconds
      ],
      'db_tars_relationship' => [
          'class' => 'yii\db\Connection',
          'dsn' => 'mysql:host=10.105.1.106;port=3306;dbname=relationship_0',
          'username' => 'gordon',
          'password' => '4qYAEZ6scVNYPLTWRviT',
          'charset' => 'utf8',
          'enableSchemaCache' => true,
          'schemaCacheDuration' => 86400, // time in seconds
      ],
      ```

      

3. 使用示例

   -  查询

     ```php
     /** 查询一条 **/
     Invoice::find()->where(['order_sn' =>'xxxxx'])->asArray(false)->one()
     /** 查询多条 **/
     Invoice::find()->where(['order_sn' =>'xxxxx'])->asArray(false)->all()
     /** 取出数组 **/   
     Invoice::find()->where(['order_sn' =>'xxxxx'])->asArray(false)->asArray()->all()   
     ```

     

   - 更新

     ```php
     public function actionUpdate($orderSn){
         /** @var Order $order */
         $order = Order::find()->where(['order_sn' => $orderSn])->asArray(false)->one();
         if (empty($order)){
             die("查询失败");
         }
         print_r($order->toArray());
         $order->b_user_id = "1234";
         $saveRsp = $order->save();
         if (!$saveRsp){
             print_r($order->getErrors());
         } else {
             $order = Order::find()->where(['order_sn' => $orderSn])->asArray(true)->one();
             print_r($order);
         }
     }
     ```

     

   - 删除

     ```PHP
     public function actionDelete($orderSn){
         /** @var Order $order */
         $order = Order::find()->where(['order_sn' => $orderSn])->asArray(false)->one();
         if (empty($order)){
             die("查询失败");
         }
         $order->delete();
         $order = Order::find()->where(['order_sn' => $orderSn])->asArray(true)->one();
         print_r($order);
     }
     ```

4. 已知问题
   - dcache不支持limit语句，查询all时要注意取出数据的数量
   - orm不支里面使用带查询的验证器，比如union验证器。建议放在外层自验证验证唯一性