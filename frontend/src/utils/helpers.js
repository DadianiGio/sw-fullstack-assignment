/** "With USB 3 ports" → "with-usb-3-ports" */
export const toKebabCase = (str) =>
  str.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');

/** Format price with 2 decimal places and currency symbol */
export const formatPrice = (amount, symbol = '$') =>
  `${symbol}${amount.toFixed(2)}`;