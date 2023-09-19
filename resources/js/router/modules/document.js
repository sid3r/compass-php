/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const documentRoutes = {
  path: '/documents',
  component: Layout,
  redirect: 'list',
  meta: {
    permissions: ['view menu documents'],
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/documents/documents.vue'),
      name: 'documents',
      meta: {
        title: 'documents',
        icon: 'layers',
      },
      // hidden: true,
    },
    {
      path: 'view/:docId',
      component: () => import('@/views/documents/view.vue'),
      name: 'viewdocument',
      hidden: true,
      meta: { title: 'viewdocument' },
    },
    {
      path: 'edit/:docId',
      component: () => import('@/views/documents/edit.vue'),
      name: 'editdocument',
      hidden: true,
      meta: { title: 'editdocument' },
    },
    {
      path: 'new',
      component: () => import('@/views/documents/new.vue'),
      name: 'newdocument',
      hidden: true,
      meta: { title: 'newdocument' },
    },
  ],
};

export default documentRoutes;
