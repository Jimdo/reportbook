db = db.getSiblingDB("DATABASE");

db.getCollectionNames().forEach(function(doc) {
    db[doc].drop();
});
