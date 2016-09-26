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
