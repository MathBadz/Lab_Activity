<script setup lang="ts">
import { ref, computed } from 'vue';
import { useCart, formatPrice } from '../composables/useCart';

const { items, totalPrice, removeFromCart, updateProductStock } = useCart();

const isOpen = ref(false);
const isProcessing = ref(false);
const checkoutResults = ref<Array<{ productName: string; success: boolean; message: string }>>([]);
const checkoutComplete = ref(false);
const fullName = ref('');
const fullNameError = ref('');
const location = ref('');
const locationError = ref('');

const cartItems = computed(() =>
    items.value.map((item) => ({
        ...item,
        subtotal: item.product.price * item.quantity,
    })),
);

function open() {
    isOpen.value = true;
    checkoutResults.value = [];
    checkoutComplete.value = false;
    fullNameError.value = '';
    locationError.value = '';
}

function close() {
    isOpen.value = false;
}

async function processCheckout() {
    if (!fullName.value.trim()) {
        fullNameError.value = 'Full name is required before checkout.';
        return;
    }

    if (!location.value.trim()) {
        fullNameError.value = '';
        locationError.value = 'Delivery location is required before checkout.';
        return;
    }

    fullNameError.value = '';
    locationError.value = '';
    isProcessing.value = true;
    checkoutResults.value = [];

    const snapshot = [...items.value];
    const successfulProductIds: number[] = [];
    const checkoutFullName = fullName.value.trim();
    const checkoutLocation = location.value.trim();

    for (const item of snapshot) {
        try {
            const response = await fetch('/api/order', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: item.product.id,
                    quantity: item.quantity,
                    full_name: checkoutFullName,
                    location: checkoutLocation,
                }),
            });

            const payload = await response.json().catch(() => ({} as Record<string, unknown>));

            if (response.ok) {
                const remainingStock = Number(payload.remainingStock ?? item.product.stock);
                const message = String(payload.message ?? 'Order successful');

                checkoutResults.value.push({
                    productName: item.product.name,
                    success: true,
                    message: `${message} - ${remainingStock} left in stock`,
                });

                updateProductStock(item.product.id, remainingStock);
                successfulProductIds.push(item.product.id);
            } else {
                const message = String(payload.error ?? payload.message ?? 'Order failed');

                checkoutResults.value.push({
                    productName: item.product.name,
                    success: false,
                    message,
                });
            }
        } catch {
            checkoutResults.value.push({
                productName: item.product.name,
                success: false,
                message: 'Network error while placing order',
            });
        }
    }

    checkoutComplete.value = true;
    isProcessing.value = false;

    for (const productId of successfulProductIds) {
        removeFromCart(productId);
    }

    if (checkoutResults.value.every((r) => r.success)) {
        fullName.value = '';
        location.value = '';
    }
}

