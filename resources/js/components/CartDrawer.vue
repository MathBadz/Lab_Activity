<script setup lang="ts">
import { useCart, formatPrice } from '../composables/useCart';
import CartItemComponent from './CartItem.vue';

const { items, totalItems, totalPrice, incrementQuantity, decrementQuantity, removeFromCart } = useCart();

defineProps<{
    isOpen: boolean;
}>();

defineEmits<{
    close: [];
    checkout: [];
}>();
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="isOpen" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm" @click="$emit('close')"></div>
        </Transition>

        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full"
        >
            <div
                v-if="isOpen"
                class="fixed top-0 right-0 z-50 flex h-full w-full max-w-md flex-col bg-white/95 shadow-2xl backdrop-blur-2xl dark:bg-[#0B0E14]/95 border-l border-gray-200/50 dark:border-blue-900/30 text-gray-900 dark:text-gray-100"
            >
                <div class="flex items-center justify-between border-b border-gray-200/50 px-6 py-5 dark:border-gray-800">
                    <h2 class="flex items-center gap-3 text-xl font-black tracking-tight drop-shadow-sm">
                        <i class="fa-solid fa-cart-shopping bg-gradient-to-br from-blue-600 to-indigo-600 bg-clip-text text-transparent"></i>
                        Cart
                        <span v-if="totalItems > 0" class="rounded-full bg-blue-100/80 px-2.5 py-0.5 text-xs font-black text-blue-700 dark:bg-blue-900/40 dark:text-blue-400 border border-blue-200 dark:border-blue-800/50 shadow-sm">
                            {{ totalItems }}
                        </span>
                    </h2>
                    <button
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100/50 text-gray-500 transition-all hover:bg-gray-200 hover:text-gray-900 dark:bg-gray-800/50 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                        @click="$emit('close')"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-6">
                    <div v-if="items.length === 0" class="flex flex-col items-center justify-center py-20 text-center text-gray-500 dark:text-gray-400">
                        <div class="flex h-24 w-24 items-center justify-center rounded-full bg-gray-100 mb-6 dark:bg-[#151923]">
                            <i class="fa-solid fa-ghost text-5xl opacity-50"></i>
                        </div>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mb-2">Your cart is empty</p>
                        <p class="text-sm">Gear up and add some items.</p>
                        <button
                            class="mt-8 rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold uppercase tracking-wider text-white shadow-[0_0_15px_rgba(37,99,235,0.3)] transition hover:bg-blue-500 hover:shadow-[0_0_25px_rgba(37,99,235,0.5)]"
                            @click="$emit('close')"
                        >
                            Return to Store
                        </button>
                    </div>

                    <div v-else class="space-y-4">
                        <CartItemComponent
                            v-for="item in items"
                            :key="item.product.id"
                            :item="item"
                            @increment="incrementQuantity"
                            @decrement="decrementQuantity"
                            @remove="removeFromCart"
                        />
                    </div>
                </div>

                <div v-if="items.length > 0" class="border-t border-gray-200/50 bg-gray-50/50 px-6 py-6 dark:border-gray-800 dark:bg-gray-900/50 backdrop-blur-md">
                    <div class="mb-5 flex items-end justify-between">
                        <span class="text-sm font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Total</span>
                        <span class="text-3xl font-black tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-300">
                            {{ formatPrice(totalPrice) }}
                        </span>
                    </div>
                    <button
                        class="relative flex w-full items-center justify-center gap-3 overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 py-4 text-sm font-black uppercase tracking-widest text-white shadow-[0_10px_30px_rgba(37,99,235,0.3)] transition-all hover:scale-[1.02] hover:shadow-[0_15px_40px_rgba(37,99,235,0.5)] active:scale-[0.98]"
                        @click="$emit('checkout')"
                    >
                        <div class="absolute inset-0 bg-white/20 translate-y-full transition-transform duration-300 hover:translate-y-0"></div>
                        <i class="fa-solid fa-gamepad relative z-10"></i>
                        <span class="relative z-10">Checkout</span>
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
