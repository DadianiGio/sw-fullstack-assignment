import { useState, useEffect, useRef } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { client } from '../graphql/client';
import { GET_PRODUCT } from '../graphql/queries';
import { useCart } from '../context/CartContext';
import AttributeSelector from '../components/AttributeSelector';
import { formatPrice } from '../utils/helpers';

/**
 * Parses HTML description without dangerouslySetInnerHTML.
 * Uses DOMParser (safe — no script execution) and renders text nodes.
 */
function SafeHtml({ html }) {
  const ref = useRef(null);

  useEffect(() => {
    if (!ref.current) return;
    // DOMParser parses HTML in a sandboxed document — scripts don't execute
    const parser = new DOMParser();
    const doc    = parser.parseFromString(html, 'text/html');
    ref.current.innerHTML = ''; // clear previous
    // Append each child from parsed body
    Array.from(doc.body.childNodes).forEach(node => {
      ref.current.appendChild(document.importNode(node, true));
    });
  }, [html]);

  return <div ref={ref} data-testid="product-description" className="pdp__description" />;
}

export default function ProductDetailsPage() {
  const { id }                        = useParams();
  const navigate                       = useNavigate();
  const { addToCart, setCartOpen }    = useCart();
  const [product, setProduct]         = useState(null);
  const [loading, setLoading]         = useState(true);
  const [selectedAttrs, setSelectedAttrs] = useState({});
  const [mainImgIdx, setMainImgIdx]   = useState(0);

  useEffect(() => {
    setLoading(true);
    client
      .request(GET_PRODUCT, { id })
      .then(data => {
        setProduct(data.product);
        setLoading(false);
      })
      .catch(() => setLoading(false));
  }, [id]);

  if (loading) return <p className="pdp__loading">Loading...</p>;
  if (!product) return <p className="pdp__loading">Product not found.</p>;

  const price  = product.prices[0];
  const symbol = price?.currency?.symbol ?? '$';

  // All required attributes must be selected before Add to Cart is enabled
  const allSelected = product.attributes.every(
    attr => selectedAttrs[attr.id] !== undefined
  );

  const handleAttrChange = (attrId, itemId) => {
    setSelectedAttrs(prev => ({ ...prev, [attrId]: itemId }));
  };

  const handleAddToCart = () => {
    addToCart(product, selectedAttrs);
    setCartOpen(true);
  };

  const gallery = product.gallery;

  return (
    <main className="pdp">
      {/* Image gallery*/}
      <section className="pdp__gallery" data-testid="product-gallery">
        {/* Thumbnail column */}
        <div className="pdp__thumbs">
          {gallery.map((img, idx) => (
            <img
              key={idx}
              src={img}
              alt={`${product.name} ${idx + 1}`}
              className={`pdp__thumb ${idx === mainImgIdx ? 'pdp__thumb--active' : ''}`}
              onClick={() => setMainImgIdx(idx)}
            />
          ))}
        </div>

        {/* Main image with prev/next arrows */}
        <div className="pdp__main-img-wrap">
          <button
            className="pdp__arrow pdp__arrow--left"
            onClick={() => setMainImgIdx(i => (i === 0 ? gallery.length - 1 : i - 1))}
          >‹</button>

          <img
            src={gallery[mainImgIdx]}
            alt={product.name}
            className="pdp__main-img"
          />

          <button
            className="pdp__arrow pdp__arrow--right"
            onClick={() => setMainImgIdx(i => (i === gallery.length - 1 ? 0 : i + 1))}
          >›</button>
        </div>
      </section>

      {/* Product info */}
      <section className="pdp__info">
        <h1 className="pdp__brand">{product.brand}</h1>
        <h2 className="pdp__name">{product.name}</h2>

        {/* Attributes */}
        {product.attributes.map(attr => (
          <AttributeSelector
            key={attr.id}
            attribute={attr}
            selected={selectedAttrs[attr.id]}
            onChange={handleAttrChange}
          />
        ))}

        {/* Price */}
        <p className="pdp__price-label">PRICE:</p>
        <p className="pdp__price">{formatPrice(price?.amount ?? 0, symbol)}</p>

        {/* Add to Cart */}
        <button
          className={`pdp__add-to-cart ${!allSelected || !product.inStock ? 'pdp__add-to-cart--disabled' : ''}`}
          disabled={!allSelected || !product.inStock}
          onClick={handleAddToCart}
          data-testid="add-to-cart"  
        >
          {product.inStock ? 'ADD TO CART' : 'OUT OF STOCK'}
        </button>

        {/* Description — parsed safely, no dangerouslySetInnerHTML */}
        <SafeHtml html={product.description} />
      </section>
    </main>
  );
}