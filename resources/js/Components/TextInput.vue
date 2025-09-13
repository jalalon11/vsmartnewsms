<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-lg bg-gray-800 border-gray-700 text-white placeholder-gray-400 shadow-sm focus:border-red-500 focus:ring-red-500 transition-colors duration-200"
        v-model="model"
        ref="input"
    />
</template>
