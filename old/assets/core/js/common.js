//define global namespaces
if (typeof EPBOutage === 'undefined') var EPBOutage = {};

String.prototype.hashCode = function() {
  var hash = 0, i, chr;
  if (this.length === 0) return hash;
  for (i = 0; i < this.length; i++) {
    chr   = this.charCodeAt(i);
    hash  = ((hash << 5) - hash) + chr;
    hash |= 0; // Convert to 32bit integer
  }
  return hash;
};

EPBOutage.darkenHexColor = function(hexColor) {
    hexColor = hexColor.substring(1);
    var cDec = parseInt('0x'+ hexColor);
    var dDec = ((cDec & 0x6E6E6E) >> 1) | (cDec & 0x808080);
    //console.log([backgroundColor, cDec, dDec, dDec.toString(16)]);

    return '#'+ dDec.toString(16);
};