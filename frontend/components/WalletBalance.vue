<template>
  <div class="wallet-card">
    <h2 class="title">Saldo disponible</h2>

    <div class="balance">
      <span class="amount">{{ formattedBalance }}</span>
      <span class="currency">{{ currency }}</span>
    </div>

    <p class="subtitle">Actualizado hace {{ lastUpdate }} segundos</p>
  </div>
</template>

<script>
export default {
  name: 'WalletBalance',
  props: {
    balance: {
      type: Number,
      required: true
    },
    currency: {
      type: String,
      default: 'USD'
    },
    lastUpdate: {
      type: Number,
      default: 0
    }
  },
  computed: {
    formattedBalance() {
      return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: this.currency
      }).format(this.balance)
    }
  }
}
</script>

<style scoped>
.wallet-card {
  background: #fff;
  padding: 1.5rem;
  border-radius: 1rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  text-align: center;
}

.title {
  font-size: 1.2rem;
  color: #555;
}

.balance {
  margin: 0.5rem 0;
  font-size: 2rem;
  font-weight: bold;
}

.amount {
  color: #222;
}

.currency {
  margin-left: 0.25rem;
  font-size: 1rem;
  color: #888;
}

.subtitle {
  font-size: 0.9rem;
  color: #aaa;
}
</style>
