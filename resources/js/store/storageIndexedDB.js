var DB = {
  dbName: 'acuvueClientDB',
  dbVersion: 1,
  indexedDB:
    window.indexedDB ||
    window.webkitindexedDB ||
    window.msIndexedDB ||
    window.mozIndexedDB,
  // 缓存数据库，避免同一个页面重复创建和销毁
  db: {},
  errorCode: {
    // 错误码
    open: 91001, // 打开数据库失败的错误
    save: 91002, // 保存数据失败的错误
    get: 91003, // 获取数据失败的错误
    delete: 91004, // 删除数据失败的错误
    deleteAll: 91005 // 清空数据库失败的错误
  },
  store: {
    picture: {
      name: 'picture',
      key: 'id',
      cursorIndex: [
        { name: 'userPhone', unique: false },
        { name: 'storeNumber', unique: false },
        { name: 'consumerPhone', unique: false },
        { name: 'md5', unique: true }
      ]
    },
    vuex_store: {

      name: 'vuex_store',
      key: 'id',
      cursorIndex: [
        { name: 'storeNumber', unique: true }
      ]
    },
  },
  async initDB () {
    let that = this;
    let request = this.indexedDB.open(DB.dbName, DB.dbVersion);
    request.onerror = function () {
      console.log('打开数据库失败');
    };

    request.onsuccess = function () {
      console.log('打开数据库成功');
    };
    request.onupgradeneeded = function (event) {
      let db = event.target.result;
      for (var t in that.store) {
        if (!db.objectStoreNames.contains(that.store[t].name)) {
          var objectStore = db.createObjectStore(that.store[t].name, {
            keyPath: that.store[t].key,
            autoIncrement: true
          });
          for (let i = 0; i < that.store[t].cursorIndex.length; i++) {
            var element = that.store[t].cursorIndex[i];
            objectStore.createIndex(element.name, element.name, {
              unique: element.unique
            });
          }
        }
      }
    };
  },
  // 打开数据库
  openDB: function () {
    if (DB.db[DB.dbName]) {
      return new Promise((resolve, reject) => {
        resolve(DB.db[DB.dbName]);
      });
    } else {
      return new Promise((resolve, reject) => {
        let request = this.indexedDB.open(DB.dbName, DB.dbVersion);
        request.onerror = function (event) {
          console.log('IndexedDB数据库打开错误，' + event);
          reject({ code: DB.errorCode.open, error: event });
        };
        request.onsuccess = function (event) {
          console.log('IndexedDB数据库打开成功');
          DB.db[DB.dbName] = event.target.result;
          resolve(event.target.result);
        };
      });
    }
  },
  // 删除数据库
  deleteDB: function () {
    return new Promise((resolve, reject) => {
      let deleteQuest = this.indexedDB.deleteDatabase(DB.dbName);
      deleteQuest.onerror = function () {
        console.log('IndexedDB数据库删除错误，' + event);
        reject(false);
      };
      deleteQuest.onsuccess = function () {
        console.log('IndexedDB数据库删除成功.');
        resolve(true);
      };
    });
  },
  // 关闭数据库
  closeDB: function () {
    if (DB.db[DB.dbName]) {
      return new Promise(resolve => {
        let closeQuest = DB.db[DB.dbName].close();
        closeQuest.onerror = function () {
          resolve(false);
        };
        closeQuest.onsuccess = function () {
          resolve(true);
        };
      });
    } else {
      return new Promise(resolve => {
        resolve(true);
      });
    }
  },
  // 添加数据，add添加新值
  insert: async function (table, data) {
    try {
      let db = await this.openDB();
      let request = db
        .transaction(table.name, 'readwrite')
        .objectStore(table.name)
        .add(data);

      return new Promise((resolve, reject) => {
        request.onerror = function (e) {
          reject({ code: DB.errorCode.save, error: e });
          console.log(e);
        };
        request.onsuccess = function (e) {
          resolve(true);
        };
      });
    } catch (error) {
      console.log(error);
      return Promise.reject({ code: DB.errorCode.save, error: error });
    }
  },
  // 更新
  upsert: async function (table, data) {
    try {
      let db = await this.openDB();
      let request = db
        .transaction(table.name, 'readwrite')
        .objectStore(table.name)
        .put(data);
      return new Promise((resolve, reject) => {
        request.onerror = function (e) {
          reject({ code: DB.errorCode.save, error: e });
          console.log(e);
        };
        request.onsuccess = function (e) {
          resolve(true);
        };
      });
    } catch (error) {
      console.log(error);
      return Promise.resolve({ code: DB.errorCode.save, error: error });
    }
  },
  // 删除数据
  delete: async function (table, keyValue) {
    try {
      let db = await this.openDB();
      let request = db
        .transaction(table.name, 'readwrite')
        .objectStore(table.name)
        .delete(keyValue);
      return new Promise(resolve => {
        request.onerror = function () {
          resolve(false);
        };
        request.onsuccess = function () {
          resolve(true);
        };
      });
    } catch (error) {
      return Promise.reject(false);
    }
  },
  deleteByIndex: async function (table, keyValue, indexCursor) {
    try {
      let db = await this.openDB();
      let store = db
       .transaction(table.name, 'readwrite')
        .objectStore(table.name);
      // let request = store.
      let keyRng = IDBKeyRange.only(keyValue);
      let request = store.index(indexCursor).openCursor(keyRng);
      return new Promise((resolve, reject) => {
        request.onerror = function (event) {
          reject(false);
        };
        request.onsuccess = function (event) {
          // resolve(event.target.result);
          var cursor = event.target.result;
          if (cursor) {
            // console.log(cursor);
            cursor.delete();
            cursor.continue();
          }
          resolve(true);
        };
      });
    } catch (error) {
      console.log(error);
      return Promise.reject(false);
    }
  },
  // 清空数据
  clear: function (table) {
    this.openDB().then((db) => {
      let store = db.transaction(table.name, 'readwrite').objectStore(table.name);
      store.clear();
    }).catch((er) => {
      console.log(er);
    })
  },
  // 查询数据 表名 主键值
  get: async function (table, keyValue) {
    try {
      let db = await this.openDB();
      let store = db
       .transaction(table.name, 'readonly')
        .objectStore(table.name);
      let request = store.get(keyValue);
      return new Promise((resolve, reject) => {
        request.onerror = function (event) {
          reject({ code: DB.errorCode.get, error: event });
        };
        request.onsuccess = function (event) {
          resolve(event.target.result);
        };
      });
    } catch (error) {
      return Promise.reject({ code: DB.errorCode.get, error: error });
    }
  },
  // 查询数据 表名 索引值 索引 key  没有value key为key 而不是索引
  getByIndex: async function (table, keyValue, indexCursor) {
    try {
      let db = await this.openDB();
      let store = db
       .transaction(table.name, 'readonly')
        .objectStore(table.name);
      // let request = store.
      let keyRng = IDBKeyRange.only(keyValue);
      let request = store.index(indexCursor).openCursor(keyRng);
      let data = [];
      return new Promise(resolve => {
        request.onerror = function (event) {
          reject({ code: DB.errorCode.get, error: event });
        };
        request.onsuccess = function (event) {
          // resolve(event.target.result);
          var cursor = event.target.result;
          if (cursor) {
            data.push(cursor.value);
            cursor.continue();
          }
          resolve(data);
        };
      });
    } catch (error) {
      return Promise.reject({ code: DB.errorCode.get, error: error });
    }
  },
  tellExistByIndex: async function (table, keyValue, indexCursor) {
    try {
      let db = await this.openDB();
      let store = db
       .transaction(table.name, 'readonly')
        .objectStore(table.name);
      // let request = store.
      let keyRng = IDBKeyRange.only(keyValue);
      let request = store.index(indexCursor).openCursor(keyRng);
      return new Promise((resolve, reject) => {
        request.onerror = function (event) {
          reject(false);
        };
        request.onsuccess = function (event) {
          // resolve(event.target.result);
          var cursor = event.target.result;
          if (cursor) {
            resolve(true);
          } else {
            reject(false);
          }
        };
      });
    } catch (error) {
      return Promise.reject(false);
    }
  },
  // 通过索引游标操作数据, callback中要有游标移动方式
  handleDataByIndex: async function (table, keyRange, indexCursor) {
    try {
      let db = await this.openDB();
      let store = db
       .transaction(table.name, 'readonly')
        .objectStore(table.name);
      let request = store.index(indexCursor).openCursor(keyRange);
      let data = [];
      return new Promise(resolve => {
        request.onerror = function (event) {
          reject({ code: DB.errorCode.get, error: event });
        };
        request.onsuccess = function (event) {
          // resolve(event.target.result);
          var cursor = event.target.result;
          if (cursor) {
            // console.log(cursor.value);
            data.push(cursor.value);
            cursor.continue();
          }
          resolve(data);
        };
      });
    } catch (error) {
      return Promise.reject({ code: DB.errorCode.get, error: error });
    }
  },
  // Read all records.
  getAll: async function (table) {
    try {
      let db = await this.openDB();
      let store = db
       .transaction(table.name, 'readonly')
        .objectStore(table.name);
      let request = store.openCursor();
      let data = [];
      return new Promise(resolve => {
        request.onerror = function (event) {
          reject({ code: DB.errorCode.get, error: event });
        };
        request.onsuccess = function (event) {
          // resolve(event.target.result);
          var cursor = event.target.result;
          if (cursor) {
            console.log(cursor.value);
            data.push(cursor.value);
            cursor.continue();
          }
        };
        resolve(data);
      });
    } catch (error) {
      return Promise.reject({ code: DB.errorCode.get, error: error });
    }
  },
  // Read all records.
  getAllKeys: async function (table) {
    try {
      let db = await this.openDB();
      let store = db
       .transaction(table.name, 'readonly')
        .objectStore(table.name);
      let request = store.getAllKeys();
      return new Promise(resolve => {
        request.onerror = function (event) {
          reject({ code: DB.errorCode.get, error: event });
        };
        request.onsuccess = function (event) {
          resolve(event.target.result);
        };
      });
    } catch (error) {
      return Promise.reject({ code: DB.errorCode.get, error: error });
    }
  }
};

window.DB = DB;

export default DB;
