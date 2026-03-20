<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import { useCart } from '../composables/useCart';
import { useParticles } from '../composables/useParticles';
import type { Product } from '../composables/useCart';
import Navbar from '../components/Navbar.vue';
import ProductCard from '../components/ProductCard.vue';
import CartDrawer from '../components/CartDrawer.vue';
import CheckoutModal from '../components/CheckoutModal.vue';

// Composables
const { addToCart } = useCart();
const { particlesContainer, throttledMouseMove } = useParticles();

// State
const allProducts = ref<Product[]>([]);
const loading = ref(true);
const cartOpen = ref(false);
const toast = ref<{ type: 'success' | 'error'; text: string } | null>(null);

// Template Refs
const checkoutRef = ref<InstanceType<typeof CheckoutModal> | null>(null);

// Search & Pagination Config
const searchQuery = ref('');
const selectedCategory = ref('All');
const currentPage = ref(1);
const perPage = 8;
let searchTimeout: ReturnType<typeof setTimeout> | null = null;

const categories = computed(() => {
    const list = Array.from(
        new Set(
            allProducts.value
                .map((product) => product.category)
                .filter((category): category is string => Boolean(category && category.trim())),
        ),
    ).sort((a, b) => a.localeCompare(b));

    return ['All', ...list];
});

// Computed: Search filter
const filteredProducts = computed(() => {
    const categoryFiltered = selectedCategory.value === 'All'
        ? allProducts.value
        : allProducts.value.filter((p) => p.category === selectedCategory.value);

    const q = searchQuery.value.toLowerCase().trim();
    const searched = !q
        ? categoryFiltered
        : categoryFiltered.filter(
        (p) => p.name.toLowerCase().includes(q) || 
               p.category?.toLowerCase().includes(q) || 
               p.platform?.toLowerCase().includes(q)
    );

    return [...searched].sort((a, b) => {
        const aOutOfStock = a.stock <= 0;
        const bOutOfStock = b.stock <= 0;

        if (aOutOfStock !== bOutOfStock) {
            return aOutOfStock ? 1 : -1;
        }

        return a.name.localeCompare(b.name);
    });
});

// Computed: Pagination
const totalPages = computed(() => Math.ceil(filteredProducts.value.length / perPage));

const paginatedProducts = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return filteredProducts.value.slice(start, start + perPage);
});

const pageNumbers = computed(() => {
    const pages: number[] = [];
    const total = totalPages.value;
    const current = currentPage.value;

    if (total <= 5) {
        for (let i = 1; i <= total; i++) pages.push(i);
    } else {
        pages.push(1);
        if (current > 3) pages.push(-1); // ellipsis
        for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) {
            pages.push(i);
        }
        if (current < total - 2) pages.push(-1); // ellipsis
        pages.push(total);
    }
    return pages;
});

// Watchers
watch([searchQuery, selectedCategory], () => {
    currentPage.value = 1; // Reset to page 1 on search
});

// Actions
async function fetchProducts() {
    loading.value = true;
    try {
        const response = await fetch('/api/products', {
            headers: {
                Accept: 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error('Failed to load products');
        }

        allProducts.value = await response.json();
    } catch {
        allProducts.value = [];
        showToast('error', 'Unable to load products. Please refresh.');
    } finally {
        loading.value = false;
    }
}

function handleSearch(event: Event) {
    const value = (event.target as HTMLInputElement).value;
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        searchQuery.value = value;
    }, 250);
}

function handleAddToCart(product: Product) {
    if (product.stock <= 0) return;
    addToCart(product);
    showToast('success', `${product.name} added to cart`);
}

function handleCheckout() {
    cartOpen.value = false;
    checkoutRef.value?.open();
}

function selectCategory(category: string) {
    selectedCategory.value = category;
}

function showToast(type: 'success' | 'error', text: string) {
    toast.value = { type, text };
    setTimeout(() => { toast.value = null; }, 3000);
}

