ALTER TABLE photos DROP PRIMARY KEY;

ALTER TABLE photos ADD COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;

ALTER TABLE photos_people ADD COLUMN new_photo_id BIGINT UNSIGNED FIRST;

UPDATE photos_people
JOIN photos ON photos_people.photo_id = photos.uid
SET photos_people.new_photo_id = photos.id;

ALTER TABLE `photos_people` DROP INDEX `photo_id`;
ALTER TABLE photos_people DROP COLUMN photo_id;
ALTER TABLE photos_people CHANGE COLUMN new_photo_id photo_id BIGINT UNSIGNED NOT NULL;

ALTER TABLE photos DROP COLUMN uid;

ALTER TABLE photos_people ADD CONSTRAINT fk_photos_people_photo_id FOREIGN KEY (photo_id) REFERENCES photos(id);

ALTER TABLE photos_people ADD UNIQUE KEY (`photo_id`,`persona_id`);