defineExpose({ open, close });
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
            <div v-if="isOpen" class="fixed inset-0 z-[60] bg-black/70 backdrop-blur-md" @click="close"></div>
        </Transition>

        <Transition
            enter-active-class="transition duration-400 ease-[cubic-bezier(0.175,0.885,0.32,1.275)]"
            enter-from-class="opacity-0 scale-90 translate-y-8"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-4"
        >
            <div v-if="isOpen" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                <div class="relative w-full max-w-lg overflow-hidden rounded-3xl bg-white/95 shadow-2xl backdrop-blur-2xl dark:bg-[#0B0E14]/95 dark:border border-blue-900/40" @click.stop>
                    
                    <!-- Decorative glow -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/10 rounded-full blur-[80px] pointer-events-none"></div>
                    
                    <div class="relative flex items-center justify-between border-b border-gray-200/50 px-6 py-5 text-gray-900 dark:border-gray-800 dark:text-white">
                        <h2 class="flex items-center gap-3 text-xl font-black tracking-tight">
                            <i class="fa-brands fa-playstation text-blue-600 dark:text-blue-500"></i>
                            Checkout Securely
                        </h2>
                        <button
                            class="flex h-8 w-8 items-center justify-center rounded-xl bg-gray-100 text-gray-400 transition hover:bg-gray-200 hover:text-gray-900 dark:bg-gray-800 dark:text-gray-500 dark:hover:bg-gray-700 dark:hover:text-white"
                            @click="close"
                        >
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <!-- Order Summary -->
                    <div v-if="!checkoutComplete" class="relative max-h-[60vh] overflow-y-auto px-6 py-4 custom-scrollbar">
                        <div class="space-y-4">
                            <div
                                v-for="item in cartItems"
                                :key="item.product.id"
                                class="flex items-center gap-4 rounded-2xl border border-gray-200/50 bg-gray-50/50 p-3 dark:border-gray-800 dark:bg-[#151923]"
                            >
                                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-white dark:bg-[#0B0E14] shadow-sm">
                                    <img
                                        v-if="item.product.image"
                                        :src="item.product.image"
                                        :alt="item.product.name"
                                        class="h-8 w-8 object-contain"
                                    />
                                    <i v-else class="fa-solid fa-gamepad text-gray-400"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold tracking-tight text-gray-900 dark:text-gray-100">{{ item.product.name }}</p>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                        Qty: {{ item.quantity }} &times; {{ formatPrice(item.product.price) }}
                                    </p>
                                </div>
                                <p class="text-sm font-black text-blue-600 dark:text-blue-400">{{ formatPrice(item.subtotal) }}</p>
                            </div>
                        </div>

                        <div class="mt-6 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-50 p-5 shadow-inner dark:from-[#151923] dark:to-[#0B0E14] border border-gray-200/50 dark:border-gray-800">
                            <label for="checkout-full-name" class="mb-2 block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Full Name
                            </label>
                            <input
                                id="checkout-full-name"
                                v-model.trim="fullName"
                                type="text"
                                placeholder="Enter full name"
                                class="mb-2 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-gray-700 dark:bg-[#0F1420] dark:text-gray-100 dark:focus:border-blue-400 dark:focus:ring-blue-500/20"
                            />
                            <p v-if="fullNameError" class="mb-3 text-xs font-semibold text-red-500 dark:text-red-400">
                                {{ fullNameError }}
                            </p>

                            <label for="checkout-location" class="mb-2 block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Delivery Location
                            </label>
                            <input
                                id="checkout-location"
                                v-model.trim="location"
                                type="text"
                                placeholder="Enter delivery address or location"
                                class="mb-2 w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-gray-700 dark:bg-[#0F1420] dark:text-gray-100 dark:focus:border-blue-400 dark:focus:ring-blue-500/20"
                            />
                            <p v-if="locationError" class="mb-3 text-xs font-semibold text-red-500 dark:text-red-400">
                                {{ locationError }}
                            </p>

                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Subtotal</span>
                                <span class="font-bold text-gray-900 dark:text-gray-300">{{ formatPrice(totalPrice) }}</span>
                            </div>
                            <div class="border-t border-gray-200/50 pt-3 dark:border-gray-800">
                                <div class="flex items-end justify-between">
                                    <span class="text-base font-black tracking-tight text-gray-900 dark:text-white">Amount Due</span>
                                    <span class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-indigo-400">
                                        {{ formatPrice(totalPrice) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Checkout Results -->
                    <div v-else class="relative max-h-[60vh] overflow-y-auto px-6 py-8 text-center">
                        <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                            <i class="fa-solid fa-check text-4xl text-green-500 shadow-green-500/50 drop-shadow-lg"></i>
                        </div>
                        <h3 class="mb-6 text-2xl font-black tracking-tight text-gray-900 dark:text-white">Order Successful!</h3>
                        
                        <div class="space-y-3 text-left">
                            <div
                                v-for="(result, idx) in checkoutResults"
                                :key="idx"
                                class="flex items-start gap-3 rounded-xl border px-4 py-3 shadow-sm bg-gray-50/50 dark:bg-[#151923]"
                                :class="result.success
                                    ? 'border-green-200 dark:border-green-800/50'
                                    : 'border-red-200 dark:border-red-800/50'"
                            >
                                <i
                                    class="fa-solid mt-0.5"
                                    :class="result.success
                                        ? 'fa-circle-check text-green-500'
                                        : 'fa-circle-exclamation text-red-500'"
                                ></i>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                        {{ result.productName }}
                                    </p>
                                    <p class="mt-0.5 text-xs font-medium" :class="result.success ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400'">
                                        {{ result.message }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative border-t border-gray-200/50 bg-gray-50/80 px-6 py-5 dark:border-gray-800 dark:bg-gray-900/80">
                        <button
                            v-if="!checkoutComplete"
                            class="relative flex w-full items-center justify-center gap-3 overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 py-4 text-sm font-black uppercase tracking-widest text-white transition-all hover:scale-[1.02] hover:shadow-[0_15px_30px_rgba(37,99,235,0.4)] disabled:opacity-70 disabled:hover:scale-100 disabled:hover:shadow-none"
                            :disabled="isProcessing || cartItems.length === 0 || !fullName.trim() || !location.trim()"
                            @click="processCheckout"
                        >
                            <div v-if="isProcessing" class="absolute inset-0 bg-white/20 animate-pulse"></div>
                            <i class="fa-brands" :class="isProcessing ? 'fa-playstation fa-spin' : 'fa-playstation'"></i>
                            <span class="relative z-10">{{ isProcessing ? 'Authorizing...' : `Confirm Payment` }}</span>
                        </button>
                        <button
                            v-else
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-gray-900 py-4 text-sm font-black uppercase tracking-widest text-white shadow-md transition hover:bg-gray-800 dark:bg-gray-800 dark:hover:bg-gray-700"
                            @click="close"
                        >
                            <i class="fa-solid fa-gamepad"></i>
                            Continue Shopping
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.3);
    border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(75, 85, 99, 0.5);
}
</style>
