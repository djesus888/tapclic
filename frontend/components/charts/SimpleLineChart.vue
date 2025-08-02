<template>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <div class="mb-3 text-sm text-gray-600">{{ title }}</div>
    <canvas ref="canvasEl"></canvas>
  </div>
</template>

<script>
import { Chart, LineController, LineElement, PointElement, LinearScale, CategoryScale, Tooltip, Legend } from "chart.js";
Chart.register(LineController, LineElement, PointElement, LinearScale, CategoryScale, Tooltip, Legend);

export default {
  name: "SimpleLineChart",
  props: {
    title: { type: String, default: "" },
    labels: { type: Array, required: true },
    data: { type: Array, required: true },
  },
  mounted() {
    this.chart = new Chart(this.$refs.canvasEl, {
      type: "line",
      data: {
        labels: this.labels,
        datasets: [
          {
            label: "Serie",
            data: this.data,
            fill: false,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        aspectRatio: 2,
        plugins: { legend: { display: false } },
        scales: { x: { grid: { display: false } }, y: { grid: { color: "#eee" } } }
      },
    });
  },
  beforeUnmount() {
    if (this.chart) this.chart.destroy();
  },
};
</script>
