import request from '@/utils/request';
import Resource from '@/api/resource';

class DocResource extends Resource {
  constructor() {
    super('documents');
  }

  latest() {
    return request({
      url: '/' + this.uri + '/latest',
      method: 'get',
    });
  }
  stats() {
    return request({
      url: '/' + this.uri + '/stats',
      method: 'get',
    });
  }
  terminate(id) {
    return request({
      url: '/' + this.uri + '/terminate/' + id,
      method: 'post',
    });
  }
  cancel(id) {
    return request({
      url: '/' + this.uri + '/cancel/' + id,
      method: 'post',
    });
  }
}

export { DocResource as default };
