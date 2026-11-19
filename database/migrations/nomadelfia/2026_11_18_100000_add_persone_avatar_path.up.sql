-- Run the migrations

-- The path of the avatar of the persona (a square photo of the face) on the media_previews disk, empty when missing.
ALTER TABLE `persone` ADD COLUMN `avatar_path` varchar(255) NULL DEFAULT NULL;
