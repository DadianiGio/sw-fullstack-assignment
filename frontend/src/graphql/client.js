import { GraphQLClient } from 'graphql-request';

// Point to your local PHP backend
export const client = new GraphQLClient('http://localhost/sw-backend/public/index.php');