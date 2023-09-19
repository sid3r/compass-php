/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const stocksRoutes = {
  path: '/stocks',
  component: Layout,
  redirect: 'list',
  meta: {
    permissions: ['view menu stocks'],
  },
  children: [
    {
      name: 'stocks',
      path: 'list',
      component: () => import('@/views/stocks/index.vue'),
      meta: {
        title: 'stocks',
        icon: 'percent',
      },
    },
  ],
};

export default stocksRoutes;
