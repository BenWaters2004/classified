USE dbs;

CREATE USER 'classified'@'%' IDENTIFIED BY 'devpasswd';
GRANT ALL PRIVILEGES ON dbs.* TO 'classified'@'%';
FLUSH PRIVILEGES;

INSERT INTO `users` (`id`, `userType`, `dbsApplication`, `bpssApplication`, `weApplication`, `email`, `organisationID`, `password`, `remember_token`, `recoveryCode`, `recoveryCodeExpiry`, `title`, `firstName`, `lastName`, `position`, `phoneNumber`, `userStatus`, `lastLogin`, `failedAttempts`, `lastAttemptTime`, `lockExpire`, `to_purge`, `purge_date`, `createdOn`, `createdBy`, `modifiedOn`, `modifiedBy`, `deleted`, `deletedOn`, `deletedBy`) VALUES
(2, 'admin', 1, 0, 0, 'testAdmin1@mailhog.local', 1, '$2y$10$0hSijPhFgnDM7HQzG7OguOkvXa/KBU5bKQt/dbe34Ak3yOdPboiNa', 'aFfNMSyAQWXlWN2NdqR5zKFvdBf1E2ydFxS476WRJFloiBgTQ4OC8vFC0W8b', NULL, NULL, 'Mr', 'Admin', 'User', 'testFName', 'Software Developer', 1, '2021-07-21 12:12:00', 0, '2021-07-21 11:42:00', NULL, 0, NULL, '2018-04-08 00:00:00', 1, NULL, NULL, 0, NULL, NULL);
-- pwd = g8eAOKh0gBIO5YfkG84u ;

INSERT INTO user_roles (id, userID, role) values (2, 2, "superuser");

INSERT INTO organisations (id, organisationName, organisationStatus, organisationEmail) values (1, "testOrg1", 1, "testOrg1@mailhog.local");
