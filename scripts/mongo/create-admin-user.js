db.createUser(
  {
    user: "admin",
    pwd: "PASSWORD",
    roles: [
      {
        role: "userAdminAnyDatabase",
        db: "admin"
      }
    ],
    mechanisms: [
      "SCRAM-SHA-1"
    ]
  }
);
