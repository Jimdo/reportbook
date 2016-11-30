db.createCollection("users");
db.createCollection("profiles");
db.createCollection("reports");
db.createCollection("comments");
db.createCollection("sessions");

db.users.createIndex( { "email": 1 }, { unique: true } );
db.users.createIndex( { "username": 1 }, { unique: true } );
