function RestListView(data) {
  this.rest = new Rest();
  
  this.ajaxOptions = null;
  if(data.ajaxOptions) {
      this.ajaxOptions = data.ajaxOptions;
  }
  
  this.itemView = null;
  if(data.itemView) {
    this.itemView = data.itemView;
  }
  
  this.reload = function() {
      
  };
  
  this.load = function(data) {
      
  };
  
  
}