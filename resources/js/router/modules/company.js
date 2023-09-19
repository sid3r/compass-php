/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const companyRoutes = {
  path: '/companies',
  redirect: 'list',
  component: Layout,
  meta: {
    permissions: ['view menu companies'],
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/companies/companies.vue'),
      name: 'companies',
      meta: { title: 'companies', icon: 'company' },
    },
    {
      path: 'company/:companyId',
      component: () => import('@/views/companies/view.vue'),
      name: 'viewcompany',
      hidden: true,
      meta: { title: 'viewcompany' },
    },
  ],
};

export default companyRoutes;
