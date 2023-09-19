import request from '@/utils/request';
import Resource from '@/api/resource';

class DashboardResource extends Resource {
  constructor() {
    super('dashboard');
  }
  widgets() {
    return request({
      url: '/' + this.uri + '/widgets',
      method: 'get',
    });
  }
  recentdocs() {
    return request({
      url: '/' + this.uri + '/recentdocs',
      method: 'get',
    });
  }
}

export { DashboardResource as default };
