var _ = require('lodash');

require('./constants');

/* array protos */
Array.prototype.each = function(iterator) { return _.forEach(this, iterator); };
Array.prototype.reduce = function(iterator, memo) { return _.reduce(this, iterator, memo); };
Array.prototype.pluck = function(propertyName) { return _.pluck(this, propertyName); };
Array.prototype.find = function(obj) { return _.find(this, obj); };
Array.prototype.where = function(props) { return _.where(this, props); };
Array.prototype.filter = function(predicate) { return _.filter(this, predicate); };
Array.prototype.without = function(val) { return _.without(this, val); };
Array.prototype.remove = function(val) { return _.remove(this, val); };
Array.prototype.uniq = function(isSorted){return _.uniq(this, isSorted)};
Array.prototype.shuffle = function() {
    var i = this.length, j, tempi, tempj;
    if(i == 0) return false;
    while(--i) {
        j = Math.floor(Math.random() * ( i + 1 ));
        tempi = this[i];
        tempj = this[j];
        this[i] = tempj;
        this[j] = tempi;
    }
    return this;
};

Array.prototype.has = function(val) { return this.indexOf(val) > -1; };

Array.prototype.pushRet = function(val) {
    this.push(val);
    return this;
};

Array.prototype.unshiftRet = function(val) {
    this.unshift(val);
    return this;
};

/* global utils */

// checks if number v is between a and b
global.between = function(v, a, b, inclusive) {
    var min = Math.min.apply(Math, [a, b]),
        max = Math.max.apply(Math, [a, b]);
    return inclusive ? v >= min && v <= max : v > min && v < max;
};

// executes all functions in evtArray passing data as argument
global.fireEvent = function(evtArray, data) {
    _.forEach(evtArray, function(cb) { if(typeof cb === 'function') cb.call(this, data); });
};
