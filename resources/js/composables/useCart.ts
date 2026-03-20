import { ref, computed } from 'vue';

export interface Product {
    id: number;
    name: string;
    image: string | null;
    price: number;
    stock: number;
    platform?: string;
    category?: string;
}

export interface CartItem {
    product: Product;
    quantity: number;
}

export function formatPrice(value: number): string {
    return `₱${Number(value).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

const items = ref<CartItem[]>([]);

export function useCart() {
    const totalItems = computed(() => items.value.reduce((sum, item) => sum + item.quantity, 0));

    const totalPrice = computed(() =>
        items.value.reduce((sum, item) => sum + item.product.price * item.quantity, 0),
    );

    function addToCart(product: Product, quantity: number = 1) {
        const existing = items.value.find((item) => item.product.id === product.id);
        if (existing) {
            existing.quantity = Math.min(existing.quantity + quantity, product.stock);
        } else {
            items.value.push({ product, quantity: Math.min(quantity, product.stock) });
        }
    }

    function removeFromCart(productId: number) {
        items.value = items.value.filter((item) => item.product.id !== productId);
    }

    function incrementQuantity(productId: number) {
        const item = items.value.find((i) => i.product.id === productId);
        if (item && item.quantity < item.product.stock) {
            item.quantity++;
        }
    }

    function decrementQuantity(productId: number) {
        const item = items.value.find((i) => i.product.id === productId);
        if (item) {
            if (item.quantity <= 1) {
                removeFromCart(productId);
            } else {
                item.quantity--;
            }
        }
    }

    function clearCart() {
        items.value = [];
    }

    function updateProductStock(productId: number, newStock: number) {
        const item = items.value.find((i) => i.product.id === productId);
        if (item) {
            item.product.stock = newStock;
            if (item.quantity > newStock) {
                item.quantity = newStock;
            }
            if (newStock <= 0) {
                removeFromCart(productId);
            }
        }
    }

    return {
        items,
        totalItems,
        totalPrice,
        addToCart,
        removeFromCart,
        incrementQuantity,
        decrementQuantity,
        clearCart,
        updateProductStock,
    };
}
