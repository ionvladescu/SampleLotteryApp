var _ = require('lodash'),
    Q = require('q');

module.exports = function(bookshelf) {

    var m = {};

    m.User = bookshelf.Model.extend({
        tableName: 'users'
    });

    m.Users = bookshelf.Collection.extend({model: m.User});

    m.Lottery = bookshelf.Model.extend({
        tableName: 'lotteries'
    });

    m.Lotteries = bookshelf.Collection.extend({model: m.Lottery});

    m.UserLottery = bookshelf.Model.extend({
        tableName: 'users_lotteries'
    });

    m.UsersLotteries = bookshelf.Collection.extend({model: m.UserLottery});

    return m;

};