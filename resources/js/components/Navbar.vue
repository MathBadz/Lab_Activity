<script setup lang="ts">
import { useDarkMode } from '../composables/useDarkMode';
import { useCart } from '../composables/useCart';

const { isDark, toggleDarkMode } = useDarkMode();
const { totalItems } = useCart();

defineProps<{
    searchQuery: string;
}>();

defineEmits<{
    (e: 'toggleCart'): void;
    (e: 'search', event: Event): void;
    (e: 'clearSearch'): void;
}>();
</script>

<template>
    <nav class="sticky top-0 z-50 border-b border-gray-200/50 bg-white/70 shadow-sm backdrop-blur-xl transition-colors duration-300 dark:border-blue-900/30 dark:bg-[#0B0E14]/80">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6">
            <!-- Logo -->
            <div class="flex items-center gap-3 group cursor-pointer">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-[0_0_15px_rgba(37,99,235,0.4)] transition-all duration-300 group-hover:scale-105 group-hover:shadow-[0_0_25px_rgba(37,99,235,0.6)]">
                    <i class="fa-brands fa-playstation text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-black tracking-tight text-gray-900 dark:text-white transition-colors">
                        Game<span class="text-blue-600 dark:text-blue-500">Store</span>
                    </h1>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-blue-300/60">PlayStation Premium</p>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="hidden md:flex flex-1 max-w-xl mx-8 relative group/search">
                <div class="absolute -inset-1 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 opacity-15 blur transition duration-500 group-hover/search:opacity-30"></div>
                <div class="relative w-full flex items-center">
                    <input
                        type="text"
                        placeholder="Search games, accessories..."
                        class="peer w-full rounded-xl border border-gray-200 bg-gray-50/80 py-2.5 pr-10 pl-11 text-gray-900 placeholder-gray-500 shadow-sm backdrop-blur-md transition-all focus:border-blue-500/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500/50 dark:border-white/10 dark:bg-[#151923]/60 dark:text-white dark:focus:bg-[#1A202C] text-sm font-medium tracking-wide z-0"
                        :value="searchQuery"
                        @input="$emit('search', $event)"
                    />
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none z-10 transition-colors peer-focus:text-blue-500"></i>
                    <button
                        v-if="searchQuery"
                        class="absolute right-3 rounded-full bg-gray-200/80 p-1 w-6 h-6 flex items-center justify-center text-gray-500 transition hover:bg-gray-300 hover:text-gray-900 dark:bg-white/10 dark:text-gray-400 dark:hover:bg-white/20 dark:hover:text-white"
                        @click="$emit('clearSearch')"
                    >
                        <i class="fa-solid fa-xmark text-[10px]"></i>
                    </button>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <!-- Dark Mode Toggle -->
                <button
                    class="flex h-11 w-11 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 shadow-sm transition hover:bg-gray-50 hover:text-blue-600 dark:border-gray-800 dark:bg-[#151923] dark:text-gray-400 dark:hover:border-blue-800/50 dark:hover:bg-[#1A202C] dark:hover:text-blue-400"
                    @click="toggleDarkMode"
                    :title="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                >
                    <i class="fa-solid" :class="isDark ? 'fa-sun text-yellow-400' : 'fa-moon'"></i>
                </button>

                <!-- Cart Button -->
                <button
                    class="relative flex h-11 items-center gap-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 text-sm font-bold text-white shadow-[0_4px_20px_rgba(37,99,235,0.3)] transition-all hover:scale-105 hover:shadow-[0_4px_25px_rgba(37,99,235,0.5)] active:scale-[0.98] dark:from-blue-600 dark:to-indigo-500"
                    @click="$emit('toggleCart')"
                >
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="hidden sm:inline">Cart</span>
                    <span
                        v-if="totalItems > 0"
                        class="absolute -top-2 -right-2 flex h-6 min-w-6 items-center justify-center rounded-full border-2 border-white bg-red-500 px-1.5 text-xs font-black text-white shadow-[0_0_10px_rgba(239,68,68,0.6)] dark:border-[#0B0E14]"
                    >
                        {{ totalItems }}
                    </span>
                </button>
            </div>
        </div>
    </nav>
</template>
