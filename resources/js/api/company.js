import request from '@/utils/request';
import Resource from '@/api/resource';

class DocResource extends Resource {
  constructor() {
    super('companies');
  }
  regions() {
    return request({
      url: '/' + this.uri + '/regions',
      method: 'get',
    });
  }
  activities() {
    return request({
      url: '/' + this.uri + '/activities',
      method: 'get',
    });
  }
}

export { DocResource as default };
