-- Run the migrations

-- When the avatar of the persona was saved the last time, empty for the avatars saved before this column.
ALTER TABLE `persone` ADD COLUMN `avatar_aggiornato_il` datetime NULL DEFAULT NULL AFTER `avatar_path`;
