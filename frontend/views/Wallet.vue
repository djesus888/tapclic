<template>
  <div class="wallet-container">
    <h2>Mi Billetera</h2>

    <div v-if="loading" class="loader">Cargando saldo...</div>

    <div v-else>
      <wallet-balance :balance="wallet.balance" />

      <h3>Historial de movimientos</h3>
      <ul>
        <li v-for="tx in wallet.transactions" :key="tx.id">
          {{ tx.date }} - {{ tx.description }} <strong>{{ tx.amount }} $</strong>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import { getWalletBalance } from "@/api/user";
import WalletBalance from "@/components/WalletBalance.vue";

export default {
  name: "Wallet",
  components: { WalletBalance },
  data() {
    return {
      loading: true,
      wallet: { balance: 0, transactions: [] }
    };
  },
  async mounted() {
    try {
      const { data } = await getWalletBalance();
      this.wallet = data;
    } catch (error) {
      console.error("Error cargando billetera", error);
    } finally {
      this.loading = false;
    }
  }
};
</script>
