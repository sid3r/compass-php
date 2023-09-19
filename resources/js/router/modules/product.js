import Layout from '@/layout';

const productsRoutes = {
  path: '/products',
  component: Layout,
  redirect: 'list',
  meta: {
    permissions: ['view menu products'],
  },
  children: [
    {
      name: 'products',
      path: 'list',
      component: () => import('@/views/products/products.vue'),
      meta: {
        title: 'products',
        icon: 'package',
      },
    },
    {
      path: 'categories',
      hidden: true,
      component: () => import('@/views/products/categories.vue'),
      name: 'categories',
      meta: { title: 'categories' },
    },
    {
      path: 'new',
      hidden: true,
      component: () => import('@/views/products/new.vue'),
      name: 'newproduct',
      meta: { title: 'newproduct' },
    },
    {
      path: 'view/:productId',
      hidden: true,
      component: () => import('@/views/products/view.vue'),
      name: 'viewproduct',
      meta: { title: 'viewproduct' },
    },
    {
      path: 'edit/:productId',
      hidden: true,
      component: () => import('@/views/products/new.vue'),
      name: 'editproduct',
      meta: { title: 'editproduct' },
    },
  ],
};

export default productsRoutes;
