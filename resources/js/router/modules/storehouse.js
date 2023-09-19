
import Layout from '@/layout';

const storeRoutes = {
  path: '/storehouses',
  component: Layout,
  redirect: '/storehouses/list',
  meta: {
    permissions: ['view menu storehouses'],
  },
  children: [
    {
      name: 'storehouses',
      path: 'list',
      component: () => import('@/views/storehouses/list.vue'),
      meta: {
        title: 'storehouses',
        icon: 'map',
      },
      // hidden: true,
    },
  ],
};

export default storeRoutes;
