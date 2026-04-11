import { useCart } from '../context/CartContext';
import { client } from '../graphql/client';
import { PLACE_ORDER } from '../graphql/queries';
import { formatPrice } from '../utils/helpers';
import CartItem from './CartItem';

export default function CartOverlay() {
  const {
    cartItems, cartOpen, setCartOpen,
    totalItems, totalPrice, clearCart
  } = useCart();

  if (!cartOpen) return null;

  const itemLabel = totalItems === 1 ? '1 Item' : `${totalItems} Items`;
  const symbol    = cartItems[0]?.product?.prices[0]?.currency?.symbol ?? '$';

  const handlePlaceOrder = async () => {
    if (cartItems.length === 0) return;

    // Build mutation payload
    const items = cartItems.map(i => ({
      productId: i.product.id,
      quantity:  i.quantity,
      selectedAttributes: Object.entries(i.selectedAttributes).map(
        ([attributeId, itemId]) => ({ attributeId, itemId })
      ),
    }));

    try {
      await client.request(PLACE_ORDER, { items });
      clearCart();
      setCartOpen(false);
      alert('Order placed successfully!');
    } catch (err) {
      alert('Failed to place order. Please try again.');
      console.error(err);
    }
  };

  return (
    <>
    {/* Grey overlay behind content*/}
      <div
        className="cart-backdrop"
        onClick={() => setCartOpen(false)}
      />

      {/* Overlay panel*/}
      <div className="cart-overlay" data-testid="cart-overlay">
        <p className="cart-overlay__title">
          <strong>My Bag</strong>, {itemLabel}
        </p>
        
        {/* Product list */}
        <div className="cart-overlay__items">
          {cartItems.map(item => (
            <CartItem key={item.key} item={item} />
          ))}
        </div>
        
        {/* Total */}
        <div className="cart-overlay__total">
          <span>Total</span>
          <span data-testid="cart-total">
            {formatPrice(totalPrice, symbol)}
          </span>
        </div>
        
        {/* Place Order button */}
        <button
          className={'cart-overlay__order-btn' + (cartItems.length === 0 ? ' cart-overlay__order-btn--disabled' : '')}
          onClick={handlePlaceOrder}
          disabled={cartItems.length === 0}
        >
          PLACE ORDER
        </button>
      </div>
    </>
  );
}