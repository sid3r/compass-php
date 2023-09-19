import Layout from '@/layout';

const purchaseRoutes = {
  path: '/purchases',
  component: Layout,
  redirect: 'purchases/list',
  meta: {
    permissions: ['view menu purchases'],
  },
  children: [
    {
      name: 'purchases',
      path: 'list',
      component: () => import('@/views/stocks/purchases.vue'),
      meta: { title: 'purchases',
        icon: 'purchase',
      },
    },
    {
      name: 'viewpurchase',
      path: 'view/:purchaseId',
      component: () => import('@/views/stocks/single/purchase.vue'),
      meta: { title: 'viewpurchase' },
      hidden: true,
    },
    {
      name: 'newpurchase',
      path: 'newpurchase',
      component: () => import('@/views/stocks/newPurchase.vue'),
      meta: { title: 'newpurchase' },
      hidden: true,
    },
  ],
};

export default purchaseRoutes;
