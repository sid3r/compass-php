import Layout from '@/layout';

const saleRoutes = {
  path: '/sales',
  component: Layout,
  redirect: 'sales/list',
  meta: {
    permissions: ['view menu sales'],
  },
  children: [
    {
      name: 'sales',
      path: 'list',
      component: () => import('@/views/stocks/sales.vue'),
      meta: {
        title: 'sales',
        icon: 'sale',
      },
    },
    {
      name: 'viewsale',
      path: 'view/:saleId',
      component: () => import('@/views/stocks/single/sale.vue'),
      meta: { title: 'viewsale' },
      hidden: true,
    },
    {
      name: 'newsale',
      path: 'newsale',
      component: () => import('@/views/stocks/newSale.vue'),
      meta: { title: 'newsale' },
      hidden: true,
    },
  ],
};

export default saleRoutes;
