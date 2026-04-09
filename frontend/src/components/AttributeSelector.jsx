import { toKebabCase } from '../utils/helpers';

/**
 * Renders attribute buttons on the Product Details Page.
 * Handles both text (size) and swatch (colour) types.
 */
export default function AttributeSelector({ attribute, selected, onChange }) {
  const attrKebab = toKebabCase(attribute.name);
  const isSwatch  = attribute.type === 'swatch';

  return (
    <div
      className="attr-selector"
      data-testid={`product-attribute-${attrKebab}`} 
    >
      <p className="attr-selector__label">{attribute.name.toUpperCase()}:</p>
      <div className="attr-selector__options">
        {attribute.items.map(item => {
          const isSelected = selected === item.id;

          return (
            <button
              key={item.id}
              className={[
                'attr-selector__btn',
                isSwatch ? 'attr-selector__btn--swatch' : 'attr-selector__btn--text',
                isSelected ? (isSwatch ? 'attr-selector__btn--swatch-selected' : 'attr-selector__btn--selected') : '',
              ].join(' ')}
              style={isSwatch ? { backgroundColor: item.value } : {}}
              onClick={() => onChange(attribute.id, item.id)}
              title={item.displayValue}
            >
              {isSwatch ? '' : item.displayValue}
            </button>
          );
        })}
      </div>
    </div>
  );
}