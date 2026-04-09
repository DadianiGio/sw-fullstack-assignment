import { createContext, useContext, useState } from 'react';

const CartContext = createContext(null);

export function CartProvider({ children }) {
  const [cartItems, setCartItems]       = useState([]);
  const [cartOpen, setCartOpen]         = useState(false);

  /**
   * Build a unique key for a cart item based on product id + selected options.
   * Same product with different options = different cart line.
   */
  const buildKey = (productId, selectedAttributes) =>
    productId + '_' + JSON.stringify(selectedAttributes);

  const addToCart = (product, selectedAttributes) => {
    const key = buildKey(product.id, selectedAttributes);

    setCartItems(prev => {
      const existing = prev.find(i => i.key === key);
      if (existing) {
        // Same product + same options -> increase quantity
        return prev.map(i => i.key === key ? { ...i, quantity: i.quantity + 1 } : i);
      }
      // New line item
      return [...prev, { key, product, selectedAttributes, quantity: 1 }];
    });
  };

  const increaseQty = (key) =>
    setCartItems(prev =>
      prev.map(i => i.key === key ? { ...i, quantity: i.quantity + 1 } : i)
    );

  const decreaseQty = (key) =>
    setCartItems(prev =>
      prev
        .map(i => i.key === key ? { ...i, quantity: i.quantity - 1 } : i)
        .filter(i => i.quantity > 0)   // remove if hits 0
    );

  const clearCart = () => setCartItems([]);

  const totalItems = cartItems.reduce((sum, i) => sum + i.quantity, 0);

  const totalPrice = cartItems.reduce((sum, i) => {
    const price = i.product.prices[0]?.amount ?? 0;
    return sum + price * i.quantity;
  }, 0);

  return (
    <CartContext.Provider value={{
      cartItems, cartOpen, setCartOpen,
      addToCart, increaseQty, decreaseQty, clearCart,
      totalItems, totalPrice,
    }}>
      {children}
    </CartContext.Provider>
  );
}

export const useCart = () => useContext(CartContext);