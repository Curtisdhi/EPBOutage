//define global namespaces
if (typeof EPBOutage === 'undefined') var EPBOutage = {};

EPBOutage.darkenHexColor = function(hexColor) {
    hexColor = hexColor.substring(1);
    var cDec = parseInt('0x'+ hexColor);
    var dDec = ((cDec & 0x6E6E6E) >> 1) | (cDec & 0x808080);
    //console.log([backgroundColor, cDec, dDec, dDec.toString(16)]);

    return '#'+ dDec.toString(16);
};