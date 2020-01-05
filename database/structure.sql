CREATE TABLE `bans` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_520_ci DEFAULT '',
  `byUserId` int(11) unsigned DEFAULT NULL,
  `unbannedByUserId` int(11) unsigned DEFAULT NULL,
  `reason` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `internalReason` longtext COLLATE utf8mb4_unicode_520_ci,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT NULL,
  `deletedAt` datetime DEFAULT NULL,
  `expiresAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `ip` (`ip`),
  KEY `ban_by_user` (`byUserId`),
  KEY `userId_deletedAt` (`userId`,`updatedAt`),
  KEY `ip_deletedAt` (`ip`,`deletedAt`),
  KEY `expiresAt` (`expiresAt`),
  KEY `deletedAt` (`deletedAt`),
  KEY `ban_unbanned_by_user` (`unbannedByUserId`),
  CONSTRAINT `ban_by_user` FOREIGN KEY (`byUserId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `ban_unbanned_by_user` FOREIGN KEY (`unbannedByUserId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ban_user` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


CREATE TABLE `user_sessions` (
  `id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `userId` int(11) unsigned NOT NULL,
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `adminLogin` tinyint(1) NOT NULL DEFAULT '0',
  `loggedInAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `loggedOutAt` datetime DEFAULT NULL,
  `lastUsedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`),
  KEY `userId` (`userId`),
  CONSTRAINT `user_sessions_user` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
