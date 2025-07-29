// db.js
const mysql = require('mysql2/promise');
const { DB } = require('./config');

const pool = mysql.createPool({
  host: DB.host,
  user: DB.user,
  password: DB.password,
  database: DB.database,
  waitForConnections: true,
  connectionLimit: 10
});

module.exports = pool;
