<script setup lang="ts">
import type { Product } from '../composables/useCart';
import { formatPrice } from '../composables/useCart';

defineProps<{
    product: Product;
}>();

defineEmits<{
    addToCart: [product: Product];
}>();
</script>

<template>
    <div class="group relative flex flex-col overflow-hidden rounded-2xl border border-gray-200/50 bg-white/50 shadow-sm transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_15px_30px_rgba(37,99,235,0.15)] dark:border-gray-800 dark:bg-[#151923]/80 dark:hover:border-blue-500/30 dark:hover:shadow-[0_15px_30px_rgba(37,99,235,0.1)] backdrop-blur-sm">
        
        <!-- Glow Effect on Hover -->
        <div class="absolute inset-0 z-0 bg-gradient-to-br from-blue-600/0 to-purple-600/0 opacity-0 transition-opacity duration-500 group-hover:from-blue-600/5 group-hover:to-purple-600/10 dark:group-hover:from-blue-500/10 dark:group-hover:to-purple-500/10"></div>
        
        <!-- Product Image Container -->
        <div class="relative z-10 box-border flex aspect-[4/5] w-full items-center justify-center overflow-hidden bg-gray-100 p-6 transition-colors duration-500 group-hover:bg-blue-50/50 dark:bg-[#0B0E14] dark:group-hover:bg-[#0B0E14]/80">
            <!-- Replaceable Image / Placeholder -->
            <img
                v-if="product.image"
                :src="product.image"
                :alt="product.name"
                class="h-full w-full object-contain filter transition-all duration-500 group-hover:scale-110 group-hover:drop-shadow-[0_0_15px_rgba(255,255,255,0.2)]"
            />
            <div
                v-else
                class="flex h-full w-full flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-white/50 text-gray-400 backdrop-blur-sm transition-all duration-300 group-hover:border-blue-400 group-hover:text-blue-500 dark:border-gray-800 dark:bg-gray-900/50 dark:text-gray-600 dark:group-hover:border-blue-500/50 dark:group-hover:text-blue-400"
            >
                <i class="fa-solid fa-gamepad text-5xl mb-3 opacity-50"></i>
                <span class="text-xs font-semibold uppercase tracking-widest text-inherit opacity-70">Image Placeholder</span>
            </div>

            <!-- Badges -->
            <div class="absolute top-3 right-3 z-20 flex flex-col gap-2">
                <span
                    class="rounded-lg px-2.5 py-1 text-[10px] font-black uppercase tracking-wider shadow-md backdrop-blur-md"
                    :class="product.stock > 0
                        ? 'bg-blue-100/90 text-blue-700 dark:bg-blue-600/20 dark:text-blue-400 dark:border dark:border-blue-500/30'
                        : 'bg-red-100/90 text-red-700 dark:bg-red-900/40 dark:text-red-400 dark:border dark:border-red-500/30'"
                >
                    <i class="fa-solid mr-1" :class="product.stock > 0 ? 'fa-bolt text-yellow-500' : 'fa-ban'"></i>
                    {{ product.stock > 0 ? `${product.stock} left` : 'Out of stock' }}
                </span>
            </div>

            <!-- Platform Badge -->
            <div v-if="product.platform" class="absolute top-3 left-3 z-20">
                <span class="rounded-lg bg-gray-900/90 px-2.5 py-1 flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider text-white shadow-md backdrop-blur-md border border-gray-700">
                    <i class="fa-brands fa-playstation text-blue-400"></i>
                    {{ product.platform }}
                </span>
            </div>
        </div>

        <!-- Product Info -->
        <div class="relative z-10 flex flex-1 flex-col p-5">
            <h3 class="line-clamp-2 text-base font-bold leading-tight text-gray-900 transition-colors group-hover:text-blue-600 dark:text-gray-100 dark:group-hover:text-blue-400">
                {{ product.name }}
            </h3>
            
            <p v-if="product.category" class="mt-1 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                {{ product.category }}
            </p>

            <div class="mt-auto pt-4 flex items-end justify-between">
                <p class="text-2xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-400">
                    {{ formatPrice(product.price) }}
                </p>
                <div class="hidden sm:block group-hover:animate-pulse">
                    <i class="fa-solid fa-tag text-gray-300 dark:text-gray-700"></i>
                </div>
            </div>

            <button
                class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl py-3 text-sm font-bold uppercase tracking-wide transition-all duration-300"
                :class="product.stock > 0
                    ? 'bg-gray-900 text-white shadow-[0_4px_15px_rgba(0,0,0,0.1)] hover:bg-blue-600 hover:shadow-[0_4px_20px_rgba(37,99,235,0.4)] active:scale-[0.98] dark:bg-white dark:text-gray-900 dark:hover:bg-blue-500 dark:hover:text-white dark:hover:shadow-[0_0_20px_rgba(59,130,246,0.3)]'
                    : 'cursor-not-allowed bg-gray-100 text-gray-400 dark:bg-[#1A202C] dark:text-gray-600'"
                :disabled="product.stock === 0"
                @click="$emit('addToCart', product)"
            >
                <i class="fa-solid" :class="product.stock > 0 ? 'fa-cart-plus' : 'fa-ban'"></i>
                {{ product.stock > 0 ? 'Add to Cart' : 'Unavailable' }}
            </button>
        </div>
    </div>
</template>
