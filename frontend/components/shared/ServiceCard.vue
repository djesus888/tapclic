<template>
  <button
    class="w-full text-left rounded-2xl border border-gray-200 bg-white p-4 shadow-sm hover:shadow-md transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
    @click="$emit('select')"
  >
    <div class="flex items-start justify-between">
      <h3 class="text-lg font-semibold">{{ title }}</h3>
      <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">{{ eta }}</span>
    </div>
    <p class="mt-2 text-sm text-gray-600">{{ description }}</p>
    <div class="mt-3 text-sm font-medium text-gray-800">{{ priceText }}</div>
    <div class="mt-4">
      <span class="inline-flex items-center gap-2 text-indigo-600 text-sm font-medium">
        Solicitar ahora
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12.293 17.293a1 1 0 0 0 1.414 1.414l6-6a1 1 0 0 0 0-1.414l-6-6a1 1 0 0 0-1.414 1.414L16.586 11H4a1 1 0 1 0 0 2h12.586l-4.293 4.293Z"/></svg>
      </span>
    </div>
  </button>
</template>

<script>
export default {
  name: "ServiceCard",
  props: {
    title: { type: String, required: true },
    description: { type: String, required: true },
    eta: { type: String, default: "24-48h" },
    priceText: { type: String, default: "" },
  },
  emits: ["select"],
};
</script>
