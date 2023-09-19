import request from '@/utils/request';
import Resource from '@/api/resource';

class ContactResource extends Resource {
  constructor() {
    super('contacts');
  }
  functions() {
    return request({
      url: '/' + this.uri + '/functions',
      method: 'get',
    });
  }
}

export { ContactResource as default };
