import request from '@/utils/request';
import Resource from '@/api/resource';

class CategoryResource extends Resource {
  constructor() {
    super('categories');
  }
  reorder(resource) {
    return request({
      url: '/' + this.uri + '/reorder',
      method: 'post',
      data: resource,
    });
  }
}

export { CategoryResource as default };
