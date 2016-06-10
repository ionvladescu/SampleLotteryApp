var knexDBLottery = require('knex')({
    client: 'mysql',
    connection: {
        host: process.env.DB_HOST,
        port: process.env.DB_PORT,
        user: process.env.DB_USERNAME,
        password: process.env.DB_PASSWORD,
        database: process.env.DB_DATABASE,
        charset: 'utf8',
        /* this one actually passes to the mysql module */
        typeCast: function(field, next) {
            if(field.type === 'TINY' && field.length == 1) { // TINYINT(1)
                return (field.string() == '1'); // boolean convert
            }
            //convert bigint to string, because it loses precision in JS if it is of type number.
            if(field.type === 'LONGLONG' && field.length == 20) { // BIGINT(20)
                return field.string(); // return the string value
            }
            return next();
        }
        //		debug: true
    }
});

module.exports = {
    ltr: require('bookshelf')(knexDBLottery)
};
