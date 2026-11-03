-- Run the migrations

-- The path of the photo of the persona in the "avatars" disk, shown in the detail pages of the persona.
ALTER TABLE `persone` ADD COLUMN `avatar_path` varchar(255) DEFAULT NULL;
