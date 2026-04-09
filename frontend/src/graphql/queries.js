import { gql } from 'graphql-request';

export const GET_CATEGORIES_AND_PRODUCTS = gql`
  query GetCategoriesAndProducts($category: String) {
    categories {
      name
    }
    products(category: $category) {
      id
      name
      inStock
      gallery
      brand
      prices {
        amount
        currency { label symbol }
      }
      attributes {
        id
        name
        type
        items { id displayValue value }
      }
    }
  }
`;

export const GET_PRODUCT = gql`
  query GetProduct($id: String!) {
    product(id: $id) {
      id
      name
      inStock
      gallery
      description
      brand
      prices {
        amount
        currency { label symbol }
      }
      attributes {
        id
        name
        type
        items { id displayValue value }
      }
    }
  }
`;

export const PLACE_ORDER = gql`
  mutation PlaceOrder($items: [OrderItemInput!]!) {
    placeOrder(items: $items) {
      orderId
      success
    }
  }
`;