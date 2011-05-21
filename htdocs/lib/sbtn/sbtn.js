function sbtn(i) {
  this.i = i;
  this.click = function() {
    if( $(i).attr("value") == "on") {
      $(i).css("background-position", "40px 0px");
      $(i).attr( "value", "off" );
    }
    else {
      $(i).css("background-position", "0px 0px");
      $(i).attr( "value", "on" );
    }
  }
}
