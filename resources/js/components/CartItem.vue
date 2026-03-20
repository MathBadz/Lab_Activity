<script setup lang="ts">
import type { CartItem } from '../composables/useCart';
import { formatPrice } from '../composables/useCart';

defineProps<{
    item: CartItem;
}>();

defineEmits<{
    increment: [productId: number];
    decrement: [productId: number];
    remove: [productId: number];
}>();
</script>

<template>
    <div class="group flex items-center gap-4 rounded-2xl border border-gray-200/50 bg-white/50 p-3 shadow-sm backdrop-blur-sm transition-colors hover:bg-white dark:border-gray-800 dark:bg-[#151923]/80 dark:hover:bg-[#151923]">
        <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-xl bg-gray-100 p-2 dark:bg-[#0B0E14]">
            <img
                v-if="item.product.image"
                :src="item.product.image"
                :alt="item.product.name"
                class="h-full w-full object-contain filter drop-shadow-md"
            />
            <i v-else class="fa-solid fa-gamepad text-2xl text-gray-400 dark:text-gray-600"></i>
        </div>

        <div class="min-w-0 flex-1">
            <h4 class="truncate text-sm font-bold tracking-tight text-gray-900 dark:text-gray-100">{{ item.product.name }}</h4>
            <div class="mt-1 flex items-center gap-2">
                <p class="text-sm font-black text-blue-600 dark:text-blue-400">{{ formatPrice(item.product.price) }}</p>
                <span v-if="item.product.platform" class="rounded-md bg-gray-200/50 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                    {{ item.product.platform }}
                </span>
            </div>
        </div>

        <div class="flex flex-col items-end gap-2">
            <button
                class="flex h-7 w-7 items-center justify-center rounded-lg text-red-500 transition hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-900/20"
                @click="$emit('remove', item.product.id)"
                title="Remove item"
            >
                <i class="fa-solid fa-trash-can text-xs"></i>
            </button>
            <div class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-1 py-1 shadow-sm dark:border-gray-700 dark:bg-[#0B0E14]">
                <button
                    class="flex h-6 w-6 items-center justify-center rounded-md text-gray-500 transition hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                    @click="$emit('decrement', item.product.id)"
                >
                    <i class="fa-solid fa-minus text-[10px]"></i>
                </button>
                <span class="w-4 text-center text-xs font-bold text-gray-900 dark:text-white">{{ item.quantity }}</span>
                <button
                    class="flex h-6 w-6 items-center justify-center rounded-md text-gray-500 transition hover:bg-gray-100 hover:text-gray-900 disabled:opacity-30 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                    :disabled="item.quantity >= item.product.stock"
                    @click="$emit('increment', item.product.id)"
                >
                    <i class="fa-solid fa-plus text-[10px]"></i>
                </button>
            </div>
        </div>
    </div>
</template>
