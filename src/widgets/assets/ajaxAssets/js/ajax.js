function ajax(data) {
    var oauth2 = new Oauth2();
    if(!data.headers) {
          data.headers = {};
    }
    data.headers.Authorization = "Bearer " + oauth2.getAjaxAccessToken();
    return $.ajax(data);
}