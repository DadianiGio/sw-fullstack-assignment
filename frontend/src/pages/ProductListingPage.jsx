import { useNavigate } from 'react-router-dom';
import ProductCard from '../components/ProductCard';

export default function ProductListingPage({ products, activeCategory }) {
  const navigate = useNavigate();

  return (
    <main className="plp">
      <h1 className="plp__title">
        {activeCategory.charAt(0).toUpperCase() + activeCategory.slice(1)}
      </h1>

      <div className="plp__grid">
        {products.map(product => (
          <ProductCard
            key={product.id}
            product={product}
            onClick={() => navigate(`/product/${product.id}`)}
          />
        ))}
      </div>
    </main>
  );
}