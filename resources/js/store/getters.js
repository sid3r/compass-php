const getters = {
  sidebar: state => state.app.sidebar,
  language: state => state.app.language,
  size: state => state.app.size,
  device: state => state.app.device,
  userId: state => state.user.id,
  token: state => state.user.token,
  name: state => state.user.name,
  user: state => state.user.user,
  config: state => state.user.config,
  roles: state => state.user.roles,
  permissions: state => state.user.permissions,
  permissionRoutes: state => state.permission.routes,
  addRoutes: state => state.permission.addRoutes,
};
export default getters;
