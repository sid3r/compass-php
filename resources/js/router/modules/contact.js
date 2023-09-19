/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const contactRoutes = {
  path: '/contacts',
  redirect: 'list',
  component: Layout,
  meta: {
    permissions: ['view menu contacts'],
  },
  children: [
    {
      path: 'list',
      name: 'contacts',
      component: () => import('@/views/contacts/contacts.vue'),
      meta: { title: 'contacts', icon: 'contacts' },
    },
    {
      path: 'contact/:contactId',
      component: () => import('@/views/contacts/view.vue'),
      name: 'viewcontact',
      hidden: true,
      meta: { title: 'viewcontact' },
    },
  ],
};

export default contactRoutes;
