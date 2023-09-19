import request from '@/utils/request';
import Resource from '@/api/resource';

class StatResource extends Resource {
  constructor() {
    super('stats');
  }

  purchases(query) {
    return request({
      url: '/' + this.uri + '/purchases',
      method: 'get',
      params: query,
    });
  }
  sales(query) {
    return request({
      url: '/' + this.uri + '/sales',
      method: 'get',
      params: query,
    });
  }
  stores() {
    return request({
      url: '/' + this.uri + '/stores',
      method: 'get',
    });
  }
}

export { StatResource as default };
