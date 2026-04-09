import { useState, useEffect } from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { client } from './graphql/client';
import { GET_CATEGORIES_AND_PRODUCTS } from './graphql/queries';
import { CartProvider } from './context/CartContext';
import Header from './components/Header';
import CartOverlay from './components/CartOverlay';
import ProductListingPage from './pages/ProductListingPage';
import ProductDetailsPage from './pages/ProductDetailsPage';
import './App.css';

function ShopApp() {
  const [categories, setCategories]       = useState([]);
  const [products, setProducts]           = useState([]);
  const [activeCategory, setActiveCategory] = useState('all');
  const [loading, setLoading]             = useState(true);

  // Fetch data whenever active category changes
  useEffect(() => {
    setLoading(true);
    client
      .request(GET_CATEGORIES_AND_PRODUCTS, { category: activeCategory })
      .then(data => {
        setCategories(data.categories);
        setProducts(data.products);
        setLoading(false);
      })
      .catch(err => {
        console.error(err);
        setLoading(false);
      });
  }, [activeCategory]);

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
          <Route
            path="/"
            element={
              loading
                ? <p className="loading">Loading...</p>
                : <ProductListingPage products={products} activeCategory={activeCategory} />
            }
          />
          <Route path="/product/:id" element={<ProductDetailsPage />} />
          {/* Catch-all -> home */}
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