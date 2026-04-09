import { useCart } from '../context/CartContext';
import { toKebabCase } from '../utils/helpers';

// The green shopping bag SVG icon
function CartIcon() {
  return (
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
         xmlns="http://www.w3.org/2000/svg">
      <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"
            stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
      <line x1="3" y1="6" x2="21" y2="6"
            stroke="currentColor" strokeWidth="2" strokeLinecap="round"/>
      <path d="M16 10a4 4 0 01-8 0"
            stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
    </svg>
  );
}

export default function Header({ categories, activeCategory, onCategoryChange }) {
  const { totalItems, cartOpen, setCartOpen } = useCart();

  return (
    <header className="header">
      {/* Category navigation */}
      <nav className="header__nav">
        {categories.map(cat => (
          <button
            key={cat.name}
            className={`header__cat-link ${activeCategory === cat.name ? 'header__cat-link--active' : ''}`}
            onClick={() => onCategoryChange(cat.name)}
            data-testid={activeCategory === cat.name ? 'active-category-link' : 'category-link'}
          >
            {cat.name.toUpperCase()}
          </button>
        ))}
      </nav>

      {/*Logo (centre) */}
      <div className="header__logo">
        <svg width="41" height="41" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M20.5 0C9.18 0 0 9.18 0 20.5S9.18 41 20.5 41 41 31.82 41 20.5 31.82 0 20.5 0z" fill="#5ECE7B"/>
          <path d="M28 16h-3v-2a4.5 4.5 0 00-9 0v2h-3l-1 13h17l-1-13zm-10-2a2.5 2.5 0 015 0v2h-5v-2z" fill="white"/>
        </svg>
      </div>

      {/*Cart button*/}
      <div className="header__cart">
        <button
          className="header__cart-btn"
          onClick={() => setCartOpen(prev => !prev)}
          data-testid="cart-btn"
        >
          <CartIcon />
          {/* Badge only visible when cart has items */}
          {totalItems > 0 && (
            <span className="header__cart-badge">{totalItems}</span>
          )}
        </button>
      </div>
    </header>
  );
}