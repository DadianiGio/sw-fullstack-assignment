import { useState, useEffect } from 'react';
import { BrowserRouter, Routes, Route, Navigate, useParams } from 'react-router-dom';
import { client } from './graphql/client';
import { GET_CATEGORIES_AND_PRODUCTS } from './graphql/queries';
import { CartProvider } from './context/CartContext';
import Header from './components/Header';
import CartOverlay from './components/CartOverlay';
import ProductListingPage from './pages/ProductListingPage';
import ProductDetailsPage from './pages/ProductDetailsPage';
import './App.css';

// Wrapper so category route param sets active category
function CategoryPage({ onCategoryChange }) {
  const { categoryName } = useParams();
  const [products, setProducts] = useState([]);
  const [loading, setLoading]   = useState(true);
  const activeCat = categoryName || 'all';

  useEffect(() => {
    onCategoryChange(activeCat);
    setLoading(true);
    client
      .request(GET_CATEGORIES_AND_PRODUCTS, { category: activeCat })
      .then(data => {
        setProducts(data.products);
        setLoading(false);
      })
      .catch(() => setLoading(false));
  }, [activeCat]);

  if (loading) return <p className="loading">Loading...</p>;
  return <ProductListingPage products={products} activeCategory={activeCat} />;
}

function ShopApp() {
  const [categories, setCategories]         = useState([]);
  const [activeCategory, setActiveCategory] = useState('all');

  useEffect(() => {
    client
      .request(GET_CATEGORIES_AND_PRODUCTS, { category: 'all' })
      .then(data => setCategories(data.categories))
      .catch(console.error);
  }, []);

  return (
    <>
      <Header
        categories={categories}
        activeCategory={activeCategory}
        onCategoryChange={setActiveCategory}
      />
      <CartOverlay />

      <div className="page-content">
        <Routes>
        {/* Default route — show all products */}
          <Route
            path="/"
            element={<CategoryPage onCategoryChange={setActiveCategory} />}
          />
          {/* Category route — /all, /tech, /clothes */}
          <Route
            path="/:categoryName"
            element={<CategoryPage onCategoryChange={setActiveCategory} />}
          />
          {/* Product details */}
          <Route path="/product/:id" element={<ProductDetailsPage />} />
          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </div>
    </>
  );
}

export default function App() {
  return (
    <BrowserRouter>
      <CartProvider>
        <ShopApp />
      </CartProvider>
    </BrowserRouter>
  );
}