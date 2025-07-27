// frontend/api/user.js

/**
 * Obtiene el balance de la wallet para un usuario específico desde la API.
 * @param {string} userId - ID del usuario para obtener el balance.
 * @returns {Promise<{balance: number, currency: string}>} - Objeto con balance y moneda.
 * @throws {Error} - Si la llamada a la API falla o la respuesta es inválida.
 */
export async function getWalletBalance(userId) {
  if (!userId) {
    throw new Error('El parámetro userId es obligatorio');
  }

  const API_URL = `https://api.tuapp.com/wallet/${userId}/balance`; // Cambia esta URL por tu endpoint real

  try {
    const response = await fetch(API_URL, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        // Si usas autenticación, agrega el token aquí
        // 'Authorization': `Bearer ${token}`,
      },
    });

    if (!response.ok) {
      throw new Error(`Error al obtener balance: ${response.status} ${response.statusText}`);
    }

    const data = await response.json();

    if (typeof data.balance !== 'number' || typeof data.currency !== 'string') {
      throw new Error('Respuesta inválida de la API');
    }

    return {
      balance: data.balance,
      currency: data.currency,
    };

  } catch (error) {
    // Aquí puedes agregar lógica para logging o mostrar mensajes de error específicos
    console.error('getWalletBalance error:', error);
    throw error;
  }
}
