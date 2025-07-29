// server.js
const { createServer } = require("http");
const { Server } = require("socket.io");
const jwt = require("jsonwebtoken");
const pool = require("./db");
const { PORT, JWT_SECRET } = require("./config");

const httpServer = createServer();
const io = new Server(httpServer, {
  cors: {
    origin: "*",
    methods: ["GET", "POST"]
  }
});

io.use((socket, next) => {
  const token = socket.handshake.query.token;
  if (!token) return next(new Error("Token no proporcionado"));

  try {
    const payload = jwt.verify(token, JWT_SECRET);
    socket.user = payload;
    next();
  } catch (err) {
    return next(new Error("Token inválido"));
  }
});

io.on("connection", (socket) => {
  const userId = socket.user.id;
  console.log(`🔌 Cliente conectado: ID usuario ${userId}`);

  socket.join(`user_${userId}`); // Puedes enviar notificaciones directas a esta sala

  socket.on("disconnect", () => {
    console.log(`❌ Usuario ${userId} desconectado`);
  });
});

/** ✅ Método para enviar notificación */
async function sendNotification({ receiver_id, title, message }) {
  const [result] = await pool.execute(
    "INSERT INTO notifications (sender_id, receiver_id, receiver_role, title, message, is_read, created_at) VALUES (?, ?, 'user', ?, ?, 0, NOW())",
    [1, receiver_id, title, message]
  );

  const newNotification = {
    id: result.insertId,
    sender_id: 1,
    receiver_id,
    title,
    message,
    is_read: 0,
    created_at: new Date().toISOString().slice(0, 19).replace("T", " ")
  };

  // Emitir a la sala del usuario
  io.to(`user_${receiver_id}`).emit("new_notification", newNotification);
  console.log("📩 Notificación enviada a", receiver_id);
}

httpServer.listen(PORT, () => {
  console.log(`✅ WebSocket Server corriendo en ws://localhost:${PORT}`);
});

// Exporta la función para usar desde otros scripts si se desea
module.exports = { sendNotification };
