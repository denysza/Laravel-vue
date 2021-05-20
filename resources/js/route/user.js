const apiPrefix = '/api/' + process.env.MIX_API_VER + '/user';

export const Web = {
}

export const Api = {
  entry: apiPrefix,
  login: apiPrefix + '/login',
  show: apiPrefix + '/show',
  update: apiPrefix + '/update',
  uploadImage: apiPrefix + '/upload_image',
  deleteImage: apiPrefix + '/delete_image',
  properties: apiPrefix + '/properties',
  search: apiPrefix + '/search',
  examplelist: apiPrefix + '/examplelist',
}