// Lifecycle
onMounted(() => {
    void fetchProducts();
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 text-gray-900 transition-colors duration-300 dark:bg-[#06080A] dark:text-gray-100 selection:bg-blue-500/30 selection:text-blue-200">
        <Navbar 
            :searchQuery="searchQuery" 
            @search="handleSearch" 
            @clearSearch="searchQuery = ''" 
            @toggle-cart="cartOpen = !cartOpen" 
        />

        <!-- Toast -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 -translate-y-4 filter blur-sm"
            enter-to-class="opacity-100 translate-y-0 filter blur-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-4"
        >
            <div
                v-if="toast"
                class="fixed top-24 right-6 z-[100] flex items-center gap-3 rounded-2xl border px-5 py-4 shadow-[0_10px_40px_rgba(0,0,0,0.2)] backdrop-blur-xl"
                :class="toast.type === 'success'
                    ? 'border-green-500/30 bg-green-50/90 text-green-800 dark:bg-[#0B1511]/90 dark:text-green-400'
                    : 'border-red-500/30 bg-red-50/90 text-red-800 dark:bg-[#1A0B0B]/90 dark:text-red-400'"
            >
                <i class="fa-solid text-xl" :class="toast.type === 'success' ? 'fa-circle-check text-green-500' : 'fa-circle-exclamation text-red-500'"></i>
                <span class="text-sm font-bold tracking-wide">{{ toast.text }}</span>
            </div>
        </Transition>

        <!-- Hero Section -->
        <section class="group relative overflow-hidden bg-[#0A0E17] cursor-default" @mousemove="throttledMouseMove">
            <!-- Particles Container -->
            <div ref="particlesContainer" class="pointer-events-none absolute inset-0 z-50 overflow-hidden"></div>

            <div class="absolute inset-0 z-0 transition-transform duration-1000 ease-out group-hover:scale-105">
                <!-- Replaceable background placeholder -->
                <div class="h-full w-full bg-blue-900/10 dark:bg-blue-900/20 opacity-60">
                    <img src="/images/products/hero.jpg" alt="Hero Background" class="h-full w-full object-cover" style="object-position: center 0px;" />
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-[#06080A] via-[#06080A]/80 to-transparent dark:from-[#06080A]"></div>
                <!-- Neon glow accents -->
                <div class="absolute -top-[20%] -left-[10%] h-[500px] w-[500px] rounded-full bg-blue-600/30 blur-[120px] transition-all duration-[1500ms] ease-out group-hover:bg-blue-500/50 group-hover:blur-[150px]"></div>
                <div class="absolute -bottom-[20%] -right-[10%] h-[500px] w-[500px] rounded-full bg-purple-600/20 blur-[120px] transition-all duration-[1500ms] ease-out group-hover:bg-indigo-500/40 group-hover:blur-[150px]"></div>
            </div>

            <div class="relative z-10 mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:py-32 flex flex-col items-center text-center transition-transform duration-[1500ms] ease-out group-hover:-translate-y-2">
                <span class="mb-4 inline-flex items-center gap-2 rounded-full border border-blue-500/30 bg-blue-500/10 px-4 py-1.5 text-xs font-black uppercase tracking-widest text-blue-400 backdrop-blur-md transition-all duration-700 group-hover:border-blue-400/50 group-hover:bg-blue-500/20 group-hover:shadow-[0_0_20px_rgba(59,130,246,0.3)]">
                    <i class="fa-solid fa-gamepad"></i> Next-Gen Gaming
                </span>
                <h2 class="text-4xl font-black tracking-tighter text-white sm:text-6xl lg:text-7xl drop-shadow-2xl transition-colors duration-700">
                    Level Up Your <span class="bg-gradient-to-r from-blue-400 to-indigo-400 bg-clip-text text-transparent transition-all duration-700 group-hover:from-blue-300 group-hover:to-purple-400">Play</span>
                </h2>
                <p class="mx-auto mt-6 max-w-2xl text-lg font-medium text-gray-300 sm:text-xl leading-relaxed transition-opacity duration-700 group-hover:text-gray-200">
                    Discover premium PlayStation games, cutting-edge peripherals, and the ultimate gear for your gaming setup.
                </p>
            </div>
        </section>

        <!-- Main Content -->
        <main class="relative z-20 mx-auto max-w-7xl px-4 py-16 sm:px-6">
            <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-3">
                        <i class="fa-solid fa-fire text-orange-500"></i> Trending Now
                    </h3>
                    <p class="mt-2 text-sm font-medium text-gray-500 dark:text-gray-400">The most popular titles and gear this week.</p>
                </div>

                <!-- Results Info -->
                <div v-if="!loading && (searchQuery || selectedCategory !== 'All')" class="flex items-center gap-2 rounded-xl bg-gray-200/50 px-4 py-2 text-sm font-semibold text-gray-700 backdrop-blur-sm dark:bg-[#151923] dark:text-gray-300 border border-gray-300/50 dark:border-gray-800">
                    <i class="fa-solid fa-filter text-blue-500"></i>
                    <span>
                        <strong class="text-blue-600 dark:text-blue-400">{{ filteredProducts.length }}</strong>
                        item{{ filteredProducts.length !== 1 ? 's' : '' }}
                        <template v-if="selectedCategory !== 'All'"> in <strong class="text-gray-900 dark:text-white">{{ selectedCategory }}</strong></template>
                        <template v-if="searchQuery"> matching "<strong class="text-gray-900 dark:text-white">{{ searchQuery }}</strong>"</template>
                    </span>
                </div>
            </div>

            <div class="mb-8 flex flex-wrap items-center gap-2">
                <button
                    v-for="category in categories"
                    :key="category"
                    class="rounded-full border px-4 py-2 text-xs font-bold uppercase tracking-wide transition"
                    :class="selectedCategory === category
                        ? 'border-blue-600 bg-blue-600 text-white dark:border-blue-500 dark:bg-blue-500'
                        : 'border-gray-300 bg-white text-gray-700 hover:border-blue-500 hover:text-blue-600 dark:border-gray-700 dark:bg-[#111827] dark:text-gray-300 dark:hover:border-blue-400 dark:hover:text-blue-300'"
                    @click="selectCategory(category)"
                >
                    {{ category }}
                </button>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="flex flex-col items-center justify-center py-32">
                <div class="relative h-20 w-20">
                    <div class="absolute inset-0 rounded-full border-t-4 border-blue-500 animate-spin"></div>
                    <div class="absolute inset-2 rounded-full border-r-4 border-purple-500 animate-spin" style="animation-direction: reverse; animation-duration: 1.5s;"></div>
                    <i class="fa-brands fa-playstation absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-2xl text-blue-500 dark:text-gray-300"></i>
                </div>
                <p class="mt-6 text-sm font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Loading Arsenal...</p>
            </div>

            <!-- Product Grid -->
            <div v-else-if="paginatedProducts.length > 0" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <ProductCard
                    v-for="product in paginatedProducts"
                    :key="product.id"
                    :product="product"
                    @add-to-cart="handleAddToCart"
                />
            </div>

            <!-- Empty / No Results -->
            <div v-else class="flex flex-col items-center justify-center py-32 text-center rounded-3xl border border-dashed border-gray-300 bg-white/50 dark:border-gray-800 dark:bg-[#0B0E14]/50 backdrop-blur-sm">
                <div class="flex h-24 w-24 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-900">
                    <i class="fa-solid text-4xl text-gray-400 dark:text-gray-600" :class="searchQuery ? 'fa-ghost' : 'fa-box-open'"></i>
                </div>
                <h4 class="mt-6 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ searchQuery ? 'No matches found' : 'Arsenal Empty' }}
                </h4>
                <p class="mt-2 max-w-md text-sm font-medium text-gray-500 dark:text-gray-400 leading-relaxed">
                    {{ (searchQuery || selectedCategory !== 'All') ? 'No products match your current search/category filters. Try another filter.' : 'No products available at the moment. Check back later.' }}
                </p>
                <button
                    v-if="searchQuery || selectedCategory !== 'All'"
                    class="mt-8 inline-flex items-center gap-2 rounded-xl bg-gray-900 px-6 py-3 text-sm font-bold uppercase tracking-wider text-white transition hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-white"
                    @click="searchQuery = ''; selectedCategory = 'All'"
                >
                    <i class="fa-solid fa-rotate-left"></i> Reset Search
                </button>
            </div>

            <!-- Pagination -->
            <nav v-if="totalPages > 1" class="mt-16 flex items-center justify-center gap-2">
                <button
                    class="flex h-10 w-10 items-center justify-center rounded-xl text-sm font-bold shadow-sm transition-all disabled:opacity-30 disabled:shadow-none"
                    :class="currentPage > 1
                        ? 'border border-gray-200 bg-white text-gray-700 hover:border-blue-500 hover:text-blue-600 dark:border-gray-800 dark:bg-[#151923] dark:text-gray-300 dark:hover:border-blue-500/50 dark:hover:text-blue-400'
                        : 'border border-gray-100 bg-gray-50 text-gray-400 dark:border-gray-800/50 dark:bg-[#0B0E14] dark:text-gray-600'"
                    :disabled="currentPage <= 1"
                    @click="currentPage--"
                >
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <div class="flex items-center gap-1 rounded-xl border border-gray-200 bg-white p-1 shadow-sm dark:border-gray-800 dark:bg-[#151923]">
                    <template v-for="(page, idx) in pageNumbers" :key="idx">
                        <span v-if="page === -1" class="flex h-8 w-8 items-center justify-center text-gray-400">…</span>
                        <button
                            v-else
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-sm font-bold transition-all"
                            :class="page === currentPage
                                ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md'
                                : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white'"
                            @click="currentPage = page"
                        >
                            {{ page }}
                        </button>
                    </template>
                </div>

                <button
                    class="flex h-10 w-10 items-center justify-center rounded-xl text-sm font-bold shadow-sm transition-all disabled:opacity-30 disabled:shadow-none"
                    :class="currentPage < totalPages
                        ? 'border border-gray-200 bg-white text-gray-700 hover:border-blue-500 hover:text-blue-600 dark:border-gray-800 dark:bg-[#151923] dark:text-gray-300 dark:hover:border-blue-500/50 dark:hover:text-blue-400'
                        : 'border border-gray-100 bg-gray-50 text-gray-400 dark:border-gray-800/50 dark:bg-[#0B0E14] dark:text-gray-600'"
                    :disabled="currentPage >= totalPages"
                    @click="currentPage++"
                >
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </nav>
        </main>

        <!-- Footer -->
        <footer class="mt-20 border-t border-gray-200/50 bg-[#F8F9FA] dark:border-gray-800/50 dark:bg-[#0A0D12]">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 flex flex-col items-center justify-between gap-6 md:flex-row">
                <div class="flex items-center gap-2">
                    <i class="fa-brands fa-playstation text-2xl text-blue-600 dark:text-blue-500"></i>
                    <span class="text-lg font-black tracking-tighter text-gray-900 dark:text-white">Game<span class="text-blue-600 dark:text-blue-500">Store</span></span>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    &copy; 2026 Badz. Built for Lab Activity 3.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="text-gray-400 transition hover:text-blue-600 dark:hover:text-blue-400"><i class="fa-brands fa-twitter text-xl"></i></a>
                    <a href="#" class="text-gray-400 transition hover:text-purple-600 dark:hover:text-purple-400"><i class="fa-brands fa-discord text-xl"></i></a>
                    <a href="#" class="text-gray-400 transition hover:text-black dark:hover:text-white"><i class="fa-brands fa-github text-xl"></i></a>
                </div>
            </div>
        </footer>

        <CartDrawer :is-open="cartOpen" @close="cartOpen = false" @checkout="handleCheckout" />
        <CheckoutModal ref="checkoutRef" />
    </div>
</template>
