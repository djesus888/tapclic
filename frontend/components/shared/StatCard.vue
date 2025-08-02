<template>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm hover:shadow transition">
    <div class="text-sm text-gray-500">{{ title }}</div>
    <div class="mt-1 text-2xl font-semibold tracking-tight">{{ value }}</div>
    <div class="mt-2 text-xs text-gray-500 flex items-center gap-2">
      <span v-if="trend" :class="trend.startsWith('+') ? 'text-green-600' : 'text-red-600'">
        {{ trend }}
      </span>
      <span class="truncate">{{ hint }}</span>
    </div>
  </div>
</template>

<script>
export default {
  name: "StatCard",
  props: {
    title: { type: String, required: true },
    value: { type: [String, Number], required: true },
    hint:  { type: String, default: "" },
    trend: { type: String, default: "" },
  },
};
</script>
