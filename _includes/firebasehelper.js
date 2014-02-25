/* A helper class */

function FBH(firebaseref) {
    this.base = firebaseref;
}

// Puts protos
var FBH_add = function(protoname, protoval) {
    FBH.prototype[protoname] = protoval;
};
FBH_add("set", function(data, callback) {
    this.base.set(data, callback);
});
FBH_add("getValue", function(key) {
    alert(this.base);
});

// End

var data_store_version = "4";
var firebase_set_success = function(error) {
    var text = '';
    if (error) {
        text = 'Error connecting.';
    } else {
        text = 'Success.';
    }
    var newText = document.createTextNode(text + ' ' + firebase_child);
    document.getElementById('fb-check').appendChild(newText);
};
var firebase_url = 'https://incandescent-fire-7012.firebaseio.com/';
var firebase_base = new Firebase(firebase_url);
var firebase_connected_ips = firebase_base.child('connection-history');
var firebase_child = firebase_base.push();
firebase_child.onDisconnect().remove();
window.onload = function() {
    var date = new Date();
    window.firebase_ip_child = firebase_connected_ips.child('ip-addr-' + (
        codehelper_ip.IP.replace(/\./g, '-')) + "; v" + data_store_version);
    firebase_child.child('connect-start').set(date.getTime(),
        firebase_set_success);
    firebase_child.child('version').set(data_store_version);
    window.firebase_ip_child.child('data').set(codehelper_ip);
    window.firebase_ip_child.child('status').set('Online');
    window.firebase_ip_child.child('status').onDisconnect().set('Offline');
    window.refsforall = {};
    refsforall["base"] = new FBH(firebase_base);
    refsforall["connected"] = new FBH(firebase_connected_ips);
    refsforall["ipchild"] = new FBH(window.firebase_ip_child);
    refsforall["child"] = new FBH(firebase_child);
};