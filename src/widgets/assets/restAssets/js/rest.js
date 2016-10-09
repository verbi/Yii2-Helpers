function Rest(data) {
    if (arguments.callee._singletonInstance) {
    return arguments.callee._singletonInstance;
  }

  arguments.callee._singletonInstance = this;
  
  this.oauth2 = new Oauth2();
  
  this.action = function(data) {
      data = this.prepareData(data);
      if(!data.headers) {
          data.headers = {};
      }
      data.headers.Authorization = "Bearer " + this.oauth2.getAjaxAccessToken();
      return $.ajax(data);
  };
  
  this.create = function(data) {
      data = this.prepareData(data);
      if(!data.type) {
          data.type = 'post';
      }
      return this.action(data);
  };
  
  this.index = function() {
      return this.action(data);
  };
  
  this.view = function(data)  {
      return this.action(data);
  };
  
  this.update = function(data) {
      data = this.prepareData(data);
      if(!data.type) {
          data.type = 'put';
      }
      return this.action(data);
  };
  
  this.delete = function(data) {
      data = this.prepareData(data);
      if(!data.type) {
          data.type = 'delete';
      }
      return this.action(data);
  };
  
  this.prepareData = function(data) {
      if(!data) {
          data = {};
      }
      return data;
  };
}