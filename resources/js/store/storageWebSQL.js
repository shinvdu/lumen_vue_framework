/*
panels
* uuid
* store_number
* weight
* created

consumers
* phone
* store_number
* validated
* parsed
* coupons
* panel_id

coupon_images
* ordernumber
* image_id
* coupon_id
* panel_id
* codes
* parsed
* comment
* created

coupons
* efpp_ordernumber
* type
* order_number
* store_number
* status
* parsed
* panel_id
* created

order_items
* ordernumber
* upc
* lot
* nid
* title
* price
* image_id
* coupon_id
* panel_id
* parsed
* validate
* manunal
* comment
* created

orders
* ordernumber
* efpp_ordernumber
* consumer
* user
* store_number
* status
* product_parse_status
* panel_id
* total_price
* check_acvitity
* created

acvitity
* acvitity_id
* title
* checked
* user
* consumer
* efpp_ordernumber
* ordernumber
* coupon_id
* panel_id
* created

stores
* coupons
* created

*/
var WebSQL = {
  dbName: 'acuvueWebSQL',
  dbDisplayName: 'acuvueDB',
  dbVersion: 1,
  dbSize: 40 * 1024 * 1024,
  db: [],
  errorCode: {
    // 错误码
    open: 91001, // 打开数据库失败的错误
    save: 91002, // 保存数据失败的错误
    get: 91003, // 获取数据失败的错误
    delete: 91004, // 删除数据失败的错误
    deleteAll: 91005, // 清空数据库失败的错误
    createDB: 91006 // 清空数据库失败的错误
  },
  stores: {
    consumer: {
      name: 'consumers',
      key: 'id',
      sqls: [
        'CREATE TABLE if not exists "consumers" ( `phone` TEXT NOT NULL, `store_number` TEXT NOT NULL, `validated` INTEGER, `parsed` INTEGER, `coupons` INTEGER, `panel_id` TEXT NOT NULL, `created` INTEGER NOT NULL )',
      ]
    },
    coupon_image: {
      name: 'coupon_images',
      key: 'id',
      sqls: [
        'CREATE TABLE if not exists `coupon_images` ( `ordernumber` TEXT NOT NULL, `image_id` TEXT NOT NULL, `coupon_id` INTEGER NOT NULL, `panel_id` TEXT NOT NULL, `codes` TEXT, `parsed` INTEGER, `comment` TEXT, `created` INTEGER NOT NULL )'
      ]
    },
    coupon: {
      name: 'coupons',
      key: 'id',
      sqls: [
        'CREATE TABLE if not exists `coupons` ( `efpp_ordernumber` TEXT UNIQUE, `type` INTEGER NOT NULL, `order_number` TEXT NOT NULL, `store_number` TEXT NOT NULL, `status` INTEGER, `parsed` INTEGER, `panel_id` TEXT NOT NULL, `created` INTEGER NOT NULL )'
      ]
    },
    order_item: {
      name: 'order_items',
      key: 'id',
      sqls: [
        'CREATE TABLE if not exists `order_items` ( `ordernumber` TEXT NOT NULL, `upc` TEXT, `lot` TEXT, `nid` INTEGER, `title` TEXT, `price` NUMERIC, `image_id` TEXT, `coupon_id` INTEGER NOT NULL, `panel_id` TEXT NOT NULL, `parsed` INTEGER, `validate` INTEGER, `manunal` INTEGER NOT NULL, `comment` INTEGER, `created` INTEGER NOT NULL )'
      ]
    },
    order: {
      name: 'orders',
      key: 'id',
      sqls: [
        'CREATE TABLE if not exists "orders" ( `ordernumber` TEXT NOT NULL, `efpp_ordernumber` TEXT, `consumer` TEXT NOT NULL, `user` TEXT NOT NULL, `store_number` TEXT NOT NULL, `status` INTEGER, `product_parse_status` INTEGER, `panel_id` INTEGER NOT NULL, `total_price` NUMERIC, `check_acvitity` INTEGER, `created` INTEGER NOT NULL, PRIMARY KEY(`ordernumber`) )'
      ]
    },
    panel: {
      name: 'panels',
      key: 'id',
      sqls: [
        'CREATE TABLE if not exists "panels" ( `uuid` TEXT, `store_number` TEXT NOT NULL, `weight` INTEGER, `created` INTEGER NOT NULL, PRIMARY KEY(`uuid`) )'
      ]
    },
    acvitity: {
      name: 'acvitity',
      key: 'id',
      sqls: [
        'CREATE TABLE if not exists `acvitity` ( `acvitity_id` INTEGER NOT NULL, `title` TEXT NOT NULL, `checked` INTEGER, `user` INTEGER NOT NULL, `consumer` TEXT NOT NULL, `efpp_ordernumber` INTEGER, `ordernumber` TEXT NOT NULL, `coupon_id` INTEGER NOT NULL, `panel_id` TEXT NOT NULL, `created` INTEGER NOT NULL )'
      ]
    },
    store: {
      name: 'stores',
      key: 'id',
      sqls: [
        'CREATE TABLE if not exists `stores` ( `store_number` TEXT, `coupons` TEXT, `created` INTEGER )'
      ]
    }
  },
  createStore: async function (callback) {
    try {
      let db = await this.openWebSQL();
      let store = db.transaction(tx => {
        let that = WebSQL;
        for (var t in WebSQL.stores) {
          let sqls = that.stores[t].sqls;
          for (let i = 0; i < sqls.length; i++) {
            tx.executeSql(
              sqls[i],
              [],
              (context, result) => {
                console.log('Success to create table: result');
                console.log(result);
                return true;
              },
              (context, result) => {
                console.log('Failed to create table: result');
                console.log(result);
                return false;
              }
            );
          }
        }
      });
      return store;
    } catch (error) {
      console.log(error);
      return { code: WebSQL.errorCode.createDB, error: error };
    }
  },
  initWebSQL: function () {
    WebSQL.createStore().then((res) => {
      console.log(res);
    }).catch((res) => {
      console.log(res);
    });
  },
  // 打开数据库
  openWebSQL: function () {
    if (WebSQL.db[WebSQL.dbName]) {
      return new Promise((resolve, reject) => {
        resolve(WebSQL.db[WebSQL.dbName]);
      });
    } else {
      return new Promise((resolve, reject) => {
        try {
          WebSQL.db[WebSQL.dbName] = openDatabase(
            WebSQL.dbName,
            WebSQL.dbVersion,
            WebSQL.dbDisplayName,
            WebSQL.dbSize
          );
          resolve(WebSQL.db[WebSQL.dbName]);
        } catch (event) {
          reject({ code: DB.errorCode.open, error: event });
        }
      });
    }
    // WebSQL.openWebSQL().then((res) => {console.log(res)})
  },
  // 查询数据 表名 主键值
  query: async function ($sql, $args) {
    try {
      let db = await this.openWebSQL();
      return new Promise((resolve, reject) => {
        db.transaction(context => {
          context.executeSql($sql, $args,
            (context, results) => {
              var data = [];
              for (let i = 0; i < results.rows.length; i++) {
                data.push(results.rows.item(i));
              }
              resolve(data);
            },
            (context, results) => {
              console.log(results);
              reject({ code: DB.errorCode.get, error: results });
            },
          );
        });
      });
    } catch (error) {
      return Promise.reject({ code: WebSQL.errorCode.get, error: error });
    }
    // WebSQL.query('SELECT * FROM pictures WHERE rowid = ?', [1]).then((res) => {console.log(res)}).catch((res) => {console.log(res)})
  },
  // 插入数据
  insert: async function ($sql, $args) {
    try {
      let db = await this.openWebSQL();
      return new Promise((resolve, reject) => {
        db.transaction(context => {
          context.executeSql($sql, $args,
            (context, results) => {
              resolve(true);
            },
            (context, results) => {
              console.log(results);
              reject({ code: DB.errorCode.save, error: results });
            }
          );
        });
      });
    } catch (error) {
      return Promise.reject({ code: WebSQL.errorCode.save, error: error });
    }
    // WebSQL.insert('INSERT INTO pictures (key, value) VALUES (?, ?)', [1, '菜鸟教程']).then((res) => {console.log(res)})
  },
  // 更新数据
  update: async function ($sql, $args) {
    try {
      let db = await this.openWebSQL();
      return new Promise((resolve, reject) => {
        db.transaction(context => {
          context.executeSql($sql, $args,
            (context, results) => {
              resolve(true);
            },
            (context, results) => {
              console.log(results);
              reject({ code: DB.errorCode.save, error: results });
            }
          );
        });
      });
    } catch (error) {
      return Promise.reject({ code: WebSQL.errorCode.save, error: error });
    }
    // WebSQL.insert('INSERT INTO pictures (key, value) VALUES (?, ?)', [1, '菜鸟教程']).then((res) => {console.log(res)})
  },
  // 删除数据
  delete: async function ($sql, $args) {
    try {
      let db = await this.openWebSQL();
      return new Promise((resolve, reject) => {
        db.transaction(context => {
          context.executeSql($sql, $args,
            (context, results) => {
              resolve(true);
            },
            (context, results) => {
              console.log(results);
              reject({ code: DB.errorCode.delete, error: results });
            }
          );
        });
      });
    } catch (error) {
      return Promise.reject({ code: WebSQL.errorCode.delete, error: error });
    }
    // WebSQL.insert('INSERT INTO pictures (key, value) VALUES (?, ?)', [1, '菜鸟教程']).then((res) => {console.log(res)})
  },
  // 删除数据库
  deleteTables: async function () {
    try {
      let db = await this.openWebSQL();
      return new Promise((resolve, reject) => {
        db.transaction(tx => {
          let that = WebSQL;
          for (var t in WebSQL.stores) {
            let table = that.stores[t].name;
            let sql = 'DROP TABLE ' + table;
            tx.executeSql(
              sql,
              [],
              (context, result) => {
                console.log('Success to delete table: ' + table);
                console.log(result);
                resolve(true);
              },
              (context, result) => {
                console.log('Failed to delete table: ' + table);
                console.log(result);
                reject({ code: WebSQL.errorCode.deleteAll, error: error });
              }
            );
          }
        });
      });
    } catch (error) {
      return Promise.reject({ code: WebSQL.errorCode.deleteAll, error: error });
    }
  },
  // 清空数据
  clear: async function () {
    try {
      let db = await this.openWebSQL();
      return new Promise((resolve, reject) => {
        db.transaction(tx => {
          let that = WebSQL;
          for (var t in WebSQL.stores) {
            let table = that.stores[t].name;
            let sql = 'DELETE FROM ' + table;
            tx.executeSql(
              sql,
              [],
              (context, result) => {
                console.log('Success to truncate table: ' + table);
                console.log(result);
                resolve(true);
              },
              (context, result) => {
                console.log('Failed to truncate table: ' + table);
                console.log(result);
                reject({ code: WebSQL.errorCode.deleteAll, error: result });
              }
            );
          }
        });
      });
    } catch (error) {
      return Promise.reject({ code: WebSQL.errorCode.deleteAll, error: error });
    }
  }
};

window.WebSQL = WebSQL;

export default WebSQL;
