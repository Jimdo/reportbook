db = db.getSiblingDB("DATABASE");

db.createUser(
  {
    user: "USERNAME",
    pwd: "PASSWORD",
    roles: [
      {
        role: "readWrite",
        db: "DATABASE"
      }
    ]
  }
);
