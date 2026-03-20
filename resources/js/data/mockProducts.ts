import type { Product } from '../composables/useCart';

export const mockProducts: Product[] = [
    { id: 1, name: 'Elden Ring', image: '/images/products/eldenring.jpg', price: 3490, stock: 15, platform: 'PS5 Disc', category: 'Game' },
    { id: 2, name: 'God of War Ragnarök', image: '/images/products/gowr.jpg', price: 3490, stock: 20, platform: 'PS5 Disc', category: 'Game' },
    { id: 3, name: 'Spider-Man 2', image: '/images/products/spiderman2.png', price: 3490, stock: 25, platform: 'PS5 Disc', category: 'Game' },
    { id: 4, name: 'Final Fantasy VII Rebirth', image: '/images/products/finalfantasy.png', price: 3490, stock: 10, platform: 'PS5 Disc', category: 'Game' },
    { id: 5, name: 'Resident Evil 4 Remake', image: '/images/products/residentevil4.png', price: 2990, stock: 8, platform: 'PS5 Disc', category: 'Game' },
    { id: 6, name: 'Horizon Forbidden West', image: '/images/products/horizon.png', price: 2490, stock: 30, platform: 'PS5 Disc', category: 'Game' },
    { id: 7, name: 'Call of Duty: Modern Warfare III', image: '/images/products/cod.png', price: 3490, stock: 50, platform: 'PS5 Disc', category: 'Game' },
    { id: 8, name: 'NBA 2K24', image: '/images/products/nba.png', price: 3490, stock: 40, platform: 'PS5 Disc', category: 'Game' },
    { id: 9, name: 'Gran Turismo 7', image: '/images/products/granturismo.png', price: 3490, stock: 12, platform: 'PS5 Disc', category: 'Game' },
    { id: 10, name: 'Tekken 8', image: '/images/products/tekken.png', price: 3490, stock: 18, platform: 'PS5 Disc', category: 'Game' },
    { id: 11, name: 'Ghost of Tsushima Director\'s Cut', image: '/images/products/gotd.png', price: 3490, stock: 14, platform: 'PS5 Disc', category: 'Game' },
    { id: 12, name: 'DualSense Wireless Controller', image: '/images/products/controller.png', price: 3990, stock: 100, platform: 'Accessory', category: 'Peripheral' },
    { id: 13, name: 'Pulse 3D Headset', image: '/images/products/headset.png', price: 4990, stock: 45, platform: 'Accessory', category: 'Peripheral' },
    { id: 14, name: 'PS5 Charging Dock', image: '/images/products/charging.png', price: 1690, stock: 60, platform: 'Accessory', category: 'Peripheral' },
    { id: 15, name: 'Sony INZONE M9 Gaming Monitor', image: '/images/products/monitor.png', price: 45990, stock: 5, platform: 'Monitor', category: 'Hardware' },
    { id: 16, name: 'PlayStation Edition Gaming Chair', image: '/images/products/chair.png', price: 15990, stock: 0, platform: 'Chair', category: 'Furniture' },
];
