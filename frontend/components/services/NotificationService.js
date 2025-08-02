// src/services/NotificationService.js

import { io } from "socket.io-client";
import { useNotificationStore } from "@/stores/notificationStore";

let socket = null;
let isConnected = false;

/**
 * Inicializa la conexión WebSocket para notificaciones en tiempo real.
 * @param {string} token JWT del usuario autenticado.
 */
export function initWebSocket(token) {
  if (!token || typeof token !== "string") {
    console.warn("❌ Token WebSocket no válido");
    return;
  }

  if (socket && isConnected) return;

  socket = io("ws://localhost:4000", {
    query: { token },
    transports: ["websocket"],
    reconnection: true,
    reconnectionAttempts: Infinity,
    reconnectionDelay: 1000,
    reconnectionDelayMax: 10000
  });

  socket.on("connect", () => {
    isConnected = true;
    console.log("✅ Conectado al servidor WebSocket");
  });

  socket.on("disconnect", reason => {
    isConnected = false;
    console.warn("🔌 WebSocket desconectado:", reason);
  });

  socket.on("connect_error", err => {
    isConnected = false;
    console.error("❌ Error al conectar WebSocket:", err.message);
  });

  // 🎯 Eventos personalizados
  socket.on("new_notification", handleNewNotification);
  socket.on("chat_message", handleChatMessage);
  socket.on("order_update", handleOrderUpdate);
}

/**
 * Cierra la conexión WebSocket manualmente.
 */
export function closeWebSocket() {
  if (socket) {
    socket.disconnect();
    socket = null;
    isConnected = false;
    console.log("🔒 WebSocket desconectado manualmente");
  }
}

// 🧠 Métodos internos para manejar los eventos
function handleNewNotification(payload) {
  const store = useNotificationStore();
  console.log("📩 Nueva notificación:", payload);
  store.addNotification(payload);

  // 🔔 Aquí podrías disparar un toast si usas vue-toastification
  // toast.info(`🔔 ${payload.title}: ${payload.message}`);
}

function handleChatMessage(payload) {
  const store = useNotificationStore();
  console.log("💬 Nuevo mensaje de chat:", payload);
  store.addChatMessage(payload);

  // 🔔 Ejemplo con toast:
  // toast.success(`💬 Nuevo mensaje de ${payload.sender_name}`);
}

function handleOrderUpdate(payload) {
  const store = useNotificationStore();
  console.log("📦 Actualización de pedido:", payload);
  store.addOrderUpdate(payload);

  // 🔔 Ejemplo:
  // toast(`📦 Pedido #${payload.order_id} actualizado`);
}
