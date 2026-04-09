import { useCart } from '../context/CartContext';
import { toKebabCase, formatPrice } from '../utils/helpers';

// Small cart icon for quick-shop button
function QuickShopIcon() {
  return (
    <svg width="18" height="18" viewBox="0 0 24 24" fill="white"
         xmlns="http://www.w3.org/2000/svg">
      <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
      <path d="M16 10a4 4 0 01-8 0" fill="none" stroke="white" strokeWidth="2"/>
    </svg>
  );
}

export default function ProductCard({ product, onClick }) {
  const { addToCart, setCartOpen } = useCart();

  const price  = product.prices[0];
  const symbol = price?.currency?.symbol ?? '$';

  // Build default selected attributes (first item in each array)
  const defaultAttributes = {};
  product.attributes.forEach(attr => {
    if (attr.items.length > 0) {
      defaultAttributes[attr.id] = attr.items[0].id;
    }
  });

  const handleQuickShop = (e) => {
    e.stopPropagation();
    addToCart(product, defaultAttributes);
    setCartOpen(true);
  };

  const cardKebab = toKebabCase(product.name);

  return (
    <div
      className={`product-card ${!product.inStock ? 'product-card--out-of-stock' : ''}`}
      onClick={onClick}
      data-testid={`product-${cardKebab}`} 
    >
      {/* Image area */}
      <div className="product-card__img-wrap">
        <img
          src={product.gallery[0]}
          alt={product.name}
          className="product-card__img"
        />

        {/* Out of Stock overlay */}
        {!product.inStock && (
          <div className="product-card__oos-overlay">
            <span>OUT OF STOCK</span>
          </div>
        )}

        {/* Quick Shop button  only for in-stock, shown on hover */}
        {product.inStock && (
          <button
            className="product-card__quick-shop"
            onClick={handleQuickShop}
            title="Add to cart"
          >
            <QuickShopIcon />
          </button>
        )}
      </div>

      {/* Info */}
      <div className="product-card__info">
        <p className={`product-card__name ${!product.inStock ? 'product-card__name--oos' : ''}`}>
          {product.name}
        </p>
        <p className={`product-card__price ${!product.inStock ? 'product-card__price--oos' : ''}`}>
          {formatPrice(price?.amount ?? 0, symbol)}
        </p>
      </div>
    </div>
  );
}