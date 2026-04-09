import { useCart } from '../context/CartContext';
import { toKebabCase, formatPrice } from '../utils/helpers';

export default function CartItem({ item }) {
  const { increaseQty, decreaseQty } = useCart();
  const { product, selectedAttributes, quantity, key } = item;

  const price  = product.prices[0];
  const symbol = price?.currency?.symbol ?? '$';

  return (
    <div className="cart-item">
      {/*  Left side: info + attributes */}
      <div className="cart-item__info">
        <p className="cart-item__brand">{product.brand}</p>
        <p className="cart-item__name">{product.name}</p>
        <p className="cart-item__price">
          {formatPrice(price?.amount ?? 0, symbol)}
        </p>

        {/* Attribute swatches*/}
        {product.attributes.map(attr => {
          const attrKebab = toKebabCase(attr.name);

          return (
            <div
              key={attr.id}
              className="cart-item__attr"
              data-testid={`cart-item-attribute-${attrKebab}`}
            >
              <p className="cart-item__attr-label">{attr.name}:</p>
              <div className="cart-item__attr-options">
                {attr.items.map(opt => {
                  const optKebab   = toKebabCase(opt.id);
                  const isSelected = selectedAttributes[attr.id] === opt.id;
                  const isSwatch   = attr.type === 'swatch';

                  return (
                    <span
                      key={opt.id}
                      className={[
                        'cart-item__attr-opt',
                        isSwatch ? 'cart-item__attr-opt--swatch' : '',
                        isSelected ? 'cart-item__attr-opt--selected' : '',
                      ].join(' ')}
                      style={isSwatch ? { backgroundColor: opt.value } : {}}
                      data-testid={
                        isSelected
                          ? `cart-item-attribute-${attrKebab}-${optKebab}-selected`
                          : `cart-item-attribute-${attrKebab}-${optKebab}`
                      }
                    >
                      {isSwatch ? '' : opt.displayValue}
                    </span>
                  );
                })}
              </div>
            </div>
          );
        })}
      </div>

      {/*Middle: quantity controls*/}
      <div className="cart-item__qty">
        <button
          className="cart-item__qty-btn"
          onClick={() => increaseQty(key)}
          data-testid="cart-item-amount-increase"
        >+</button>

        <span
          className="cart-item__qty-count"
          data-testid="cart-item-amount"
        >{quantity}</span>

        <button
          className="cart-item__qty-btn"
          onClick={() => decreaseQty(key)}
          data-testid="cart-item-amount-decrease"
        >−</button>
      </div>

      {/*Right side: product image  */}
      <div className="cart-item__img-wrap">
        <img
          src={product.gallery[0]}
          alt={product.name}
          className="cart-item__img"
        />
      </div>
    </div>
  );
}